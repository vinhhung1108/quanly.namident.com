<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\LoaiThuChi */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="loai-thu-chi-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'loai_thu_chi')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ghi_chu')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
