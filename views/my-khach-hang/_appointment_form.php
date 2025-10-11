<?php
/**
 * @var $model app\models\AppointmentForm
 * @var $kh app\models\MyKhachHang
 */
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\BacSi;

$dsBacSiDrop = ArrayHelper::map(
    BacSi::find()->select(['ho_ten'])->orderBy('ho_ten')->asArray()->all(),
    'ho_ten', 'ho_ten'
);

?>

<?php $form = ActiveForm::begin([
    'id' => 'appointment-form',
    'action' => ['appointment-modal', 'id' => $kh->id],
    'enableClientValidation' => true,
    'options' => ['autocomplete' => 'off'],
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}",
        'labelOptions' => ['style' => 'font-weight:500;margin-bottom:6px'],
        'errorOptions' => ['class' => 'help-block text-danger'],
        'inputOptions' => ['class' => 'form-control'],
    ],
]); ?>

<?= Html::activeHiddenInput($model, 'kh_id', ['value' => $kh->id]) ?>
<?= Html::hiddenInput('clear', '0', ['id' => 'appt-clear']) ?>

<div class="row">
  <div class="col-sm-4">
    <?= $form->field($model, 'ngay_hen')->input('date') ?>
  </div>
  <div class="col-sm-4">
    <?= $form->field($model, 'gio_hen')->input('time') ?>
  </div>
  <div class="col-sm-4">
    <?= $form->field($model, 'bs_dieu_tri')->dropDownList(
        $dsBacSiDrop,
        ['prompt' => '— Chọn bác sĩ —']
    )->label('Bác sĩ') ?>
  </div>
</div>

<?= $form->field($model, 'noi_dung_hen')->textarea([
    'rows' => 3,
    'placeholder' => 'Ví dụ: Tái khám kiểm tra / lắp mão sứ / cắt chỉ ...',
]) ?>

<div style="display:flex;gap:8px;margin-top:6px">
  <?= Html::submitButton('Lưu lịch hẹn', ['class' => 'btn btn-primary']) ?>
  <button type="button" class="btn btn-danger" id="btn-clear-appt">Xoá lịch hẹn</button>
  <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
</div>

<?php ActiveForm::end(); ?>
