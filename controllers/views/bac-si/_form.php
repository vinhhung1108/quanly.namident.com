<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BacSi */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bac-si-form">

    <?php $form = ActiveForm::begin(); ?>
	<div class="row">
		<?= $form->field($model, 'ho_ten',['options'=>['class'=>'form-group col-xs-12 col-sm-8']])->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'nam_sinh',['options'=>['class'=>'form-group col-xs-4 col-sm-2']])->input('date') ?>
	</div>
		<div class="form-group">
			<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
		</div>

    <?php ActiveForm::end(); ?>

</div>
