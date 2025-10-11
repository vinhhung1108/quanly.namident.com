<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use app\models\MyKhachHang;
use app\models\MyDieuTri;
use app\models\MyDieuTriImage;

class MyDieuTriController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index','create','view','update','delete','index-limit','delete-image'],
                'rules' => [
                    [
                        'allow' => false,
                        'controllers' => ['my-dieu-tri'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index','create','view','update','delete','index-limit','delete-image'],
                        'roles' => ['admin','author'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'delete-image' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Danh sách điều trị (có thể lọc theo khách hàng).
     */
    public function actionIndex($khId = null)
    {
        $searchModel = new \app\models\MyDieuTriSearch([
            'fixed_khId' => $khId ? (int)$khId : null,
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
            'khId'         => $khId,
        ]);
    }

    /**
     * Xem chi tiết điều trị.
     */
    public function actionView($id)
    {
        $model = \app\models\MyDieuTri::find()
            ->with(['dvItems', 'images', 'bacSi', 'khachHang'])
            ->where(['id' => $id])
            ->one();
        if (!$model) throw new \yii\web\NotFoundHttpException('Không tìm thấy điều trị.');
        return $this->render('view', ['model' => $model]);
    }

    public function actionViewImage($id)
    {
        $model = \app\models\MyDieuTri::find()
            ->with(['dvItems', 'images', 'bacSi', 'khachHang'])
            ->where(['id' => $id])
            ->one();
        if (!$model) throw new \yii\web\NotFoundHttpException('Không tìm thấy điều trị.');
        return $this->renderAjax('view-image', ['model' => $model]);
    }
    /**
     * Tạo điều trị mới (bắt buộc có $khId).
     */
    public function actionCreate($khId)
    {
        $kh = MyKhachHang::findOne($khId);
        if (!$kh) {
            throw new NotFoundHttpException('Không tìm thấy khách hàng.');
        }

        $model = new MyDieuTri();
        $model->id_kh = (int)$kh->id;

        $imagesForm = new MyDieuTriImage();

        // Lưu
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \app\models\MyDieuTriDv::syncFromPost((int)$model->id, Yii::$app->request->post());

            // Upload ảnh (nếu có). Lỗi ảnh không chặn lưu điều trị
            if (!$imagesForm->uploadAndSave($model)) {
                Yii::$app->session->setFlash('error', 'Lưu ảnh thất bại.');
            }

            Yii::$app->session->setFlash('success', 'Đã thêm điều trị.');
            // Trả về trang KH để thấy bảng tổng cập nhật
            return $this->redirect(['/my-khach-hang/view', 'id' => $khId]);
        }

        // === INLINE / AJAX: trả về _form để nhúng vào trang KH ===
        $inline = Yii::$app->request->get('inline');            // ?inline=1
        if ($inline || Yii::$app->request->isAjax) {
            return $this->renderAjax('_form', [
                'model'      => $model,
                'kh'         => $kh,
                'imagesForm' => $imagesForm,
            ]);
        }

        // Trường hợp mở trang tạo điều trị độc lập (không inline)
        $dieuTriProvider = new \yii\data\ActiveDataProvider([
            'query' => MyDieuTri::find()->where(['id_kh' => $khId])->orderBy(['ngay_dieu_tri' => SORT_DESC, 'id' => SORT_DESC]),
            'pagination' => ['pageSize' => 10],
        ]);

        return $this->render('create', [
            'model'            => $model,
            'kh'               => $kh,
            'imagesForm'       => $imagesForm,
            'dieuTriProvider'  => $dieuTriProvider,
        ]);
    }

    /**
     * Cập nhật điều trị.
     * Lưu ý: id_kh không đổi trong form update theo yêu cầu của bạn.
     */
    public function actionUpdate($id)
    {
        $model = $this->findMyDieuTri($id);
        $kh = $model->khachHang ?? MyKhachHang::findOne($model->id_kh);
        if (!$kh) {
            throw new NotFoundHttpException('Không tìm thấy khách hàng.');
        }

        $imagesForm = new MyDieuTriImage();

        // Lưu form
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \app\models\MyDieuTriDv::syncFromPost((int)$model->id, Yii::$app->request->post());

            // Upload ảnh mới (nếu có) - lỗi ảnh không chặn lưu điều trị
            if (!$imagesForm->uploadAndSave($model)) {
                Yii::$app->session->setFlash('error', 'Lưu ảnh thất bại.');
            }

            Yii::$app->session->setFlash('success', 'Đã cập nhật điều trị.');
            // Về trang khách hàng để thấy tổng đã được cập nhật
            return $this->redirect(['/my-khach-hang/view', 'id' => (int)$model->id_kh]);
        }

        // === INLINE / AJAX: trả về _form để nhúng vào trang KH ===
        $inline = Yii::$app->request->get('inline');
        if ($inline || Yii::$app->request->isAjax) {
            return $this->renderAjax('_form', [
                'model'      => $model,
                'kh'         => $kh,
                'imagesForm' => $imagesForm,
            ]);
        }

        // Trang update độc lập (không inline)
        $dieuTriProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\models\MyDieuTri::find()
                ->where(['id_kh' => (int)$kh->id])
                ->orderBy(['ngay_dieu_tri' => SORT_DESC, 'id' => SORT_DESC]),
            'pagination' => ['pageSize' => 10],
        ]);

        return $this->render('update', [
            'model'            => $model,
            'kh'               => $kh,
            'imagesForm'       => $imagesForm,
            'dieuTriProvider'  => $dieuTriProvider,
        ]);
    }


    /**
     * Xoá điều trị.
     */
    public function actionDelete($id)
    {
        $model = $this->findMyDieuTri($id);
        $khId = (int)$model->id_kh;

        $model->delete(); // afterDelete() trong MyDieuTri sẽ tự recalc tổng

        Yii::$app->session->setFlash('success', 'Đã xoá điều trị.');
        // Về trang khách hàng để thấy tổng đã được cập nhật
        return $this->redirect(['my-khach-hang/view', 'id' => $khId]);
    }

    /**
     * Xoá một ảnh đính kèm.
     */
    public function actionDeleteImage($id)
    {
        $img = MyDieuTriImage::findOne($id);
        if (!$img) {
            throw new NotFoundHttpException('Không tìm thấy ảnh.');
        }

        $dt = $img->myDieuTri;
        $abs = Yii::getAlias('@webroot') . $img->file_path;
        if (is_file($abs)) {
            @unlink($abs);
        }
        $img->delete();

        Yii::$app->session->setFlash('success', 'Đã xoá ảnh.');
        return $this->redirect(['my-khach-hang/view', 'id' => $dt->id_kh]);
    }

    /**
     * Helper: tìm điều trị theo ID.
     */
    protected function findMyDieuTri($id): MyDieuTri
    {
        $m = MyDieuTri::findOne($id);
        if (!$m) {
            throw new NotFoundHttpException('Không tìm thấy điều trị.');
        }
        return $m;
    }
}
