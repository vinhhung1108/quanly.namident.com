<?php

namespace app\controllers;

use Yii;
use app\models\DieuTri;
use app\models\BaoCaoThuSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use app\models\Model;

/**
 * ThuChiController implements the CRUD actions for ThuChi model.
 */
class BaoCaoThuController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
			'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','view'],
                'rules' => [                   
					[
						'allow' => false,
						'controllers' => ['thu-chi'],
						'roles' => ['?'],
					],
					[
						'allow' => true,
						'actions' => ['index','view'],
						'roles' => ['admin'],
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

    /**
     * Lists all ThuChi models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BaoCaoThuSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ThuChi model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the ThuChi model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ThuChi the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DieuTri::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
	
	/**
	*** Tính các khoản thu
	***/
	public function thu_kh($ngay_bat_dau,$ngay_ket_thuc)
	{		
		if($ngay_bat_dau <> '' && $ngay_ket_thuc <> ''){ $params_ngay = [':ngay_bat_dau'=>$ngay_bat_dau,':ngay_ket_thuc'=>$ngay_ket_thuc]; }
		if($ngay_bat_dau <> '' && $ngay_ket_thuc == ''){ $params_ngay = [':ngay_bat_dau'=>$ngay_bat_dau]; }
		if($ngay_bat_dau == '' && $ngay_ket_thuc <> ''){ $params_ngay = [':ngay_ket_thuc'=>$ngay_ket_thuc]; }
				
		if($ngay_bat_dau == '' && $ngay_ket_thuc == ''){
			$dbCommand = Yii::$app->db->createCommand('SELECT SUM(tam_thu) AS tam_thu FROM dieu_tri');
			$results = $dbCommand->queryAll();	
			return $results[0]['tam_thu'];
		}
		if($ngay_bat_dau == '' && $ngay_ket_thuc <> ''){
			$dbCommand = Yii::$app->db->createCommand('SELECT SUM(tam_thu) AS tam_thu FROM dieu_tri WHERE ngay_dieu_tri <= :ngay_ket_thuc',$params_ngay);
			$results = $dbCommand->queryAll();
			return $results[0]['tam_thu'];			
		}
		if($ngay_bat_dau <> '' && $ngay_ket_thuc == ''){
			$dbCommand = Yii::$app->db->createCommand('SELECT SUM(tam_thu) AS tam_thu FROM dieu_tri WHERE ngay_dieu_tri >= :ngay_bat_dau',$params_ngay);
			$results = $dbCommand->queryAll();	
			return $results[0]['tam_thu'];
		}
		if($ngay_bat_dau <> '' && $ngay_ket_thuc <> ''){
			$dbCommand = Yii::$app->db->createCommand('SELECT SUM(tam_thu) AS tam_thu FROM dieu_tri WHERE ngay_dieu_tri >= :ngay_bat_dau AND ngay_dieu_tri <= :ngay_ket_thuc',$params_ngay);
			$results = $dbCommand->queryAll();				
			return $results[0]['tam_thu'];
		}
	}
	
	
	/** còn lại **/
	public function con_lai($ngay_bat_dau,$ngay_ket_thuc)
	{
		if($ngay_bat_dau <> '' && $ngay_ket_thuc <> ''){ $params_ngay = [':ngay_bat_dau'=>$ngay_bat_dau,':ngay_ket_thuc'=>$ngay_ket_thuc]; }
		if($ngay_bat_dau <> '' && $ngay_ket_thuc == ''){ $params_ngay = [':ngay_bat_dau'=>$ngay_bat_dau]; }
		if($ngay_bat_dau == '' && $ngay_ket_thuc <> ''){ $params_ngay = [':ngay_ket_thuc'=>$ngay_ket_thuc]; }
				
		if($ngay_bat_dau == '' && $ngay_ket_thuc == ''){
			$dbCommand = Yii::$app->db->createCommand('SELECT SUM(con_lai) AS con_lai FROM khach_hang');
			$results = $dbCommand->queryAll();	
			return $results[0]['con_lai'];
		}
		if($ngay_bat_dau == '' && $ngay_ket_thuc <> ''){
			$dbCommand = Yii::$app->db->createCommand('SELECT SUM(con_lai) AS con_lai FROM khach_hang WHERE ngay_dieu_tri <= :ngay_ket_thuc',$params_ngay);
			$results = $dbCommand->queryAll();
			return $results[0]['con_lai'];			
		}
		if($ngay_bat_dau <> '' && $ngay_ket_thuc == ''){
			$dbCommand = Yii::$app->db->createCommand('SELECT SUM(con_lai) AS con_lai FROM khach_hang WHERE ngay_dieu_tri >= :ngay_bat_dau',$params_ngay);
			$results = $dbCommand->queryAll();	
			return $results[0]['con_lai'];
		}
		if($ngay_bat_dau <> '' && $ngay_ket_thuc <> ''){
			$dbCommand = Yii::$app->db->createCommand('SELECT SUM(con_lai) AS con_lai FROM khach_hang WHERE ngay_dieu_tri >= :ngay_bat_dau AND ngay_dieu_tri <= :ngay_ket_thuc',$params_ngay);
			$results = $dbCommand->queryAll();				
			return $results[0]['con_lai'];
		}
	}
	/***
	*** Xử lý số tiền ","
	***/
	public function tien_text($tien_text)
	{
		if(($tien_text == '')||($tien_text == null)){
			$so_tien = 0;
			}else{
				$tien_tam = explode(',',$tien_text);
				$so_tien = implode($tien_tam);
				$so_tien = intval($so_tien);				
			}
		return $so_tien;		
	}
}
