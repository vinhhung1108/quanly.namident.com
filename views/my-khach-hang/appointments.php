<?php
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $from string */
/* @var $to string */
/* @var $q string */

$this->title = 'Lịch hẹn tái khám';
$this->params['breadcrumbs'][] = $this->title;

// helper ngày còn/ trễ
$daysBadge = function($date) {
    if (!$date) return '—';
    try {
        $today = new DateTime('today');
        $d     = new DateTime($date);
        $diff  = (int)$today->diff($d)->format('%r%a');
        if ($diff > 0)  return "<span class='pill pill-info'>Còn {$diff} ngày</span>";
        if ($diff === 0) return "<span class='pill pill-warning'>Hôm nay</span>";
        return "<span class='pill pill-danger'>Trễ ".abs($diff)." ngày</span>";
    } catch (\Throwable $e) { return '—'; }
};

$this->registerCss(<<<CSS
.page{max-width:1100px}
.card{background:#fff;border:1px solid #e5e7eb;border-radius:12px}
.card + .card{margin-top:16px}
.card-body{padding:14px 16px}
.card-title{font-weight:600;margin:0 0 10px;font-size:18px}
.toolbar{display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end}
.toolbar .form-group{margin:0}
.toolbar input[type=date], .toolbar input[type=text]{height:34px;padding:6px 10px}
.pill{display:inline-block;padding:3px 8px;border-radius:999px;font-size:12px}
.pill-info{background:#eff6ff;color:#1d4ed8}
.pill-warning{background:#fff7ed;color:#b45309}
.pill-danger{background:#fef2f2;color:#991b1b}
.small{color:#6b7280;font-size:12px}
.phone a{white-space:nowrap;text-decoration:none}
.phone a:hover{text-decoration:underline}
CSS);
?>

<div class="page">
  <div class="card">
    <div class="card-body">
      <div class="card-title">Bộ lọc</div>
      <form class="toolbar" method="get" action="">
        <div class="form-group">
          <label>From</label><br>
          <input type="date" name="from" value="<?= Html::encode($from) ?>">
        </div>
        <div class="form-group">
          <label>To</label><br>
          <input type="date" name="to" value="<?= Html::encode($to) ?>">
        </div>
        <div class="form-group" style="flex:1;min-width:220px">
          <label>Tìm kiếm</label><br>
          <input type="text" name="q" value="<?= Html::encode($q) ?>" placeholder="Họ tên / SĐT / Mã KH / Bác sĩ" style="width:100%">
        </div>
        <div class="form-group">
          <br>
          <button class="btn btn-primary">Lọc</button>
          <a class="btn btn-default" href="<?= Html::encode(\Yii::$app->urlManager->createUrl(['my-khach-hang/appointments'])) ?>">Xoá lọc</a>
        </div>
      </form>
      <div class="small" style="margin-top:6px">
        Mặc định hiển thị từ hôm nay đến 30 ngày tới.
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="card-title" style="margin-bottom:8px">Danh sách lịch hẹn</div>
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class'=>'table table-hover'],
        'columns' => [
          ['class'=>'yii\grid\SerialColumn'],
          [
            'attribute' => 'ngay_hen',
            'label' => 'Ngày',
            'format' => ['date','php:d/m/Y'],
            'contentOptions'=>['style'=>'white-space:nowrap;'],
          ],
          [
            'attribute' => 'gio_hen',
            'label' => 'Giờ',
            'contentOptions'=>['style'=>'white-space:nowrap;'],
          ],
          [
            'attribute' => 'ho_ten',
            'label' => 'Khách hàng',
            'format' => 'raw',
            'value' => function($m){
              return Html::a(Html::encode($m->ho_ten), ['my-khach-hang/view','id'=>$m->id]);
            }
          ],
          [
            'attribute' => 'sdt',
            'label' => 'SĐT',
            'format' => 'raw',
            'contentOptions'=>['class'=>'phone', 'style'=>'white-space:nowrap;'],
            'value' => function($m){
              if (!$m->sdt) return '—';
              $clean = preg_replace('/\D+/', '', $m->sdt); // chỉ lấy số để tạo tel:
              return Html::a(Html::encode($m->sdt), 'tel:' . $clean, [
                  'aria-label' => 'Gọi ' . $m->sdt,
                  'title' => 'Gọi ' . $m->sdt,
              ]);
            }
          ],
          [
            'attribute' => 'bs_dieu_tri',
            'label' => 'Bác sĩ',
            'contentOptions'=>['style'=>'min-width:120px;'],
          ],
          [
            'attribute' => 'noi_dung_hen',
            'label' => 'Nội dung',
            'format' => 'ntext',
            'contentOptions'=>['style'=>'max-width:380px;white-space:normal;'],
          ],
          [
            'label' => 'Tình trạng',
            'format' => 'raw',
            'value' => fn($m) => $daysBadge($m->ngay_hen),
            'contentOptions'=>['style'=>'white-space:nowrap;text-align:center'],
          ],
          [
            'class' => 'yii\grid\ActionColumn',
            'controller' => 'my-khach-hang',
            'template' => '{view}',
            'buttons' => [
              'view' => fn($url,$m)=> Html::a('Xem', ['my-khach-hang/view','id'=>$m->id], ['class'=>'btn btn-xs btn-default']),
              'update'=> fn($url,$m)=> Html::a('Sửa', ['my-khach-hang/update','id'=>$m->id], ['class'=>'btn btn-xs btn-primary']),
            ],
          ],
        ],
        'summary' => '<span class="small">Hiển thị {begin}–{end} / {totalCount} lịch hẹn</span>',
        'pager' => [
          'firstPageLabel' => '«',
          'lastPageLabel'  => '»',
          'maxButtonCount' => 5,
        ],
      ]) ?>
    </div>
  </div>
</div>
