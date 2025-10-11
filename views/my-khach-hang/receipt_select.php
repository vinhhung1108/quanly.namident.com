<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $kh app\models\MyKhachHang */
/** @var $rows app\models\MyDieuTri[] */

$this->title = 'Chọn lịch sử điều trị để in phiếu thu';
?>
<div style="margin-top:60px">
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
    'action' => ['my-khach-hang/receipt-preview', 'id' => $kh->id],
    'method' => 'post',
]); ?>

<table class="table table-bordered table-striped table-condensed">
  <thead>
    <tr>
      <th style="width:40px"><input type="checkbox" id="checkAll"></th>
      <th style="width:110px">Ngày điều trị</th>
      <th>Nội dung</th>
      <th style="width:120px" class="text-right">Phí điều trị</th>
      <th style="width:120px" class="text-right">Thanh toán</th>
      <th style="width:90px">HTTT</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><input type="checkbox" class="pick" name="dt_ids[]" value="<?= $r->id ?>"></td>
        <td><?= Html::encode($r->ngay_dieu_tri ? date('d/m/Y', strtotime($r->ngay_dieu_tri)) : '') ?></td>
        <td>
          <?php
          $parts = [];
          if (method_exists($r, 'getDvItems')) {
            foreach (($r->dvItems ?? []) as $dv) {
              /** @var \app\models\MyDieuTriDv $dv */
              $name = $dv->ten_dv ?: ($dv->dichVu->ten ?? '');
              $meta = [];
              if ((int)$dv->so_luong > 1) $meta[] = 'x' . (int)$dv->so_luong;
              if (!empty($dv->rang_so))   $meta[] = 'răng ' . $dv->rang_so;
              $line = trim($name);
              if ($meta) $line .= ' (' . implode(' · ', $meta) . ')';
              if ($line !== '') $parts[] = Html::encode($line);
            }
          }
          if (!empty($r->noi_dung)) {
            foreach (preg_split("/\\r?\\n/", trim((string)$r->noi_dung)) as $row) {
              $row = trim($row);
              if ($row !== '') $parts[] = Html::encode($row);
            }
          }
          echo $parts ? implode('<br>', $parts) : '<span class="text-muted">—</span>';
          ?>
        </td>
        <td class="text-right" data-fee="<?= (int)$r->phi ?>"><?= number_format((int)$r->phi) ?></td>
        <td class="text-right" data-paid="<?= (int)$r->tam_thu ?>"><?= number_format((int)$r->tam_thu) ?></td>
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
  <?= Html::a('Quay lại KH', ['view', 'id' => $kh->id], ['class'=>'btn btn-default']) ?>
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
