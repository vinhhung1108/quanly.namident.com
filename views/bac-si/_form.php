<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model app\models\BacSi */
/* @var $form yii\widgets\ActiveForm */

$this->registerCss(<<<CSS
.bs-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden}
.bs-header{padding:14px 16px;border-bottom:1px solid #eef0f2;background:#f9fafb}
.bs-title{font-size:18px;font-weight:600;margin:0}
.bs-body{padding:16px}
.grid{display:grid;grid-template-columns:repeat(12,1fr);gap:14px}
.col-12{grid-column:span 12}
.col-8{grid-column:span 8}
.col-6{grid-column:span 6}
.col-4{grid-column:span 4}
.col-3{grid-column:span 3}
.col-2{grid-column:span 2}

/* responsive */
@media (max-width: 992px){
  .col-8{grid-column:span 12}
  .col-6{grid-column:span 12}
  .col-4{grid-column:span 6}
  .col-3{grid-column:span 6}
  .col-2{grid-column:span 6}
}
@media (max-width: 600px){
  .col-4,.col-3,.col-2{grid-column:span 12}
}

.form-group{margin-bottom:10px}
.bs-actions{padding:12px 16px;border-top:1px solid #eef0f2;background:#fff;display:flex;gap:8px}
.small-hint{font-size:12px;color:#6b7280;margin-top:4px}

/* input-group gọn gàng */
.input-group-addon{background:#f3f4f6;border:1px solid #ced4da;border-left:0;border-radius:0 6px 6px 0;padding:6px 10px}
.input-group>.form-control{border-right:0;border-radius:6px 0 0 6px}
CSS);
?>

<div class="bac-si-form">
  <?php $form = ActiveForm::begin([
      'options' => ['autocomplete' => 'off'],
      'fieldConfig' => [
          'template' => "{label}\n{input}\n{error}\n{hint}",
          'labelOptions' => ['style' => 'font-weight:500;margin-bottom:6px'],
          'errorOptions' => ['class' => 'help-block text-danger'],
          'hintOptions'  => ['class' => 'small-hint'],
      ],
  ]); ?>

  <div class="bs-card">
    <div class="bs-header">
      <h3 class="bs-title"><?= $model->isNewRecord ? 'Thêm bác sĩ' : ('Bác sĩ #' . $model->ho_ten) ?></h3>
    </div>

    <div class="bs-body">
      <div class="grid">
        <!-- Họ tên -->
        <div class="col-8">
          <?= $form->field($model, 'ho_ten')
              ->textInput(['maxlength' => true, 'placeholder' => 'VD: Nguyễn Văn A']) ?>
        </div>

        <!-- Năm sinh -->
        <div class="col-4">
          <?= $form->field($model, 'nam_sinh')
              ->input('date')
              ->hint('Tuỳ chọn') ?>
        </div>

        <!-- Lương cố định (VNĐ, có phân cách phần ngàn khi gõ) -->
        <div class="col-6">
          <?php
          // Hiển thị ban đầu có dấu phẩy
          $luongDisplay = $model->luong_co_dinh !== null ? number_format((int)$model->luong_co_dinh, 0, '.', ',') : null;
          ?>
          <label class="control-label" for="bs-luong">Lương cố định</label>
          <div class="input-group">
            <?= MaskedInput::widget([
                'name' => Html::getInputName($model, 'luong_co_dinh'),
                'id'   => 'bs-luong',
                'value'=> $luongDisplay,
                'clientOptions' => [
                    'alias' => 'decimal',
                    'groupSeparator' => ',',
                    'radixPoint'     => '.',
                    'digits'         => 0,
                    'autoGroup'      => true,
                    'rightAlign'     => false,
                    'removeMaskOnSubmit' => true, // trả về số sạch
                ],
                'options' => ['class'=>'form-control', 'placeholder'=>'0'],
            ]) ?>
            <span class="input-group-addon">₫</span>
          </div>
          <div class="small-hint">Số nguyên VNĐ. Ví dụ: 8,000,000</div>
          <?= Html::error($model, 'luong_co_dinh', ['class'=>'help-block text-danger']) ?>
        </div>

        <!-- Tỷ lệ hoa hồng (%) có thập phân -->
        <div class="col-6">
          <?php
          $tyLeDisplay = $model->ty_le_hoa_hong !== null
              ? rtrim(rtrim(number_format((float)$model->ty_le_hoa_hong, 2, '.', ''), '0'), '.')
              : null;
          ?>
          <label class="control-label" for="bs-tyle">% Hoa hồng</label>
          <div class="input-group">
            <?= MaskedInput::widget([
                'name' => Html::getInputName($model, 'ty_le_hoa_hong'),
                'id'   => 'bs-tyle',
                'value'=> $tyLeDisplay,
                'clientOptions' => [
                    'alias' => 'decimal',
                    'groupSeparator' => ',',
                    'radixPoint'     => '.',
                    'digits'         => 2,
                    'autoGroup'      => false,
                    'rightAlign'     => false,
                    'removeMaskOnSubmit' => true, // trả về số sạch (vd 12.5)
                ],
                'options' => ['class'=>'form-control', 'placeholder'=>'0'],
            ]) ?>
            <span class="input-group-addon">%</span>
          </div>
          <div class="small-hint">Phần trăm theo tổng tạm thu. Ví dụ: 12.5</div>
          <?= Html::error($model, 'ty_le_hoa_hong', ['class'=>'help-block text-danger']) ?>
        </div>
      </div>
    </div>

    <div class="bs-actions">
      <?= Html::submitButton($model->isNewRecord ? 'Lưu' : 'Cập nhật', ['class' => 'btn btn-success']) ?>
      <?= Html::a('Hủy', ['index'], ['class'=>'btn btn-default']) ?>
    </div>
  </div>

  <?php ActiveForm::end(); ?>
</div>
