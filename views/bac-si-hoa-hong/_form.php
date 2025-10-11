<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

/**
 * @var yii\web\View            $this
 * @var app\models\BacSiHoaHong $model
 * @var app\models\BacSi        $bacSi
 * @var array                   $dichVuOptions
 * @var bool                    $isUpdate
 */

$this->registerCss(<<<CSS
.hh-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden}
.hh-header{padding:14px 16px;border-bottom:1px solid #eef0f2;background:#f9fafb}
.hh-title{font-size:18px;font-weight:600;margin:0}
.hh-body{padding:18px;display:grid;gap:16px}
.hh-actions{padding:12px 16px;border-top:1px solid #eef0f2;background:#fff;display:flex;gap:8px;flex-wrap:wrap}
.small-hint{font-size:12px;color:#6b7280;margin-top:4px}
CSS);
?>

<?php $form = ActiveForm::begin([
    'options' => ['autocomplete' => 'off'],
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n{hint}",
        'labelOptions' => ['style' => 'font-weight:500;margin-bottom:6px'],
        'errorOptions' => ['class' => 'help-block text-danger'],
        'hintOptions'  => ['class' => 'small-hint'],
    ],
]); ?>

<div class="hh-card">
  <div class="hh-header">
    <h3 class="hh-title">
      <?= Html::encode($isUpdate ? 'Cập nhật hoa hồng' : 'Thêm hoa hồng theo dịch vụ') ?>
    </h3>
    <div style="font-size:13px;color:#6b7280;margin-top:4px">
      Bác sĩ: <strong><?= Html::encode($bacSi->ho_ten) ?></strong>
      · Hoa hồng mặc định: <strong><?= Html::encode(rtrim(rtrim(number_format((float)$bacSi->ty_le_hoa_hong, 2, '.', ''), '0'), '.')) ?>%</strong>
    </div>
  </div>

  <div class="hh-body">
    <?php if (!$isUpdate && empty($dichVuOptions)): ?>
      <div class="alert alert-warning" role="alert" style="margin:0">
        Bác sĩ đã được cấu hình hoa hồng cho tất cả dịch vụ hiện có. Vui lòng tạo thêm dịch vụ mới để thêm cấu hình.
      </div>
    <?php endif; ?>

    <?= $form->field($model, 'dv_id')->dropDownList(
        $dichVuOptions,
        [
            'prompt'   => '— Chọn dịch vụ —',
            'disabled' => $isUpdate,
        ]
    )->label(false)->hint('Mỗi dịch vụ chỉ tạo một cấu hình hoa hồng cho bác sĩ này.') ?>

    <?php
    $tyLeDisplay = $model->ty_le !== null
        ? rtrim(rtrim(number_format((float)$model->ty_le, 2, '.', ''), '0'), '.')
        : null;
    ?>
    <!-- <label class="control-label" for="hh-ty-le">% Hoa hồng</label> -->
    <div class="input-group">
      <?= MaskedInput::widget([
          'model' => $model,
          'attribute' => 'ty_le',
          'id'   => 'hh-ty-le',
          'value'=> $tyLeDisplay,
          'clientOptions' => [
              'alias' => 'decimal',
              'groupSeparator' => ',',
              'radixPoint'     => '.',
              'digits'         => 2,
              'autoGroup'      => false,
              'rightAlign'     => false,
              'removeMaskOnSubmit' => true,
          ],
          'options' => ['class'=>'form-control','placeholder'=>'0'],
      ])?>
      <span class="input-group-addon">%</span>
    </div>
    <div class="small-hint">Nếu nhập 0, hệ thống sẽ dùng tỷ lệ hoa hồng mặc định của bác sĩ khi tính lương.</div>
    <?= Html::error($model, 'ty_le', ['class'=>'help-block text-danger']) ?>
  </div>

  <div class="hh-actions">
    <?= Html::submitButton($isUpdate ? 'Cập nhật' : 'Lưu', ['class'=>'btn btn-success', 'disabled' => !$isUpdate && empty($dichVuOptions)]) ?>
    <?= Html::a('Quay lại', ['bac-si/view', 'id' => $bacSi->id], ['class'=>'btn btn-default']) ?>
  </div>
</div>

<?php ActiveForm::end(); ?>
