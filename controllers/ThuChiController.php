<?php

namespace app\controllers;

use Yii;
use app\models\ThuChi;
use app\models\ThuChiSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\models\ThuChiThongke;
use app\models\LoaiThuChi;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use app\models\Model;

/**
 * ThuChiController implements the CRUD actions for ThuChi model.
 */
class ThuChiController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
			'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','update','create','delete'],
                'rules' => [                   
					[
						'allow' => false,
						'controllers' => ['thu-chi'],
						'roles' => ['?'],
					],
					[
						'allow' => true,
						'actions' => ['index','create'],
						'roles' => ['admin','author'],
					],
					[
						'allow' => true,
						'actions'=> ['update','create','delete'],
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

    /**
     * Lists all ThuChi models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ThuChiSearch();
		$thongkeModel = new ThuChiThongke();
		$loaithuchi = ArrayHelper::map(LoaiThuChi::find()->all(), 'id','loai_thu_chi');
        //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider = $thongkeModel->thongke(Yii::$app->request->queryParams);
				
		if($thongkeModel->load(Yii::$app->request->queryParams)){
			$thu_kh = $this->thu_kh($thongkeModel->ngay_bat_dau,$thongkeModel->ngay_ket_thuc);
			$thu_khac = $this->thu_khac($thongkeModel->ngay_bat_dau,$thongkeModel->ngay_ket_thuc);
			$tong_chi = $thongkeModel->tongchi(Yii::$app->request->queryParams); //$this->tong_chi($thongkeModel->ngay_bat_dau,$thongkeModel->ngay_ket_thuc, $thongkeModel->loai);
			$con_lai = $this->con_lai($thongkeModel->ngay_bat_dau,$thongkeModel->ngay_ket_thuc);
		}else{
			$thu_kh = $this->thu_kh('','');
			$thu_khac = $this->thu_khac('','');
			$tong_chi = $thongkeModel->tongchi([]); //$this->tong_chi('','','');
			$con_lai = $this->con_lai('','');
		}

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'thu_kh' => $thu_kh,
			'thu_khac' => $thu_khac,
			'tong_chi' => $tong_chi,
			'thongkeModel' => $thongkeModel,
			'con_lai' => $con_lai,
			'loaithuchi' => $loaithuchi,
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
     * Creates a new ThuChi model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ThuChi();
		    $model->loaithuchi = ArrayHelper::map(LoaiThuChi::find()->all(), 'id','loai_thu_chi');		
	  
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
          
          $model->so_tien = $this->tien_text($model->so_tien_t);						
          $this->them_hinh_anh($model);
          if($model->ngay_thu == null){
            $model->ngay_thu = date('Y-m-d');
          }
          
          if($model->save()){
            return $this->redirect(['view', 'id' => $model->id]);						
          }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ThuChi model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		$model->so_tien_t = $model->so_tien;
		$model->loaithuchi = ArrayHelper::map(LoaiThuChi::find()->all(), 'id','loai_thu_chi');	
		
        if ($model->load(Yii::$app->request->post()) && $model->save()) 
		{							
			
			$model->so_tien = $this->tien_text($model->so_tien_t);			
			$this->them_hinh_anh($model);
			
			if($model->save()){
				return $this->redirect(['view', 'id' => $model->id]);
			}
			
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ThuChi model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
		//Remove folder images
		$pathimg_del = "uploads/thu-chi/" . $model->id;
		if(file_exists($pathimg_del)){
			$objects = scandir($pathimg_del);
			foreach ($objects as $object) {
				  if ($object != "." && $object != "..") {
					if (filetype($pathimg_del."/".$object) == "dir") remove_dir($pathimg_del."/".$object);
					else unlink($pathimg_del."/".$object);
				  }
			}
			reset($objects);
			rmdir($pathimg_del);
		}
		//End remove images
		
		$this->findModel($id)->delete();
		

        return $this->redirect(['index']);
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
        if (($model = ThuChi::findOne($id)) !== null) {
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
	public function thu_khac($ngay_bat_dau,$ngay_ket_thuc)
	{
		if($ngay_bat_dau <> '' && $ngay_ket_thuc <> ''){ $params_ngay = [':ngay_bat_dau'=>$ngay_bat_dau,':ngay_ket_thuc'=>$ngay_ket_thuc]; }
		if($ngay_bat_dau <> '' && $ngay_ket_thuc == ''){ $params_ngay = [':ngay_bat_dau'=>$ngay_bat_dau]; }
		if($ngay_bat_dau == '' && $ngay_ket_thuc <> ''){ $params_ngay = [':ngay_ket_thuc'=>$ngay_ket_thuc]; }
				
		if($ngay_bat_dau == '' && $ngay_ket_thuc == ''){
			$dbCommand = Yii::$app->db->createCommand('SELECT SUM(so_tien) AS so_tien FROM thu_chi WHERE thu_chi = "Thu"');
			$results = $dbCommand->queryAll();	
			return $results[0]['so_tien'];
		}
		if($ngay_bat_dau == '' && $ngay_ket_thuc <> ''){
			$dbCommand = Yii::$app->db->createCommand('SELECT SUM(so_tien) AS so_tien FROM thu_chi WHERE thu_chi = "Thu" AND ngay_thu <= :ngay_ket_thuc',$params_ngay);
			$results = $dbCommand->queryAll();
			return $results[0]['so_tien'];			
		}
		if($ngay_bat_dau <> '' && $ngay_ket_thuc == ''){
			$dbCommand = Yii::$app->db->createCommand('SELECT SUM(so_tien) AS so_tien FROM thu_chi WHERE thu_chi = "Thu" AND ngay_thu >= :ngay_bat_dau',$params_ngay);
			$results = $dbCommand->queryAll();	
			return $results[0]['so_tien'];
		}
		if($ngay_bat_dau <> '' && $ngay_ket_thuc <> ''){
			$dbCommand = Yii::$app->db->createCommand('SELECT SUM(so_tien) AS so_tien FROM thu_chi WHERE thu_chi = "Thu" AND (ngay_thu >= :ngay_bat_dau AND ngay_thu <= :ngay_ket_thuc)',$params_ngay);
			$results = $dbCommand->queryAll();				
			return $results[0]['so_tien'];
		}
	}
	
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
	//Thêm hình ảnh
	public function them_hinh_anh($model)
	{		
			$model->upload = UploadedFile::getInstances($model, 'upload'); //get upload
			$valid = $model->validate();
			if($valid){
				if($model->upload <> null){
					$uppath = 'uploads/thu-chi/' . $model->id;
					if(!file_exists($uppath)){ mkdir($uppath); }

					$files_exists = glob($uppath . '/*');
					if(count($files_exists) < 6) { 
						foreach ($model->upload as $f_key => $file) {
							if($f_key + count($files_exists) > 5) { break; } 

							$filePath = $uppath . '/' . uniqid() . '.' . $file->extension;
							Yii::$app->imageProcessor->save(['file' => $file->tempName], $filePath, 'galleryImage');

							if (file_exists($filePath)){
								if ($model->hinh_anh <> null) {
									$model->hinh_anh = $model->hinh_anh . ":". $filePath;
								}else {$model->hinh_anh = $filePath; }
							}		
						}
					}
				$model->save();
				}
			}
	}
	//Xoa hinh anh
	public function actionDeleteimg($id)
    {
        $model = $this->findModel($id);		
		if ($model->load(Yii::$app->request->post()) && $model->save()) 
		{			
			if(isset($_POST["id_img"])){
				
				foreach($_POST["id_img"] as $id_img){
					$link_img = 'uploads/thu-chi/' . $id . '/' . $id_img;
					if(file_exists($link_img)){
						unlink($link_img);					
					}
				}
				$pathupdate = '';
				foreach(glob("uploads/thu-chi/".$id."/*") as $filename){
					$filename = str_replace("uploads/thu-chi/" . $model->id . "/","",$filename);
					if($pathupdate <> '') {						
						$pathupdate = $pathupdate . ":"  . "uploads/thu-chi/".$model->id."/" . $filename;
					}else {$pathupdate = "uploads/thu-chi/" .$id."/".$filename; }
				}
				$model->hinh_anh = $pathupdate;
				$model->save();
			} else {
				Yii::$app->getSession()->setFlash('success', 'delete not effect');
			}					        
        }

        return $this->redirect(['update','id'=>$model->id]);
	}
	//End xóa hình
}
