<?php
use yii\helpers\Html;

/** @var $rows array */
/** @var $from string */
/** @var $to string */

$this->title = "Bảng lương bác sĩ ({$from} → {$to})";
?>
<h1><?= Html::encode($this->title) ?></h1>

<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>Bác sĩ</th>
      <th style="text-align:right">Tổng phí dịch vụ</th>
      <th style="text-align:right">Lương cố định</th>
      <th style="text-align:right">Lương kinh doanh</th>
      <th style="text-align:right">Tổng lương</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $r): $bs = $r['bs']; $b = $r['breakdown']; ?>
      <tr>
        <td><?= Html::encode($bs->ho_ten) ?> (<?= (float)$bs->ty_le_hoa_hong ?>%)</td>
        <td style="text-align:right"><?= number_format($b['tong_phi_dich_vu'] ?? $b['tong_tam_thu']) ?></td>
        <td style="text-align:right"><?= number_format($b['luong_co_dinh']) ?></td>
        <td style="text-align:right"><?= number_format($b['luong_kinh_doanh']) ?></td>
        <td style="text-align:right"><b><?= number_format($b['tong_luong']) ?></b></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
