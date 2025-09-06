<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\NguoiThuChi */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="nguoi-thu-chi-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ho_ten')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dien_thoai')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ghi_chu')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
