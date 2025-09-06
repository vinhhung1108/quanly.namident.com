<?php

namespace app\controllers;

use Yii;
use app\models\NhaKhoa;
use app\models\NhaKhoaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\filters\AccessControl;


/**
 * NhaKhoaController implements the CRUD actions for NhaKhoa model.
 */
class NhaKhoaController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','view','create','update','delete'],
                'rules' => [                   
                  [
                    'allow' => false,
                    'controllers' => ['index','view','create','update','delete'],
                    'roles' => ['?'],
                  ],
                  [
                    'allow' => true,
                    'actions' => ['index','view','create','update','delete'],
                    'roles' => ['admin','author'],
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
     * Lists all NhaKhoa models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->redirect(['view','id'=>1]);
    }

    /**
     * Displays a single NhaKhoa model.
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
     * Creates a new NhaKhoa model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    // public function actionCreate()
    // {
    //     $model = new NhaKhoa();

    //     if ($model->load(Yii::$app->request->post()) && $model->save()) {
    //         return $this->redirect(['view', 'id' => $model->id]);
    //     }

    //     return $this->render('create', [
    //         'model' => $model,
    //     ]);
    // }

    /**
     * Updates an existing NhaKhoa model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldLogo = $model->logo;


        if ($model->load(Yii::$app->request->post())) {
            $model->logoFile = UploadedFile::getInstance($model, 'logoFile');

            if ($model->validate()) {
                if ($model->logoFile) {
                    $dir = Yii::getAlias('@app/web/uploads/nha_khoa/' . $model->id);
                    if (!is_dir($dir)) {
                        @mkdir($dir, 0755, true);
                    }

                    $filename = 'logo_' . time() . '.' . $model->logoFile->extension;
                    $absPath  = $dir . '/' . $filename;
                    $relPath  = '/uploads/nha_khoa/' . $model->id . '/' . $filename;

                    // Lưu file
                    Yii::$app->imageProcessor->save(['file' => $model->logoFile->tempName], $absPath, 'logo');
                    // Hoặc dùng imageProcessor như ở trên

                    // Xoá file cũ
                    if (!empty($oldLogo) && $oldLogo !== $relPath) {
                        $oldAbs = Yii::getAlias('@app/web') . $oldLogo;
                        if (is_file($oldAbs)) @unlink($oldAbs);
                    }

                    $model->logo = $relPath;
                }

                $model->save(false);
                Yii::$app->session->setFlash('success','Đã cập nhật thông tin nha khoa');
                return $this->redirect(['view','id'=>$model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing NhaKhoa model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    // public function actionDelete($id)
    // {
    //     $this->findModel($id)->delete();

    //     return $this->redirect(['index']);
    // }

    /**
     * Finds the NhaKhoa model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return NhaKhoa the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NhaKhoa::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
