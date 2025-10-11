<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use app\models\BacSi;
use app\models\BacSiSearch;
use app\models\MyDieuTri;
use app\models\BacSiHoaHong;

class BacSiController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class'  => AccessControl::class,
                'only'   => ['index','view','create','update','delete'],
                'rules'  => [
                    [
                        'allow' => true,
                        'roles' => ['admin','author'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /** Danh sách */
    public function actionIndex()
    {
        if (class_exists(BacSiSearch::class)) {
            $searchModel  = new BacSiSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            return $this->render('index', compact('dataProvider','searchModel'));
        }

        $dataProvider = new ActiveDataProvider([
            'query' => BacSi::find()->orderBy(['ho_ten'=>SORT_ASC]),
            'pagination' => ['pageSize' => 20],
        ]);
        return $this->render('index', compact('dataProvider'));
    }

    /**
     * View + TÍNH LƯƠNG (theo tạm thu từng DV)
     * GET ?from=YYYY-MM-DD&to=YYYY-MM-DD (mặc định: tháng hiện tại)
     */
    public function actionView($id)
    {
        $bs = $this->findModel($id);

        // Khoảng thời gian
        $from = Yii::$app->request->get('from');
        $to   = Yii::$app->request->get('to');
        if (!$from || !$to) {
            $first = new \DateTime('first day of this month');
            $last  = new \DateTime('last day of this month');
            $from = $from ?: $first->format('Y-m-d');
            $to   = $to   ?: $last->format('Y-m-d');
        }

        // Tính lương = Σ(tam_thu_dv * % hoa hồng áp cho DV)
        $calc = $bs->tinhLuongTheoDichVu($from, $to, true, true);

        // Danh sách điều trị liên quan tới bác sĩ trong kỳ
        $dtIdsMap = [];
        foreach (($calc['items'] ?? []) as $item) {
            $dtId = (int)($item['dieu_tri_id'] ?? 0);
            if ($dtId > 0) $dtIdsMap[$dtId] = true;
        }
        $dtIds = array_keys($dtIdsMap);

        $rowsQuery = MyDieuTri::find()
            ->alias('dt')
            ->andWhere(['between', 'dt.ngay_dieu_tri', $from, $to]);

        $or = ['or', ['dt.bs_id' => (int)$bs->id]];
        if (!empty($dtIds)) $or[] = ['dt.id' => $dtIds];

        $rowsQuery->andWhere($or)
                  ->with(['dvItems.dichVu'])
                  ->orderBy(['dt.ngay_dieu_tri' => SORT_DESC, 'dt.id' => SORT_DESC]);

        $rowsProvider = new ActiveDataProvider([
            'query' => $rowsQuery,
            'pagination' => ['pageSize' => 20],
        ]);

        $hoaHongProvider = new ActiveDataProvider([
            'query' => BacSiHoaHong::find()
                ->where(['bs_id' => (int)$bs->id])
                ->with('dichVu')
                ->orderBy(['dv_id' => SORT_ASC]),
            'pagination' => ['pageSize' => 50],
        ]);

        $printMode = (bool)Yii::$app->request->get('print', false);
        if ($printMode) {
            $rowsProvider->pagination = false;
            $rowsProvider->setSort(false);
        }

        return $this->render('view', [
            'model'            => $bs,
            'from'             => $from,
            'to'               => $to,

            // Tổng hợp theo tạm thu
            'sumTamThu'        => (int)$calc['tong_tam_thu'],
            'luongCoDinh'      => (int)$calc['luong_co_dinh'],
            'tyLe'             => null, // mỗi DV 1 tỷ lệ khác nhau
            'luongKinhDoanh'   => (int)$calc['luong_kinh_doanh'],
            'tongLuong'        => (int)$calc['tong_luong'],

            'rowsProvider'     => $rowsProvider,
            'hoaHongProvider'  => $hoaHongProvider,

            // Không dùng tổng thành tiền nữa (đã chuyển sang tạm thu theo DV)
            'tongThanhTienDv'  => null,

            'print'            => $printMode,
        ]);
    }

    /** Tạo */
    public function actionCreate()
    {
        $model = new BacSi();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success','Đã thêm bác sĩ.');
            return $this->redirect(['view','id'=>$model->id]);
        }
        return $this->render('create', compact('model'));
    }

    /** Sửa */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success','Đã cập nhật.');
            return $this->redirect(['view','id'=>$model->id]);
        }
        return $this->render('update', compact('model'));
    }

    /** Xoá */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success','Đã xoá.');
        return $this->redirect(['index']);
    }

    /** Helper */
    protected function findModel($id): BacSi
    {
        $m = BacSi::findOne($id);
        if (!$m) throw new NotFoundHttpException('Không tìm thấy bác sĩ.');
        return $m;
    }
}
