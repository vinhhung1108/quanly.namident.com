<?php
use yii\helpers\Html;

/** @var $kh app\models\KhachHang */
/** @var $nhaKhoa app\models\NhaKhoa|null */
/** @var $rows app\models\DieuTri[] */
/** @var $today DateTime */
/** @var $tong_phi int */
/** @var $thanh_toan int */
/** @var $con_lai int */
/** @var $soTienBangChu string */

$this->title = 'Phiếu thu - ' . Html::encode($kh->ho_ten);
?>
<style>
.receipt-wrap { max-width: 900px; margin: 0 auto; font-size: 13px; line-height: 1.45; }
.header { display:flex; justify-content:space-between; align-items:flex-start; }
.header .clinic { font-weight: 600; }
.title { text-align:center; font-size: 20px; font-weight:700; margin: 10px 0 4px; }
.meta { text-align:center; margin-bottom:12px; }
.table { width:100%; border-collapse: collapse; }
.table th, .table td { border:1px solid #000; padding:6px;}
.table > caption + thead > tr:first-child > th, .table > colgroup + thead > tr:first-child > th, .table > thead:first-child > tr:first-child > th, .table > caption + thead > tr:first-child > td, .table > colgroup + thead > tr:first-child > td, .table > thead:first-child > tr:first-child > td { border-top: 1px solid #000; }
.table > thead > tr > th {
    border-bottom: 1px solid #000;
}
.table th { text-align:center; }
.summary { margin: 10px 0; }
.sign { display:flex; justify-content:space-between; margin-top: 30px; }
.sign .col { width: 45%; text-align:center; }
.hint { font-style: italic; opacity: .8; font-size: 12px; }

@page { size: A4 portrait; margin: 12mm; }

/* Quy tắc in */
@media print {
  .no-print { display:none !important; }

  /* Cho in: bỏ giới hạn 900px để không bị ép co */
  .receipt-wrap { max-width: none; }

  /* Cố định layout bảng để width cột có hiệu lực */
  .table { table-layout: fixed; width: 100%; }

  /* Ấn định bề rộng theo thứ tự cột:
    1: Ngày, 2: Nội dung, 3: Bác sĩ, 4: Phí, 5: Thanh toán, 6: HTTT */
  .table th:nth-child(1), .table td:nth-child(1) { width: 22mm; text-align: center; }
  .table th:nth-child(3), .table td:nth-child(3) { width: 24mm; }
  .table th:nth-child(4), .table td:nth-child(4) { width: 26mm; text-align: center; }
  .table th:nth-child(5), .table td:nth-child(5) { width: 26mm; text-align: center; }
  /* .table th:nth-child(6), .table td:nth-child(6) { width: 18mm; text-align: center; } */

  /* Cột “Nội dung điều trị” giữ phần còn lại, cho phép xuống dòng tốt */
  .table th:nth-child(2), .table td:nth-child(2) { width: auto; }
  .table td:nth-child(2) {
    white-space: normal;
    word-break: break-word;
    overflow-wrap: anywhere;
  }
}

</style>

<div class="receipt-wrap">
  <div class="header">
    <div class="clinic">
      <?php if ($nhaKhoa && $nhaKhoa->logo): ?>
        <img src="<?= Yii::$app->request->baseUrl . $nhaKhoa->logo ?>" alt="Logo" style="height:48px;">
        <br>
      <?php endif; ?>
      <?= $nhaKhoa ? Html::encode($nhaKhoa->ten_nha_khoa) : 'NHA KHOA' ?><br>
      <?= $nhaKhoa ? Html::encode($nhaKhoa->dia_chi) : '' ?><br>
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

  <p><strong>Họ tên KH:</strong> <?= Html::encode($kh->ho_ten) ?> &nbsp; | &nbsp;
     <strong>SĐT:</strong> <?= Html::encode($kh->sdt) ?></p>
  <p><strong>Hẹn tái khám:</strong>
     <?= $kh->ngay_hen ? Yii::$app->formatter->asDate($kh->ngay_hen, 'php:d/m/Y') : '...' ?>,
     <?= Html::encode($kh->noi_dung_hen) ?></p>

  <div class="summary">
    <strong>Tổng tiền:</strong> <?= number_format($tong_phi) ?> đ &nbsp;&nbsp;
    <strong>Thanh toán:</strong> <?= number_format($thanh_toan) ?> đ &nbsp;&nbsp;
    <strong>Còn lại:</strong> <?= number_format($con_lai) ?> đ
    <br>
    <strong>Số tiền bằng chữ:</strong> <?= Html::encode($soTienBangChu) ?>
  </div>
  <div>
    <h5 style="text-decoration: underline;"><i>Nội dung chi tiết:</i></h5>
  </div>
  <table class="table">
    <thead>
      <tr>
        <th>Ngày</th>
        <th>Nội dung điều trị</th>
        <th>Bác sĩ</th>
        <th>Phí điều trị</th>
        <th>Thanh toán</th>
        <!-- <th style="width:80px">HTTT</th> -->
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td style="text-align:center"><?= $r->ngay_dieu_tri ? date('d/m/Y', strtotime($r->ngay_dieu_tri)) : '' ?></td>
          <td><?= nl2br(Html::encode($r->noi_dung)) ?></td>
          <td><?= Html::encode($r->bs) ?></td>
          <td style="text-align:right"><?= number_format((int)$r->phi) ?></td>
          <td style="text-align:right"><?= number_format((int)$r->tam_thu) ?></td>
          <!-- <td style="text-align:center"><?= Html::encode($r->hinh_thuc_thanh_toan) ?></td> -->
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
      <strong>BÁC SĨ ĐIỀU TRỊ</strong><br>
      <span class="hint">(Ký, ghi rõ họ tên)</span>
    </div>
  </div>

  <div class="no-print" style="margin-top:18px; text-align:right">
    <?= Html::a('Quay lại chọn', ['khach-hang/receipt', 'id' => $kh->id], ['class'=>'btn btn-default']) ?>
    <button onclick="window.print()" class="btn btn-primary">In</button>
  </div>
</div>
