<?php

namespace app\controllers;

use Yii;
use app\models\DieuTri;
use app\models\DieuTriSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\filters\AccessControl;
/**
 * DieuTriController implements the CRUD actions for DieuTri model.
 */
class DieuTriController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
			'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','update','create','view','delete','index-limit'],
                'rules' => [                   
					[
						'allow' => false,
						'controllers' => ['dieu-tri'],
						'roles' => ['?'],
					],
					[
						'allow' => true,
						'actions' => ['index','create','view','update','delete','index-limit'],
						'roles' => ['admin','author'],
					],
					// [
					// 	'allow' => true,
					// 	'actions'=> ['update','delete'],
					// 	'roles'=>['admin'],
					// ],
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

    /**
     * Lists all DieuTri models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DieuTriSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndexLimit()
    {
        $searchModel = new DieuTriSearch();
        $dataProvider = $searchModel->searchlimit(Yii::$app->request->queryParams);

        return $this->render('index-limit', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DieuTri model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */


    /**
     * Finds the DieuTri model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DieuTri the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DieuTri::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
