<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\DichVu;
use app\models\DichVuSearch;

class DichVuController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index','create','update','view','delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin','author'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [ 'delete' => ['POST'] ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel  = new DichVuSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', compact('searchModel','dataProvider'));
    }

    public function actionView($id)
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionCreate()
    {
        $model = new DichVu();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success','Đã thêm dịch vụ.');
            return $this->redirect(['view','id'=>$model->id]);
        }
        return $this->render('create', compact('model'));
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success','Đã cập nhật dịch vụ.');
            return $this->redirect(['view','id'=>$model->id]);
        }
        return $this->render('update', compact('model'));
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        // (tuỳ chọn) kiểm tra MyDieuTriDv có đang tham chiếu đến dịch vụ này không
        $model->delete();
        Yii::$app->session->setFlash('success','Đã xoá dịch vụ.');
        return $this->redirect(['index']);
    }

    protected function findModel($id): DichVu
    {
        $m = DichVu::findOne($id);
        if (!$m) throw new NotFoundHttpException('Không tìm thấy dịch vụ.');
        return $m;
    }
}
