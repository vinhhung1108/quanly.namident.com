<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var $kh app\models\KhachHang */
/** @var $rows app\models\DieuTri[] */

$this->title = 'Chọn lịch sử điều trị để in phiếu thu';
?>
<div style="margin-top:100px">
<h3><?= Html::encode($this->title) ?></h3>
</div>

<div class="well" style="padding:10px 15px;">
  <strong>Khách hàng:</strong> <?= Html::encode($kh->ho_ten) ?> |
  <strong>SĐT:</strong> <?= Html::encode($kh->sdt) ?> |
  <strong>Hẹn tái khám:</strong>
  <?= $kh->ngay_hen ? Yii::$app->formatter->asDate($kh->ngay_hen, 'php:d/m/Y') : '-' ?>,
  <?= Html::encode($kh->noi_dung_hen) ?>
</div>

<?php $form = ActiveForm::begin([
    'action' => ['khach-hang/receipt-preview', 'id' => $kh->id],
    'method' => 'post',
]); ?>

<table class="table table-bordered table-striped table-condensed">
  <thead>
    <tr>
      <th style="width:40px"><input type="checkbox" id="checkAll"></th>
      <th style="width:110px">Ngày điều trị</th>
      <th>Nội dung</th>
      <th style="width:160px">Bác sĩ</th>
      <th style="width:120px" class="text-right">Phí điều trị</th>
      <th style="width:120px" class="text-right">Thanh toán</th>
      <th style="width:90px">HTTT</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td>
          <input type="checkbox" class="pick" name="dt_ids[]" value="<?= $r->id ?>">
        </td>
        <td><?= Html::encode($r->ngay_dieu_tri ? date('d/m/Y', strtotime($r->ngay_dieu_tri)) : '') ?></td>
        <td><?= nl2br(Html::encode($r->noi_dung)) ?></td>
        <td><?= Html::encode($r->bs) ?></td>
        <td class="text-right" data-fee="<?= $r->phi ?>">
          <?= number_format((int)$r->phi) ?>
        </td>
        <td class="text-right" data-paid="<?= $r->tam_thu ?>">
          <?= number_format((int)$r->tam_thu) ?>
        </td>
        <td><?= Html::encode($r->hinh_thuc_thanh_toan) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
  <tfoot>
    <tr>
      <th colspan="4" class="text-right">Tổng:</th>
      <th class="text-right" id="sumFee">0</th>
      <th class="text-right" id="sumPaid">0</th>
      <th></th>
    </tr>
  </tfoot>
</table>

<div class="text-right">
  <?= Html::a('Quay lại', ['view', 'id' => $kh->id], ['class'=>'btn btn-default']) ?>
  <button type="submit" class="btn btn-primary">Xem trước &amp; In</button>
</div>

<?php ActiveForm::end(); ?>

<?php
$js = <<<JS
function fmt(n){ return n.toString().replace(/\\B(?=(\\d{3})+(?!\\d))/g, ","); }
function recalc(){
  var fee=0, paid=0;
  $('input.pick:checked').each(function(){
    var tr = $(this).closest('tr');
    fee  += parseInt(tr.find('[data-fee]').data('fee'))||0;
    paid += parseInt(tr.find('[data-paid]').data('paid'))||0;
  });
  $('#sumFee').text(fmt(fee));
  $('#sumPaid').text(fmt(paid));
}
$('#checkAll').on('change', function(){
  $('.pick').prop('checked', this.checked);
  recalc();
});
$(document).on('change', '.pick', recalc);
JS;
$this->registerJs($js);
