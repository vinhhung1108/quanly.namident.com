<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use app\models\BacSi;
use app\models\BacSiHoaHong;
use app\models\DichVu;

class BacSiHoaHongController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only'  => ['create','update','delete'],
                'rules' => [
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

    public function actionCreate($bsId)
    {
        $bacSi = $this->findBacSi($bsId);
        $model = new BacSiHoaHong();
        $model->bs_id = $bacSi->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Đã thêm cấu hình hoa hồng.');
            return $this->redirect(['bac-si/view', 'id' => $bacSi->id]);
        }

        return $this->renderAjax('create', [
            'model'         => $model,
            'bacSi'         => $bacSi,
            'dichVuOptions' => $this->buildDichVuOptions($bacSi->id, null),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $bacSi = $model->bacSi;
        if (!$bacSi) {
            throw new NotFoundHttpException('Không tìm thấy bác sĩ liên quan.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Đã cập nhật tỷ lệ hoa hồng.');
            return $this->redirect(['bac-si/view', 'id' => $bacSi->id]);
        }

        return $this->render('update', [
            'model'         => $model,
            'bacSi'         => $bacSi,
            'dichVuOptions' => $this->buildDichVuOptions($bacSi->id, (int)$model->dv_id),
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $bsId  = (int)$model->bs_id;
        $model->delete();

        Yii::$app->session->setFlash('success', 'Đã xoá cấu hình hoa hồng.');
        return $this->redirect(['bac-si/view', 'id' => $bsId]);
    }

    protected function findModel($id): BacSiHoaHong
    {
        $m = BacSiHoaHong::findOne($id);
        if (!$m) {
            throw new NotFoundHttpException('Không tìm thấy cấu hình hoa hồng.');
        }
        return $m;
    }

    protected function findBacSi($id): BacSi
    {
        $m = BacSi::findOne($id);
        if (!$m) {
            throw new NotFoundHttpException('Không tìm thấy bác sĩ.');
        }
        return $m;
    }

    /**
     * Lấy danh sách dịch vụ dạng [id => tên], loại bỏ những dịch vụ bác sĩ đã cấu hình.
     * $currentDvId: cho phép giữ lại dịch vụ hiện tại khi update.
     */
    protected function buildDichVuOptions(int $bsId, ?int $currentDvId): array
    {
        $assigned = BacSiHoaHong::find()
            ->select('dv_id')
            ->where(['bs_id' => $bsId])
            ->column();

        if ($currentDvId) {
            $assigned = array_values(array_diff($assigned, [$currentDvId]));
        }

        $query = DichVu::find()->orderBy(['ten' => SORT_ASC]);
        if ($assigned) {
            $query->andWhere(['not in', 'id', $assigned]);
        }

        $rows = $query->all();
        return ArrayHelper::map($rows, 'id', 'ten');
    }
}
