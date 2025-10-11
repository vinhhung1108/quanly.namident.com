<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\BacSi;
use app\models\NhomKhachHang;

$dsNhom = ArrayHelper::map(
    NhomKhachHang::find()->orderBy(['ten_nhom'=>SORT_ASC])->asArray()->all(),
    'id', 'ten_nhom'
);

/* @var $model app\models\MyKhachHang|app\models\KhachHang */

$this->registerCss(<<<CSS
.form-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:18px 18px 6px;margin-bottom:16px}
.form-title{font-weight:600;margin:0 0 14px;font-size:18px}
.form-actions{position:sticky;bottom:0;background:#f9fafb;border-top:1px solid #e5e7eb;padding:12px 16px;margin-top:16px;border-bottom-left-radius:12px;border-bottom-right-radius:12px}
.form-group{margin-bottom:12px}
.help-block{margin-top:6px}
/* .section{margin-bottom:16px} */
.hint{font-size:12px;color:#6b7280;margin-top:4px}
textarea.form-control{min-height:0}
.ta-120{min-height:120px;}
.ta-90{min-height:90px;}
.ta-30{min-height:30px;}
.ta-20{min-height:20px;}
.ta-10{min-height:10px;}
@media (max-width: 767px){
  .col-sm-6,.col-sm-4,.col-sm-8{width:100%;float:none}
}
CSS);

$this->registerCssFile(
  'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
);
$this->registerJsFile(
  'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
  ['depends' => [\yii\web\JqueryAsset::class]]
);

?>

<?php $form = ActiveForm::begin([
    'options' => ['autocomplete' => 'off'],
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}",
        'labelOptions' => ['style' => 'font-weight:500;margin-bottom:6px'],
        'inputOptions' => ['class' => 'form-control'],
        'errorOptions' => ['class' => 'help-block text-danger'],
    ],
]); ?>

<!-- Thông tin cơ bản -->
<div class="form-card">
  <div class="form-title">Thông tin cơ bản</div>

  <div class="row section">
    <div class="col-sm-4">
      <?= $form->field($model, 'ho_ten')->textInput([
        'maxlength' => true,
        'placeholder' => 'VD: Nguyễn Văn A',
        'autofocus' => true,
      ]) ?>
    </div>
    <div class="col-sm-2">
      <?= $form->field($model, 'ngay_sinh')->input('date') ?>
    </div>
    <div class="col-sm-2">
      <?= $form->field($model, 'gioi_tinh')->dropDownList(
        ['Nam'=>'Nam','Nữ'=>'Nữ','Khác'=>'Khác'],
        ['prompt'=>'Chọn giới tính']
      ) ?>
    </div>
    <div class="col-sm-2">
      <?= $form->field($model, 'ma_the')->textInput([
        'maxlength' => true,
        'placeholder' => 'Mã BN (nếu có)',
      ]) ?>
    </div>
    <div class="col-sm-2">
      <?= $form->field($model, 'gioi_thieu')->textInput([
        'maxlength' => 25,
        'placeholder' => 'Nguồn giới thiệu (FB, bạn bè, ...)',
      ]) ?>
    </div>
    
  </div>

  <div class="row section">
    <div class="col-sm-2">
      <?= $form->field($model, 'sdt')->textInput([
        'maxlength' => true,
        'placeholder' => 'VD: 09xxxxxxxx',
      ]) ?>
    </div>
    <div class="col-sm-6">
      <?= $form->field($model, 'dia_chi')->textInput([
        'placeholder' => 'Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành',
      ]) ?>
    </div>
    <div class="col-sm-4">
      <?= $form->field($model, 'nhom_ids')
      ->dropDownList($dsNhom, [
          'id' => 'nhom-ids',
          'multiple' => true,              // vẫn là nhiều-nhiều
      ]) ?>
    </div>
  </div>
</div>

<div class="form-card">
  <div class="row">
    <div class="col-sm-12">
      <?= $form->field($model, 'ghi_chu')->textarea([
        'rows' => 1,
        'placeholder' => 'Ghi chú thêm (dị ứng thuốc, thói quen, dặn dò...)',
        'class'=>'form-control ta-30',
      ]) ?>
    </div>
    <div class="col-sm-6">
      <div class="section">
        <?= $form->field($model, 'chan_doan')->textarea([
          'rows' => 1,
          'placeholder' => 'Ví dụ: Sâu răng 36, viêm tuỷ, chỉ định điều trị ...',
          'class'=>'form-control ta-30',
        ]) ?>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="section">
        <?= $form->field($model, 'tien_su_benh')->textarea([
          'rows' => 1,
          'placeholder' => 'Ví dụ: Tiểu đường type 2, dị ứng penicillin, đang dùng thuốc ...',
          'class'=>'form-control ta-30',
        ]) ?>
        <div class="hint">Nếu có bệnh nền/dị ứng, hãy ghi rõ tên thuốc & mức độ.</div>
      </div>
    </div>
  </div><!--end row-->
</div>

<!-- Hồ sơ y khoa -->




<div class="form-actions">
  <?= Html::submitButton('Lưu', ['class' => 'btn btn-primary']) ?>
  <?= Html::a('Hủy', ['index'], ['class' => 'btn btn-default', 'style'=>'margin-left:6px']) ?>
</div>

<?php ActiveForm::end(); ?>

<?php
$this->registerJs(<<<JS
(function(){
  var \$el = $('#nhom-ids');
  if (\$el.length) {
    \$el.select2({
      placeholder: 'Chọn nhóm khách hàng',
      allowClear: true,
      width: '100%'
    });
  }
})();
JS);
?>
