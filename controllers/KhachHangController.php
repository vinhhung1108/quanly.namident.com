<?php

namespace app\controllers;

use Yii;
use app\models\KhachHang;
use app\models\KhachHangSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\models\DieuTri;
use app\models\BacSi;
use app\models\NhaKhoa;
use app\models\NhomKhachHang;
use app\models\Model;
use yii\helpers\ArrayHelper;

use yii\web\UploadedFile;

use yii\filters\AccessControl;

use yii\imagine\Image;


/**
 * KhachHangController implements the CRUD actions for KhachHang model.
 */
class KhachHangController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
			'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','update','create','delete','view'],
                'rules' => [                   
					[
						'allow' => false,
						'controllers' => ['khach-hang'],
						'roles' => ['?'],
					],
					[
						'allow' => true,
						'actions' => ['index','create','view','update'],
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

    /**
     * Lists all KhachHang models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new KhachHangSearch();				
		
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single KhachHang model.
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
     * Creates a new KhachHang model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new KhachHang();
		$modelsDieuTri = [new DieuTri];
		$modelBacSi = new BacSi;
		$bac_si = ArrayHelper::map(BacSi::find()->all(), 'ho_ten','ho_ten');
		
    $bac_si = array_map(function($v){
    return (preg_match('/Ã|Â/', $v))
        ? mb_convert_encoding($v, 'UTF-8', 'Windows-1252,ISO-8859-1')
        : $v;
}, $bac_si);

        if ($model->load(Yii::$app->request->post()) && $model->save())
		{
			$modelsDieuTri = Model::createMultiple(DieuTri::classname());
            Model::loadMultiple($modelsDieuTri, Yii::$app->request->post());
			
			$model->upload = UploadedFile::getInstances($model, 'upload');
			
			
            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsDieuTri) && $valid;
            
			$tong_phi = 0;
			$tam_thu = 0;
			$ngay_dieu_tri = null;
			$tam_thu_last = 0;
			//$check_tam_thu_last = true;
			foreach ($modelsDieuTri as $key => $modelDieuTri)
			{
				
				if(($modelDieuTri->phi_t == null) || ($modelDieuTri->phi_t == '')){$phit = 0;
				}else{
					$phi = explode(',',$modelDieuTri->phi_t);
					$phit = implode($phi);					
				}
				$modelDieuTri->phi = intval($phit);
				
				if(($modelDieuTri->tam_thu_t == null) || ($modelDieuTri->tam_thu_t == '')){
                    $phitamt= 0;
                    //$tam_thu_last = 0;
				}else{
					$phitam = explode(',',$modelDieuTri->tam_thu_t);				
					$phitamt = implode($phitam);
				}
				$modelDieuTri->tam_thu = intval($phitamt);

				//if($check_tam_thu_last) {$tam_thu_last = $modelDieuTri->tam_thu; $check_tam_thu_last = !$check_tam_thu_last;}

				$tong_phi = intval($modelDieuTri->phi) + $tong_phi;
				$tam_thu = intval($modelDieuTri->tam_thu) + $tam_thu;

                if($modelDieuTri->ngay_dieu_tri > $ngay_dieu_tri){
                    $ngay_dieu_tri = $modelDieuTri->ngay_dieu_tri;
                    $tam_thu_last = $modelDieuTri->tam_thu;
                }
			}
			  if ($ngay_dieu_tri === '' || $ngay_dieu_tri === '0') {
              $ngay_dieu_tri = null;
          }
			$model->ngay_dieu_tri = $ngay_dieu_tri;
			$con_lai = $tong_phi - $tam_thu;
			$model->tong_phi = $tong_phi;
			$model->tam_thu = $tam_thu;
			$model->con_lai = $con_lai;
			$model->tam_thu_last = $tam_thu_last;
			
			$model->ho_ten = mb_strtoupper($model->ho_ten);
			$model->gio = date('H:i');
            if ($valid) {
                $uppath = 'uploads';
                if(!file_exists($uppath) ){mkdir($uppath); }
                $uppath = 'uploads/posts';
                if(!file_exists($uppath)) {mkdir($uppath); }	
				if($model->upload <> null){
                    $uppath = 'uploads/posts/'.$model->id;
                    if(!file_exists($uppath)){mkdir($uppath);}
                }
                $files_exists = glob($uppath . '/*');
                if(count($files_exists) < 6) { // Cho phep tai len neu da co it hon 6 file
                    foreach ($model->upload as $f_key => $file) {
                        if($f_key + count($files_exists) > 5) { break; }  //Neu nhieu hon 6 file thi break
                        $filePath = $uppath . '/' . uniqid() . '.' . $file->extension;
                        Yii::$app->imageProcessor->save(['file' => $file->tempName], $filePath, 'galleryImage');
                        if(file_exists($filePath)) {
                            if ($model->hinh_anh <> null) {
                                $model->hinh_anh = $model->hinh_anh . ":". $filePath;
                            }else {$model->hinh_anh = $filePath; }
                        }					
                    }
                }
				
				if($model->videos <> null){
					if ($model->video <> null) {
						$model->video = $model->video . "__" . $model->videos;
					}else {$model->video = $model->videos; }
				}
        
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        foreach ($modelsDieuTri as $modelDieuTri) {
                            $modelDieuTri->id_kh = $model->id;													
                            if (! ($flag = $modelDieuTri->save(false))) {								
								$transaction->rollBack();								
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
			
           // return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
			'modelsDieuTri' => (empty($modelsDieuTri)) ? [new DieuTri] : $modelsDieuTri,
			'bac_si' => $bac_si
        ]);
    }

    /**
     * Updates an existing KhachHang model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		$modelsDieuTri = $model->dieuTris;
		$bac_si = ArrayHelper::map(BacSi::find()->all(), 'ho_ten','ho_ten');
		$user = Yii::$app->user->identity;

		foreach ($modelsDieuTri as $key => $modelDieuTri)
		{ 
			$modelDieuTri->phi_t = $modelDieuTri->phi;
			$modelDieuTri->tam_thu_t = $modelDieuTri->tam_thu;
            
		}

        if ($model->load(Yii::$app->request->post()) && $model->save()) 
		{
			$oldIDs = ArrayHelper::map($modelsDieuTri, 'id', 'id');
            $modelsDieuTri = Model::createMultiple(DieuTri::classname(), $modelsDieuTri);
            Model::loadMultiple($modelsDieuTri, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsDieuTri, 'id', 'id')));
									
			$model->upload = UploadedFile::getInstances($model, 'upload');
            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsDieuTri) && $valid;			
			
			$tong_phi = 0;
			$tam_thu = 0;
			$ngay_dieu_tri = null;
			$tam_thu_last = 0;			
			//$check_tam_thu_last = true;
			foreach ($modelsDieuTri as $key => $modelDieuTri)
			{
							
				if(($modelDieuTri->phi_t == null) || ($modelDieuTri->phi_t == '')){$phit = 0;
				}else{
					$phi = explode(',',$modelDieuTri->phi_t);
					$phit = implode($phi);					
				}
				$modelDieuTri->phi = intval($phit);
				
				if(($modelDieuTri->tam_thu_t == null) || ($modelDieuTri->tam_thu_t == '')){
                    $phitamt= 0;
                    //$tam_thu_last = 0;
				}else{
					$phitam = explode(',',$modelDieuTri->tam_thu_t);				
					$phitamt = implode($phitam);
				}
				$modelDieuTri->tam_thu = intval($phitamt);

				//if($check_tam_thu_last) {$tam_thu_last = $modelDieuTri->tam_thu; $check_tam_thu_last = !$check_tam_thu_last;}
				
                $tong_phi = intval($modelDieuTri->phi) + $tong_phi;
				$tam_thu = intval($modelDieuTri->tam_thu) + $tam_thu;
                if($modelDieuTri->ngay_dieu_tri > $ngay_dieu_tri){
                    $ngay_dieu_tri = $modelDieuTri->ngay_dieu_tri;
                    $tam_thu_last = $modelDieuTri->tam_thu;
                }	
			}			
			
			$model->ngay_dieu_tri = $ngay_dieu_tri;						
			$con_lai = $tong_phi - $tam_thu;
			$model->tong_phi = $tong_phi;
			$model->tam_thu = $tam_thu;
			$model->con_lai = $con_lai;
			$model->tam_thu_last = $tam_thu_last;			
			$model->ho_ten = mb_strtoupper($model->ho_ten);
			$model->gio = date('H:i');
            if ($valid) {
                $uppath = 'uploads';
                if(!file_exists($uppath) ){mkdir($uppath); }
                $uppath = 'uploads/posts';
                if(!file_exists($uppath)) {mkdir($uppath); }	
				if($model->upload <> null){
                    $uppath = 'uploads/posts/'.$model->id;
                    if(!file_exists($uppath)){mkdir($uppath);}
                }
				$files_exists = glob($uppath . '/*');
                if(count($files_exists) < 6) {
                    foreach ($model->upload as $f_key => $file) {
                        if($f_key + count($files_exists) > 5) { break; }
                        $filePath = $uppath . '/' . uniqid() . '.' . $file->extension;
                        Yii::$app->imageProcessor->save(['file' => $file->tempName], $filePath, 'galleryImage');
                        if(file_exists($filePath)) {
                            if ($model->hinh_anh <> null) {
                                $model->hinh_anh = $model->hinh_anh . ":". $filePath;
                            }else {$model->hinh_anh = $filePath; }
                        }					
                    }
                }

				if($model->videos <> null){
					if ($model->video <> null) {
						$model->video = $model->video . "__" . $model->videos;
					}else {$model->video = $model->videos; }
				}				
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (! empty($deletedIDs)) {
                            DieuTri::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsDieuTri as $modelDieuTri) {
                                $modelDieuTri->id_kh = $model->id;							
                                if (! ($flag = $modelDieuTri->save(false))) {								
                                    $transaction->rollBack();								
                                    break;
                                }
                            
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }

        }

        return $this->render('update', [
            'model' => $model,
			'modelsDieuTri' => (empty($modelsDieuTri)) ? [new DieuTri] : $modelsDieuTri,
			'bac_si' => $bac_si,
        ]);
    }

    /**
     * Deletes an existing KhachHang model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
		$modelsDieuTri = $model->dieuTris; //Khai báo trong model KhachHang => function getDieuTris()...
			foreach ($modelsDieuTri as $i => $modelDieuTri):
				$modelDieuTri->delete();
			endforeach;
        $name = $model->ho_ten;
		//Remove folder images
		$pathimg_del = "uploads/posts/".$model->id;
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
		

        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Đã xóa khách hàng  <strong>"' . $name . '"</strong>.');
        }


        return $this->redirect(['index']);
    }

    /**
     * Finds the KhachHang model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return KhachHang the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = KhachHang::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
	//Xoa hinh anh
	public function actionDeleteimg($id)
    {
        $model = $this->findModel($id);
		$modelsDieuTri = $model->dieuTris;
		if ($model->load(Yii::$app->request->post()) && $model->save()) 
		{
			
			$oldIDs = ArrayHelper::map($modelsDieuTri, 'id', 'id');
            $modelsDieuTri = Model::createMultiple(DieuTri::classname(), $modelsDieuTri);
            Model::loadMultiple($modelsDieuTri, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsDieuTri, 'id', 'id')));

            // ajax validation
           /* if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsAddress),
                    ActiveForm::validate($modelCustomer)
                );
            }
			*/			
			if(isset($_POST["id_img"])){
				foreach($_POST["id_img"] as $id_img){
					$link_img = 'uploads/posts/'.$id.'/'.$id_img;
					if(file_exists($link_img)){
						unlink($link_img);					
					}
				}
				$pathupdate = '';
				foreach(glob("uploads/posts/".$id."/*") as $filename){
					$filename = str_replace("uploads/posts/" . $model->id . "/","",$filename);
					
					if($pathupdate <> '') {						
						$pathupdate = $pathupdate . ":" . "uploads/posts/".$model->id."/" . $filename;
					}else {$pathupdate = "uploads/posts/" .$id."/". $filename; }
				}
				$model->hinh_anh = $pathupdate;
			}
			$model->upload = UploadedFile::getInstances($model, 'upload');
            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsDieuTri) && $valid;
		
            if ($valid) {
                $uppath = 'uploads';
                if(!file_exists($uppath) ){mkdir($uppath); }
                $uppath = 'uploads/posts';
                if(!file_exists($uppath)) {mkdir($uppath); }
				if($model->upload <> null){ $uppath = 'uploads/posts/'.$id; if(!file_exists($uppath)){mkdir($uppath);}}
				foreach ($model->upload as $file) {
					$filePath = $uppath . '/' . $file->baseName . '.' . $file->extension;					
					if ($file->saveAs($filePath)){
						if ($model->hinh_anh <> null) {
							$model->hinh_anh = $model->hinh_anh . ":". $filePath;
						}else {$model->hinh_anh = $filePath; }
					}					
				}
				
				if($model->videos <> null){
					if ($model->video <> null) {
						$model->video = $model->video . "__" . $model->videos;
					}else {$model->video = $model->videos; }
				}
				
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (! empty($deletedIDs)) {
                            DieuTri::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsDieuTri as $modelDieuTri) {
                            $modelDieuTri->id_kh = $model->id;
                            if (! ($flag = $modelDieuTri->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['update', 'id' => $model->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }

        }

        return $this->render('update', [
            'model' => $model,
			'modelsDieuTri' => (empty($modelsDieuTri)) ? [new DieuTri] : $modelsDieuTri
        ]);
	}
	//Xoa video
	public function actionDeletevideo($id)
    {
        $model = $this->findModel($id);
		$modelsDieuTri = $model->dieuTris;
		if ($model->load(Yii::$app->request->post()) && $model->save()) 
		{
			
			$oldIDs = ArrayHelper::map($modelsDieuTri, 'id', 'id');
            $modelsDieuTri = Model::createMultiple(DieuTri::classname(), $modelsDieuTri);
            Model::loadMultiple($modelsDieuTri, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsDieuTri, 'id', 'id')));

            // ajax validation
           if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsAddress),
                    ActiveForm::validate($modelCustomer)
                );
            }
						
			if(isset($_POST["id_video"])){
				foreach($_POST["id_video"] as $id_video){
					$temp_video = $model->video;
					$id_video_rp = "https://youtu.be". $id_video;
					$temp_video = str_replace("__".$id_video_rp."__","__",$temp_video);
					$temp_video = str_replace($id_video_rp."__","",$temp_video);
					$temp_video = str_replace("__".$id_video_rp,"",$temp_video);
					$temp_video = str_replace($id_video_rp,"",$temp_video);
				}
				$model->video = $temp_video;
			}
			
			$model->upload = UploadedFile::getInstances($model, 'upload');
            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsDieuTri) && $valid;
		
            if ($valid) {
				if($model->upload <> null){ $uppath = 'uploads/'.$id; if(!file_exists($uppath)){mkdir($uppath);}}
				foreach ($model->upload as $file) {
					$filePath = $uppath . '/' . $file->baseName . '.' . $file->extension;					
					if ($file->saveAs($filePath)){
						if ($model->hinh_anh <> null) {
							$model->hinh_anh = $model->hinh_anh . ":". $filePath;
						}else {$model->hinh_anh = $filePath; }
					}					
				}
				
				if($model->videos <> null){
					if ($model->video <> null) {
						$model->video = $model->video . "__" . $model->videos;
					}else {$model->video = $model->videos; }
				}
				
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (! empty($deletedIDs)) {
                            DieuTri::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsDieuTri as $modelDieuTri) {
                            $modelDieuTri->id_kh = $model->id;
                            if (! ($flag = $modelDieuTri->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['update', 'id' => $model->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }

        }

        return $this->render('update', [
            'model' => $model,
			'modelsDieuTri' => (empty($modelsDieuTri)) ? [new DieuTri] : $modelsDieuTri
        ]);
	}

  public function actionReceipt($id)
{
    $kh = KhachHang::findOne($id);
    if (!$kh) throw new NotFoundHttpException('Không tìm thấy khách hàng.');
    $rows = DieuTri::find()->where(['id_kh' => $id])
        ->orderBy(['ngay_dieu_tri' => SORT_DESC, 'id' => SORT_DESC])
        ->all();

    return $this->render('receipt_select', [
        'kh' => $kh,
        'rows' => $rows,
    ]);
}

public function actionReceiptPreview($id)
{
    $kh = KhachHang::findOne($id);
    if (!$kh) throw new NotFoundHttpException('Không tìm thấy khách hàng.');

    $ids = (array)\Yii::$app->request->post('dt_ids', []);
    if (empty($ids)) {
        \Yii::$app->session->setFlash('warning', 'Bạn chưa chọn dòng điều trị nào.');
        return $this->redirect(['receipt', 'id' => $id]);
    }

    // Chỉ nhận các id thuộc đúng khách hàng này (tránh chọn linh tinh)
    $rows = DieuTri::find()->where(['id' => $ids, 'id_kh' => $id])
        ->orderBy(['ngay_dieu_tri' => SORT_ASC, 'id' => SORT_ASC])
        ->all();

    if (!$rows) {
        \Yii::$app->session->setFlash('warning', 'Dòng điều trị đã chọn không hợp lệ.');
        return $this->redirect(['receipt', 'id' => $id]);
    }

    $tong_phi = 0; $thanh_toan = 0;
    foreach ($rows as $r) {
        $tong_phi   += (int)$r->phi;
        $thanh_toan += (int)$r->tam_thu;
    }
    $con_lai = $tong_phi - $thanh_toan;

    $nhaKhoa = NhaKhoa::find()->one(); // nếu có nhiều cơ sở, tùy logic của bạn
    $today = new \DateTime('now', new \DateTimeZone('Asia/Ho_Chi_Minh'));

    // Đọc số tiền bằng chữ (ưu tiên intl)
    $soTienBangChu = $this->vietnameseMoneyWords($tong_phi) . ' đồng';

    return $this->render('receipt_preview', [
        'kh' => $kh,
        'rows' => $rows,
        'nhaKhoa' => $nhaKhoa,
        'today' => $today,
        'tong_phi' => $tong_phi,
        'thanh_toan' => $thanh_toan,
        'con_lai' => $con_lai,
        'soTienBangChu' => $soTienBangChu,
    ]);
}

/**
 * Đọc số tiền bằng chữ tiếng Việt.
 * Dùng intl NumberFormatter nếu có; fallback sang bộ đọc đơn giản.
 */
private function vietnameseMoneyWords($number)
{
    if (class_exists(\NumberFormatter::class)) {
        try {
            $fmt = new \NumberFormatter('vi_VN', \NumberFormatter::SPELLOUT);
            $w = $fmt->format($number);
            if ($w !== false && $w !== null) {
                // Chuẩn hoá viết hoa đầu câu
                return mb_convert_case($w, MB_CASE_TITLE, "UTF-8");
            }
        } catch (\Throwable $e) { /* ignore */ }
    }
    // Fallback đơn giản (đọc hàng nghìn/triệu/tỷ). Đủ dùng cho chứng từ thường gặp.
    return $this->simpleVnNumber($number);
}

private function simpleVnNumber($number)
{
    $dv = ['không','một','hai','ba','bốn','năm','sáu','bảy','tám','chín'];
    $u = ['', 'nghìn', 'triệu', 'tỷ', 'nghìn tỷ', 'triệu tỷ'];
    if ($number == 0) return 'Không';

    $num = (string)$number;
    $num = ltrim($num, '0');
    $groups = [];
    while (strlen($num) > 0) {
        $groups[] = substr($num, -3);
        $num = substr($num, 0, -3);
    }
    $parts = [];
    foreach ($groups as $i => $grp) {
        $grp = str_pad($grp, 3, '0', STR_PAD_LEFT);
        $tr = (int)$grp[0];
        $ch = (int)$grp[1];
        $dv1 = (int)$grp[2];

        $chunk = [];
        if ($tr > 0) $chunk[] = $dv[$tr] . ' trăm';
        if ($ch > 1) {
            $chunk[] = $dv[$ch] . ' mươi';
            if ($dv1 == 1) $chunk[] = 'mốt';
            elseif ($dv1 == 5) $chunk[] = 'lăm';
            elseif ($dv1 > 0) $chunk[] = $dv[$dv1];
        } elseif ($ch == 1) {
            $chunk[] = 'mười';
            if ($dv1 == 5) $chunk[] = 'lăm';
            elseif ($dv1 > 0) $chunk[] = $dv[$dv1];
        } else { // ch == 0
            if ($tr > 0 && $dv1 > 0) {
                $chunk[] = 'lẻ';
                if ($dv1 == 5) $chunk[] = 'lăm';
                else $chunk[] = $dv[$dv1];
            } elseif ($tr == 0 && $dv1 > 0) {
                $chunk[] = $dv[$dv1];
            }
        }
        $text = trim(implode(' ', $chunk));
        if ($text !== '') {
            $unit = $u[$i] ?? '';
            $parts[] = trim($text . ' ' . $unit);
        }
    }
    $parts = array_reverse($parts);
    $res = trim(preg_replace('/\s+/', ' ', implode(' ', $parts)));
    // Viết hoa chữ cái đầu
    return mb_strtoupper(mb_substr($res, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($res, 1, null, 'UTF-8');
  }
	

}
