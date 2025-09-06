<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\NhomKhachHang;
/* @var $this yii\web\View */
/* @var $model app\models\KhachHangSearch */
/* @var $form yii\widgets\ActiveForm */

$nhomkhachhang = ArrayHelper::merge([0 =>'Tất cả'],ArrayHelper::map(NhomKhachHang::find()->all(), 'id','ten_nhom'));

?>

<div class="khach-hang-search pull-right">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <?php echo Html::a(' + ', ['/nhom-khach-hang/index'], ['class'=>'pull-left', 'style'=>'font-size: 22px; padding-right:10px;text-decoration:none;', 'data-pjax'=>0]); ?>
    <?= $form->field($model, 'nhom_kh', ['options' => ['class'=>'pull-left', 'style'=>'padding-right: 10px;font-size:22px']])->dropDownList($nhomkhachhang)->label(false) ?> 
    <?= Html::submitButton('Tìm kiếm', ['class' => 'btn btn-primary pull-left']) ?>
    <!-- <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ho_ten') ?>  

    <?= $form->field($model, 'sdt') ?>

    <?= $form->field($model, 'ngay_sinh') ?> -->

    <?php // echo $form->field($model, 'dieu_tri') ?>

    <?php // echo $form->field($model, 'ngay_dieu_tri') ?>

    <?php // echo $form->field($model, 'ngay_hen') ?>

    <?php // echo $form->field($model, 'tong_phi') ?>

    <?php // echo $form->field($model, 'tam_thu') ?>

    <?php // echo $form->field($model, 'con_lai') ?>

    <?php // echo $form->field($model, 'bs_dieu_tri') ?>

    <?php // echo $form->field($model, 'gioi_thieu') ?>

    <?php // echo $form->field($model, 'hinh_anh') ?>

    <!-- <div class="form-group">
        
        <?php // echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div> -->

    <?php ActiveForm::end(); ?>

</div>
