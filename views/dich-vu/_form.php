<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

/* @var $model app\models\DichVu */

$this->registerCss(<<<CSS
.form-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:18px;margin-bottom:16px}
.form-title{font-weight:600;margin:0 0 14px;font-size:18px}
CSS);
?>
<div class="form-card">
  <div class="form-title"><?= $model->isNewRecord ? 'Thêm dịch vụ' : 'Cập nhật dịch vụ' ?></div>

  <?php $form = ActiveForm::begin([
    'options' => ['autocomplete' => 'off'],
    'fieldConfig' => [
      'template' => "{label}\n{input}\n{error}",
      'labelOptions' => ['style' => 'font-weight:500;margin-bottom:6px'],
      'errorOptions' => ['class' => 'help-block text-danger'],
    ],
  ]); ?>

  <?= $form->field($model, 'ten')->textInput(['maxlength'=>255, 'placeholder'=>'Tên dịch vụ']) ?>

  <div class="row">
    <div class="col-sm-4">
      <?= $form->field($model, 'don_gia')->widget(MaskedInput::class, [
          'clientOptions' => [
              'alias' => 'decimal',
              'groupSeparator' => ',',
              'radixPoint'     => '.',
              'digits'         => 0,
              'autoGroup'      => true,
              'rightAlign'     => false,
              'removeMaskOnSubmit' => true,
          ],
          'options' => ['class'=>'form-control','placeholder'=>'0'],
      ]) ?>
    </div>
    <div class="col-sm-4">
      <?= $form->field($model, 'active')->dropDownList(
          ['1'=>'Đang dùng','0'=>'Ẩn'],
          ['prompt' => '— Chọn trạng thái —'] 
      ) ?>
    </div>
  </div>

  <div>
    <?= Html::submitButton('Lưu', ['class'=>'btn btn-primary']) ?>
    <?= Html::a('Hủy', ['index'], ['class'=>'btn btn-default', 'style'=>'margin-left:6px']) ?>
  </div>

  <?php ActiveForm::end(); ?>
</div>
