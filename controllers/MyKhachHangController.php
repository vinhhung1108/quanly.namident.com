<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use app\models\MyKhachHang;
use app\models\MyDieuTri;
use app\models\AppointmentForm;
use app\models\MyKhachHangSearch;
use yii\filters\AccessControl;
use yii\web\Response;

class MyKhachHangController extends Controller
{
    public function behaviors()
    {
      return [
        'access' => [
          'class' => AccessControl::className(),
          'only' => ['index','update','create','delete','view','appointments','appointment-modal','receipt','receipt-preview'],
          'rules' => [                   
            [
              'allow' => false,
              'controllers' => ['my-khach-hang'],
              'roles' => ['?'],
            ],
            [
              'allow' => true,
              'actions' => ['index','create','view','update','appointments','appointment-modal','receipt','receipt-preview'],
              'roles' => ['admin','author'],
            ],
            [
              'allow' => true,
              'actions'=> ['delete'],
              'roles'=>['admin'],
            ],

          ],
        ],
        'verbs' => [
            'class' => VerbFilter::className(),
            'actions' => [
                'delete' => ['POST'],
            ],
        ],
      ];
    }

    public function actionIndex()
    {
        $searchModel = new MyKhachHangSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        $dieuTriProvider = new ActiveDataProvider([
            'query' => MyDieuTri::find()
              ->where(['id_kh' => $id])
              ->orderBy(['ngay_dieu_tri' => SORT_DESC, 'id' => SORT_DESC])
              ->with(['bacSi', 'dvItems.bacSi', 'dvItems.dichVu']),
            'pagination' => ['pageSize' => 10],
        ]);

        return $this->render('view', [
            'model' => $model,
            'dieuTriProvider' => $dieuTriProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new MyKhachHang();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Đã tạo khách hàng.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Đã cập nhật.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', ['model' => $model]);
    }

    public function actionAppointments()
    {
        $request = \Yii::$app->request;

        // bộ lọc đơn giản qua GET
        $from = $request->get('from');
        $to   = $request->get('to');
        $q    = trim((string)$request->get('q', ''));

        // mặc định: hôm nay → 30 ngày tới
        if (empty($from)) $from = date('Y-m-d');
        if (empty($to))   $to   = date('Y-m-d', strtotime('+30 days'));

        $query = \app\models\MyKhachHang::find()
            ->andWhere(['not', ['ngay_hen' => null]])
            ->andWhere(['between', 'ngay_hen', $from, $to])
            ->orderBy(['ngay_hen' => SORT_ASC, 'gio_hen' => SORT_ASC, 'id' => SORT_ASC]);

        if ($q !== '') {
            // tìm theo họ tên / SĐT / bác sĩ (text) / mã thẻ
            $query->andFilterWhere(['or',
                ['like', 'ho_ten', $q],
                ['like', 'sdt', $q],
                ['like', 'bs_dieu_tri', $q],
                ['like', 'ma_the', $q],
            ]);
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('appointments', [
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to'   => $to,
            'q'    => $q,
        ]);
    }

    //Form đặt lịch hẹn cho khách hàng
    public function actionAppointmentModal($id)
    {
        $kh = MyKhachHang::findOne((int)$id);
        if (!$kh) throw new NotFoundHttpException('Không tìm thấy khách hàng.');

         // --- XÓA RỖNG LỊCH HẸN ---
        if (Yii::$app->request->isPost && Yii::$app->request->post('clear') === '1') {
            $kh->ngay_hen = null;
            $kh->gio_hen = null;
            $kh->bs_dieu_tri = null;
            $kh->noi_dung_hen = null;
            $kh->save(false);

            Yii::$app->response->format = Response::FORMAT_JSON;
            $html = $this->renderPartial('_appointment_box', ['model' => $kh]);
            return ['ok' => true, 'html' => $html, 'message' => 'Đã xoá lịch hẹn.'];
        }
        
        $model = new AppointmentForm();
        $model->loadFromKhachHang($kh);

        // Prefill khi mở lần đầu
        if (\Yii::$app->request->isGet && empty($model->ngay_hen)) {
            $model->ngay_hen = date('Y-m-d');
            $model->gio_hen  = date('H:i');
        }

        // Submit AJAX
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $kh->refresh();
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $html = $this->renderPartial('_appointment_box', ['model' => $kh]);
            return ['ok' => true, 'html' => $html, 'message' => 'Đã lưu lịch hẹn.'];
        }

        // Trả về HTML form để nhúng vào modal
        return $this->renderAjax('_appointment_form', [
            'model' => $model,
            'kh'    => $kh,
        ]);
    }


    public function actionDelete($id)
    {
        $model = MyKhachHang::findOne($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException('Không tìm thấy khách hàng.');
        }

        try {
            $ok = $model->delete(); // sẽ trả về false nếu bị chặn ở beforeDelete()
            if ($ok === false) {
                // Đã có flash từ beforeDelete(); chỉ cần quay lại
                return $this->redirect(['view', 'id' => $model->id]);
            }

            Yii::$app->session->setFlash('success', 'Đã xoá khách hàng.');
            return $this->redirect(['index']);

        } catch (\yii\db\IntegrityException $e) {
            // Phòng khi có nơi khác xoá trực tiếp bỏ qua beforeDelete()
            $count = (int)$model->getDieuTris()->count();
            Yii::$app->session->setFlash('warning',
                "Không thể xoá khách hàng vì còn {$count} lần điều trị."
            );
            return $this->redirect(['view', 'id' => $model->id]);

        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', 'Có lỗi khi xoá: ' . $e->getMessage());
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }

    protected function findModel($id): MyKhachHang
    {
        $m = MyKhachHang::findOne($id);
        if (!$m) throw new NotFoundHttpException('Không tìm thấy khách hàng.');
        return $m;
    }

    /** Chọn lịch sử điều trị để in phiếu thu */
    public function actionReceipt($id)
    {
        $kh = MyKhachHang::findOne($id);
        if (!$kh) throw new NotFoundHttpException('Không tìm thấy khách hàng.');

        $rows = MyDieuTri::find()
            ->where(['id_kh' => $kh->id])
            ->orderBy(['ngay_dieu_tri' => SORT_ASC, 'id' => SORT_ASC])
            ->with(['dvItems.dichVu', 'bacSi'])
            ->all();

        return $this->render('receipt_select', compact('kh', 'rows'));
    }

    /** Xem trước & in phiếu thu */
    public function actionReceiptPreview($id)
    {
        $kh = MyKhachHang::findOne($id);
        if (!$kh) throw new NotFoundHttpException('Không tìm thấy khách hàng.');

        $dtIds = (array)Yii::$app->request->post('dt_ids', []);
        if (empty($dtIds)) {
            Yii::$app->session->setFlash('warning', 'Vui lòng chọn ít nhất 1 dòng điều trị.');
            return $this->redirect(['receipt', 'id' => $kh->id]);
        }

        $rows = MyDieuTri::find()
            ->where(['id_kh' => $kh->id])
            ->andWhere(['id' => $dtIds])
            ->orderBy(['ngay_dieu_tri' => SORT_ASC, 'id' => SORT_ASC])
            ->with(['dvItems.dichVu', 'bacSi'])
            ->all();

        $tong_phi   = (int)array_sum(array_map(fn($r)=> (int)$r->phi, $rows));
        $thanh_toan = (int)array_sum(array_map(fn($r)=> (int)$r->tam_thu, $rows));
        $con_lai    = $tong_phi - $thanh_toan;

        $today = new \DateTime('now', new \DateTimeZone('Asia/Ho_Chi_Minh'));

        // Lấy thông tin phòng khám nếu có (tùy dự án)
        $nhaKhoa = null;
        if (class_exists('\app\models\NhaKhoa')) {
            $nhaKhoa = \app\models\NhaKhoa::find()->one();
        }

        // Đọc số tiền bằng chữ (dùng Intl nếu có)
        $soTienBangChu = $this->toVietnameseWords($thanh_toan) . ' đồng';

        return $this->render('receipt_preview', compact(
            'kh','rows','nhaKhoa','today','tong_phi','thanh_toan','con_lai','soTienBangChu'
        ));
    }

    /** Đổi số thành chữ tiếng Việt (dùng Intl nếu có) */
    private function toVietnameseWords(int $n): string
    {
        if (class_exists('\NumberFormatter')) {
            $fmt = new \NumberFormatter('vi_VN', \NumberFormatter::SPELLOUT);
            $txt = $fmt->format($n);
            // Viết hoa chữ cái đầu
            return mb_convert_case($txt, MB_CASE_TITLE, 'UTF-8');
        }
        // Fallback đơn giản
        return number_format($n, 0, ',', '.');
    }

    public function actionAllImages($id)
    {
        $kh = \app\models\MyKhachHang::findOne((int)$id);
        if (!$kh) throw new \yii\web\NotFoundHttpException('Không tìm thấy KH.');

        $rows = \app\models\MyDieuTri::find()
            ->with('images')
            ->where(['id_kh' => (int)$kh->id])
            ->orderBy(['ngay_dieu_tri' => SORT_DESC, 'id' => SORT_DESC])
            ->all();

        return $this->renderAjax('all-images', [
            'kh'   => $kh,
            'rows' => $rows,
        ]);
    }

}
