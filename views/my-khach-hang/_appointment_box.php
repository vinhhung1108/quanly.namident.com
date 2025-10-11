<?php
use yii\helpers\Html;

/** @var $model app\models\MyKhachHang */

$daysBadge = '';
if (!empty($model->ngay_hen)) {
    try {
        $today = new DateTime('today');
        $hen   = new DateTime($model->ngay_hen);
        $diff  = (int)$today->diff($hen)->format('%r%a');
        if ($diff > 0) {
            $daysBadge = "<span class='pill pill-info'>Còn {$diff} ngày</span>";
        } elseif ($diff === 0) {
            $daysBadge = "<span class='pill pill-warning'>Hôm nay</span>";
        } else {
            $daysBadge = "<span class='pill pill-danger'>Trễ " . abs($diff) . " ngày</span>";
        }
    } catch (\Throwable $e) {}
}

$doctorName = $model->bs_dieu_tri ?: null;
?>

<?php if (!empty($model->ngay_hen) || !empty($model->noi_dung_hen) || !empty($model->gio_hen) || !empty($doctorName)): ?>
  <div class="appointment" style="margin:12px 0 6px">
    <div>
      <div class="title">
        <span class="icon">🗓</span>
        Lịch hẹn tái khám: 
        <?= $daysBadge ?>
      </div>
      <div class="subtle" style="display:flex;flex-wrap:wrap;gap:12px">
        <span><b>Ngày:</b> <?= $model->ngay_hen ? Yii::$app->formatter->asDate($model->ngay_hen, 'php:d/m/Y') : '—' ?></span>
        <span><b>Giờ:</b> <?= $model->gio_hen ?: '—' ?></span>
        <span><b>Bác sĩ:</b> <?= $doctorName ? Html::encode($doctorName) : '—' ?></span>
      </div>
      <div style="margin-top:6px">
        <b>Nội dung:</b>
        <?= $model->noi_dung_hen ? nl2br(Html::encode($model->noi_dung_hen)) : '<span class="subtle">—</span>' ?>
      </div>
    </div>
  </div>
<?php else: ?>
  <div class="subtle" style="margin:12px 0 6px">Chưa có lịch hẹn.</div>
<?php endif; ?>
