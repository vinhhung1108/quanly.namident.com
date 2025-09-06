<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\NhomKhachHang */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="nhom-khach-hang-form">

    <?php $form = ActiveForm::begin(); ?>
	<div class="row">
		<?= $form->field($model, 'ten_nhom',['options'=>['class'=>'form-group col-xs-12 col-sm-8']])->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'ghi_chu',['options'=>['class'=>'form-group col-xs-4 col-sm-2']])->textInput(['maxlength' => true]) ?>
	</div>
		<div class="form-group">
			<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
		</div>

    <?php ActiveForm::end(); ?>

</div>
