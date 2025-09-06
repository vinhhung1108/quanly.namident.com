<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\NhaKhoa */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="nha-khoa-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ten_nha_khoa')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dia_chi')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'so_dien_thoai')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ma_so_thue')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'logoFile')->fileInput(['accept'=>'image/*']) ?>

    <?php if ($model->logo): ?>
        <div style="margin:8px 0">
            <img src="<?= $model->getLogoUrl() ?>" alt="Logo hiện tại" style="max-height:80px; height:auto;">
        </div>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
