<?php
use yii\helpers\Html;

/** @var $kh app\models\MyKhachHang */
/** @var $nhaKhoa app\models\NhaKhoa|null */
/** @var $rows app\models\MyDieuTri[] */
/** @var $today DateTime */
/** @var $tong_phi int */
/** @var $thanh_toan int */
/** @var $con_lai int */
/** @var $soTienBangChu string */

$this->title = 'Phiếu thu - ' . Html::encode($kh->ho_ten);
?>
<style>
.receipt-wrap { max-width: 780px; margin: 0 auto; font-size: 11px; line-height: 1.45;}
.header { display:flex; justify-content:space-between; align-items:flex-start; }
.header .clinic { font-weight: 600; }
.title { text-align:center; font-size: 18px; font-weight:700; margin: 6px 0 4px; }
.meta { text-align:center; margin-bottom:12px; }
.table { width:100%; border-collapse: collapse; }
.table th, .table td { border:1px solid #000; padding:6px; }
.table > thead > tr > th { border-bottom: 1px solid #000; padding: 4px;}
.table > thead:first-child > tr:first-child > th { border-top: 1px solid #000; }
.table th { text-align:center; }
.table { margin-bottom: 10px; }
.table > tbody > tr > td { padding:4px;}
.summary { margin-bottom:2px; margin-top:0px }
.sign { display:flex; justify-content:space-between; margin-top: 10px; }
.sign .col { width: 45%; text-align:center; }
.hint { font-style: italic; opacity: .8; font-size: 11px; }

@page { size: A5 landscape; margin: 8mm; }
@media print {
  .no-print { display:none !important; }
  .receipt-wrap { max-width: none; font-size: 11px; line-height: 1.4; }
  .header img { max-height: 34px; height: 34px; }
  .title { font-size: 18px; margin: 6px 0 4px; }
  .table { table-layout: fixed; width: 100%; border-collapse: collapse; }
  .table th:nth-child(1), .table td:nth-child(1) { width: 22mm; text-align: center; }
  .table th:nth-child(3), .table td:nth-child(3) { width: 22mm; }
  .table th:nth-child(4), .table td:nth-child(4) { width: 24mm; }
  .table th:nth-child(5), .table td:nth-child(5) { width: 24mm; }
  .table th:nth-child(2), .table td:nth-child(2) { width: auto; }
  .table td:nth-child(2) { white-space: normal; word-break: break-word; overflow-wrap: anywhere; }
  thead { display: table-header-group; }
  tfoot { display: table-footer-group; }
  tr { break-inside: avoid; page-break-inside: avoid; }
  .footer { display: none !important; }
  .wrap { padding-bottom: 0 !important; }
}
</style>

<div class="space-top no-print" style="height:60px"></div>
<div class="receipt-wrap">
  <div class="header">
    <div class="clinic">
      <?php if ($nhaKhoa && $nhaKhoa->logo): ?>
        <img src="<?= Yii::$app->request->baseUrl . $nhaKhoa->logo ?>" alt="Logo" style="height:48px;">
        <br>
      <?php endif; ?>
      <?= $nhaKhoa ? Html::encode($nhaKhoa->ten_nha_khoa) : 'NHA KHOA' ?><br>
      <?= $nhaKhoa ? Html::encode($nhaKhoa->dia_chi) : '' ?>&nbsp; | &nbsp;
      <?= $nhaKhoa ? 'ĐT: ' . Html::encode($nhaKhoa->so_dien_thoai) : '' ?>
      <?= $nhaKhoa && $nhaKhoa->ma_so_thue ? '<br>MST: ' . Html::encode($nhaKhoa->ma_so_thue) : '' ?>
    </div>
    <div style="text-align:right">
      <div><strong>Ngày lập:</strong>
        <?= 'Ngày ' . $today->format('d') . ' tháng ' . $today->format('m') . ' năm ' . $today->format('Y') ?>
      </div>
      <div><strong>Mã KH:</strong> <?= Html::encode($kh->ma_the ?: $kh->id) ?></div>
    </div>
  </div>

  <div class="title">PHIẾU THU</div>
  <div class="meta"></div>

  <div>
    <strong>Họ tên KH:</strong> <?= Html::encode($kh->ho_ten) ?> &nbsp; | &nbsp;
    <strong>SĐT:</strong> <?= Html::encode($kh->sdt) ?>
    <br />
    <strong>Hẹn tái khám:</strong>
    <?= $kh->ngay_hen ? Yii::$app->formatter->asDate($kh->ngay_hen, 'php:d/m/Y') : '...' ?>,
    <strong>Giờ:</strong> <?= Html::encode($kh->gio_hen ?: '...') ?>,
    <strong>Bác sĩ:</strong> <?= Html::encode($kh->bs_dieu_tri ?: '...') ?>
    <br />
    <strong>Nội dung hẹn:</strong> <?= Html::encode($kh->noi_dung_hen ?: '...') ?>
  </div>

  <div class="summary">
    <strong>Tổng tiền:</strong> <?= number_format((int)$tong_phi, 0, '.', ',') ?> đ &nbsp;&nbsp;
    <strong>Thanh toán:</strong> <?= number_format((int)$thanh_toan, 0, '.', ',') ?> đ &nbsp;&nbsp;
    <strong>Còn lại:</strong> <?= number_format((int)$con_lai, 0, '.', ',') ?> đ
    <br>
    <strong>Số tiền bằng chữ:</strong> <?= Html::encode($soTienBangChu) ?>
  </div>

  <div><span style="text-decoration: underline;"><i>Nội dung chi tiết:</i></span></div>

  <table class="table">
    <thead>
      <tr>
        <th>Ngày</th>
        <th>Nội dung điều trị</th>
        <th>Phí điều trị</th>
        <th>Thanh toán</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td style="text-align:center">
            <?= $r->ngay_dieu_tri ? date('d/m/Y', strtotime($r->ngay_dieu_tri)) : '' ?>
          </td>
          <td>
            <?php
            $parts = [];
            if (method_exists($r, 'getDvItems')) {
              foreach (($r->dvItems ?? []) as $dv) {
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
            echo $parts ? implode('<br>', $parts) : '<span>—</span>';
            ?>
          </td>
          
          <td style="text-align:right"><?= number_format((int)$r->phi, 0, '.', ',') ?></td>
          <td style="text-align:right"><?= number_format((int)$r->tam_thu, 0, '.', ',') ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="sign">
    <div class="col">
      <strong>KHÁCH HÀNG</strong><br>
      <span class="hint">(Ký, ghi rõ họ tên)</span>
    </div>
    <div class="col">
      <strong>KẾ TOÁN</strong><br>
      <span class="hint">(Ký, ghi rõ họ tên)</span>
    </div>
  </div>

  <div class="no-print" style="margin-top:18px; text-align:right">
    <?= Html::a('Quay lại chọn', ['my-khach-hang/receipt', 'id' => $kh->id], ['class'=>'btn btn-default']) ?>
    <button onclick="window.print()" class="btn btn-primary">In</button>
  </div>
</div>
