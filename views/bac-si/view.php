<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\bootstrap\Modal;

/**
 * @var $this yii\web\View
 * @var $model app\models\BacSi
 * @var $from string
 * @var $to string
 * @var $luongCoDinh int
 * @var $luongKinhDoanh int
 * @var $tongLuong int
 * @var $rowsProvider yii\data\ActiveDataProvider
 * @var $hoaHongProvider yii\data\ActiveDataProvider
 * @var $sumTamThu int|null
 * @var $print bool
 */

$this->title = 'Bác sĩ: ' . Html::encode($model->ho_ten);
$this->params['breadcrumbs'][] = ['label' => 'Bác sĩ', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->ho_ten;

$fmtVnd = fn($n) => number_format((int)$n, 0, '.', ',') . ' ₫';

$this->registerCss(<<<CSS
.bs-view .wrap > .container,
.bs-view .container{max-width:100%!important;width:100%!important;padding:0 12px;}
.bs-view .card{background:#fff;border:1px solid #e5e7eb;border-radius:14px}
.bs-view .card + .card{margin-top:18px}
.bs-view .card-body{padding:18px}
.bs-view .card-title{font-size:18px;font-weight:600;margin:0 0 12px}
.bs-view .subtle{color:#6b7280;font-size:13px}
.bs-view .sum-grid{display:grid;grid-template-columns:repeat(3,minmax(160px,1fr));gap:10px}
.bs-view .sum-item{border:1px solid #eef0f2;border-radius:10px;padding:10px;display:flex;justify-content:space-between;align-items:center}
.bs-view .sum-item .k{font-size:12px;color:#6b7280}
.bs-view .sum-item .v{font-weight:700}
.bs-view .sum-item.total{border-color:#94a3b8;background:#f8fafc}
.bs-view .table-card .tbl-head{padding:14px 18px;border-bottom:1px solid #eef0f2;display:flex;justify-content:space-between;align-items:center}
.bs-view .table-card .tbl-body{padding:12px 18px}
@media (max-width:860px){ .bs-view .sum-grid{grid-template-columns:repeat(2,minmax(160px,1fr))} }
@media (max-width:620px){ .bs-view .sum-grid{grid-template-columns:1fr} }
CSS);

if ($print) {
    $this->registerCss(<<<'CSS'
@media print {
  .no-print {display:none!important;}
  body{background:#fff;}
  .bs-view .card{border:none;box-shadow:none;}
  .bs-view .card + .card{margin-top:12px;}
  .bs-view .card-body{padding:12px 0;}
  .bs-view .table-card .tbl-body{padding:0;}
  .bs-view .sum-grid{grid-template-columns:repeat(3,minmax(160px,1fr));}
}
CSS);
    $this->registerJs(<<<JS
window.print();
window.addEventListener('afterprint', function(){ window.close(); });
setTimeout(function(){ window.close(); }, 0);
JS);
}
?>

<div class="bs-view page-wrap">
  <?php if (!$print): ?>
    <?php
    $modalId = 'hh-config-modal';
    Modal::begin([
        'id' => $modalId,
        'header' => '<h4 class="modal-title"></h4>',
        'closeButton' => ['label' => '×','class' => 'close'],
        'options' => ['tabindex' => false],
        'clientOptions' => ['backdrop' => 'static', 'keyboard' => true],
    ]);
    echo '<div class="modal-body"></div>';
    Modal::end();

    $loadingHtml = Html::tag(
        'div',
        Html::tag('span', '', ['class' => 'glyphicon glyphicon-refresh glyphicon-spin']) .
        Html::tag('div', 'Đang tải...', ['style' => 'margin-top:8px']),
        ['style' => 'padding:20px;text-align:center;color:#555;']
    );

    $this->registerJs(<<<JS
    const modal = $('#{$modalId}');
    $(document).on('click', '[data-hh-modal="open"]', function(e){
      e.preventDefault();
      const trigger = $(this);
      const url = trigger.data('url');
      const title = trigger.data('title') || 'Cấu hình hoa hồng';
      modal.find('.modal-title').text(title);
      modal.find('.modal-body').html('{$loadingHtml}');
      modal.modal('show');
      $.get(url)
        .done(function(res){ modal.find('.modal-body').html(res); })
        .fail(function(){
          modal.find('.modal-body').html('<div class="alert alert-danger" role="alert">Không tải được nội dung. Vui lòng thử lại.</div>');
        });
    });
    JS);
    ?>
  <?php endif; ?>

  <!-- HEADER -->
  <div class="card">
    <div class="card-body" style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between">
      <div>
        <div class="card-title" style="margin-bottom:4px"><?= Html::encode($this->title) ?></div>
        <div class="subtle no-print">
          <?= Html::a('Sửa thông tin bác sĩ',['bac-si/update','id'=>$model->id]) ?>
        </div>
        <div class="subtle">
          Kỳ lương:
          <b><?= Html::encode(Yii::$app->formatter->asDate($from, 'php:d/m/Y')) ?></b>
          →
          <b><?= Html::encode(Yii::$app->formatter->asDate($to, 'php:d/m/Y')) ?></b>
        </div>
      </div>
      <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
        <?php if (!$print): ?>
          <?php
            $prevStart = (new DateTime('first day of last month'))->format('Y-m-d');
            $prevEnd   = (new DateTime('last day of last month'))->format('Y-m-d');
            $thisStart = (new DateTime('first day of this month'))->format('Y-m-d');
            $thisEnd   = (new DateTime('last day of this month'))->format('Y-m-d');
          ?>
          <form method="get" action="" class="form-inline no-print" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
            <input type="hidden" name="id" value="<?= (int)$model->id ?>">
            <input type="date" name="from" value="<?= Html::encode($from) ?>" class="form-control" style="min-width:160px">
            <input type="date" name="to"   value="<?= Html::encode($to)   ?>" class="form-control" style="min-width:160px">
            <button class="btn btn-primary">Lọc</button>
            <?= Html::a('Tháng trước', ['view','id'=>$model->id,'from'=>$prevStart,'to'=>$prevEnd], ['class'=>'btn btn-default','data-pjax'=>'0']) ?>
            <?= Html::a('Tháng này',   ['view','id'=>$model->id,'from'=>$thisStart,'to'=>$thisEnd], ['class'=>'btn btn-default','data-pjax'=>'0']) ?>
          </form>
        <?php endif; ?>
        <?= Html::a(
              'In phiếu lương',
              Url::to(['view','id'=>$model->id,'from'=>$from,'to'=>$to,'print'=>1]),
              ['class'=>'btn btn-warning no-print','target'=>'_blank','data-pjax'=>'0']
        ) ?>
      </div>
    </div>

    <!-- SUMMARY -->
    <div class="card-body">
      <div class="sum-grid">
        <div class="sum-item"><div class="k">Lương cố định</div><div class="v"><?= $fmtVnd($luongCoDinh) ?></div></div>
        <div class="sum-item"><div class="k">Lương kinh doanh</div><div class="v"><?= $fmtVnd($luongKinhDoanh) ?></div></div>
        <div class="sum-item total"><div class="k">TỔNG LƯƠNG</div><div class="v"><?= $fmtVnd($tongLuong) ?></div></div>
        <?php if (isset($sumTamThu)): ?>
          <div class="sum-item" style="grid-column:1/-1;justify-content:flex-start;gap:10px">
            <div class="k">Tổng tạm thu DV do bác sĩ phụ trách trong kỳ</div>
            <div class="v"><?= $fmtVnd($sumTamThu) ?></div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- CẤU HÌNH HOA HỒNG -->
  <?php if (!$print): ?>
  <div class="card table-card no-print">
    <div class="tbl-head">
      <div>
        <div class="card-title" style="margin:0">Cấu hình hoa hồng theo dịch vụ</div>
        <div class="subtle">Tỷ lệ riêng sẽ được áp dụng thay cho tỷ lệ mặc định <?= Html::encode(rtrim(rtrim(number_format((float)$model->ty_le_hoa_hong, 2, '.', ''), '0'), '.')) ?>%.</div>
      </div>
      <?= Html::button(
            'Thêm cấu hình',
            [
              'type'=>'button',
              'class'=>'btn btn-primary',
              'data-hh-modal'=>'open',
              'data-url'=>Url::to(['bac-si-hoa-hong/create', 'bsId' => (int)$model->id]),
              'data-title'=>' ',
            ]
      ) ?>
    </div>
    <div class="tbl-body" style="overflow-x:auto">
      <?= GridView::widget([
        'dataProvider' => $hoaHongProvider,
        'emptyText'    => 'Chưa có tỷ lệ hoa hồng riêng cho bác sĩ này.',
        'tableOptions' => ['class'=>'table table-hover', 'style'=>'min-width:520px'],
        'columns' => [
          ['class'=>'yii\grid\SerialColumn'],
          [
            'label' => 'Dịch vụ',
            'value' => function($model){
              /** @var \app\models\BacSiHoaHong $model */
              return $model->dichVu ? $model->dichVu->ten : '—';
            },
            'contentOptions' => ['style'=>'white-space:normal;max-width:280px'],
          ],
          [
            'attribute' => 'ty_le',
            'label' => 'Tỷ lệ (%)',
            'value' => function($model){
              $tyLe = (float)$model->ty_le;
              $formatted = rtrim(rtrim(number_format($tyLe, 2, '.', ''), '0'), '.');
              return $formatted === '' ? '0' : $formatted;
            },
            'contentOptions' => ['style'=>'text-align:center;white-space:nowrap'],
          ],
          [
            'attribute' => 'updated_at',
            'label' => 'Cập nhật',
            'value' => fn($model) => Yii::$app->formatter->asDatetime($model->updated_at),
            'contentOptions' => ['style'=>'white-space:nowrap'],
          ],
          [
            'class' => 'yii\grid\ActionColumn',
            'controller' => 'bac-si-hoa-hong',
            'template' => '{update} {delete}',
            'buttons' => [
              'update' => fn($url, $m) => Html::a(
                  '<span class="glyphicon glyphicon-pencil"></span>',
                  '#',
                  [
                    'class'=>'btn-link',
                    'title'=>'Sửa',
                    'aria-label'=>'Sửa',
                    'data-hh-modal'=>'open',
                    'data-url'=>Url::to(['bac-si-hoa-hong/update', 'id' => $m->id]),
                    'data-title'=>'Cập nhật hoa hồng dịch vụ',
                  ]
              ),
              'delete' => fn($url, $m) => Html::a(
                  '<span class="glyphicon glyphicon-trash"></span>',
                  ['bac-si-hoa-hong/delete', 'id' => $m->id],
                  [
                    'title' => 'Xoá',
                    'aria-label' => 'Xoá',
                    'class' => 'text-danger',
                    'data' => [
                      'confirm' => 'Xoá cấu hình hoa hồng này?',
                      'method'  => 'post',
                    ],
                    'data-pjax' => '0',
                  ]
              ),
            ],
            'contentOptions' => ['style'=>'text-align:center;white-space:nowrap;width:90px'],
            'headerOptions'  => ['style'=>'text-align:center;white-space:nowrap'],
          ],
        ],
        'summary' => '<span class="subtle">Hiển thị {count} cấu hình</span>',
      ]) ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- DANH SÁCH ĐIỀU TRỊ (hiển thị theo DV của bác sĩ, tính trên TẠM THU) -->
  <div class="card table-card">
    <div class="tbl-head">
      <div class="card-title" style="margin:0">Danh sách điều trị trong kỳ</div>
      <div class="subtle">
        <?= Html::encode(Yii::$app->formatter->asDate($from, 'php:d/m/Y')) ?> ·
        <?= Html::encode(Yii::$app->formatter->asDate($to, 'php:d/m/Y')) ?>
      </div>
    </div>

    <div class="tbl-body">
      <?= GridView::widget([
        'dataProvider' => $rowsProvider,
        'tableOptions' => ['class'=>'table table-hover'],
        'columns' => [
          ['class'=>'yii\grid\SerialColumn'],
          [
            'attribute'=>'ngay_dieu_tri',
            'format'=>['date','php:d/m/Y'],
            'contentOptions'=>['style'=>'white-space:nowrap;'],
          ],
          [
            'attribute'=>'gio',
            'contentOptions'=>['style'=>'white-space:nowrap;'],
          ],
          [
            'label' => 'Nội dung',
            'format' => 'raw',
            'value' => function($m) use ($model){
              $parts = [];
              if (method_exists($m, 'getDvItems')) {
                foreach (($m->dvItems ?? []) as $dv) {
                  $dvBsId  = (int)($dv->bs_id ?? 0);
                  $belongs = $dvBsId === (int)$model->id
                      || ($dvBsId === 0 && (int)($m->bs_id ?? 0) === (int)$model->id);
                  if (!$belongs) continue;

                  $name = $dv->ten_dv ?: ($dv->dichVu->ten ?? '');
                  $meta = [];
                  if ((int)$dv->so_luong > 1) $meta[] = 'x' . (int)$dv->so_luong;
                  if (!empty($dv->rang_so))   $meta[] = 'răng ' . $dv->rang_so;

                  $line = trim($name);
                  if ($meta) $line .= ' (' . implode(' · ', $meta) . ')';
                  if ($line !== '') $parts[] = \yii\helpers\Html::encode($line);
                }
              }
              if (!empty($m->noi_dung)) {
                foreach (preg_split("/\\r?\\n/", trim((string)$m->noi_dung)) as $row) {
                  $row = trim($row);
                  if ($row !== '') $parts[] = \yii\helpers\Html::encode($row);
                }
              }
              if (!$parts) return '<span class="text-muted">—</span>';
              return '<div style="white-space:normal;max-width:520px">'.implode('<br>', $parts).'</div>';
            },
            'contentOptions'=>['style'=>'white-space:normal;max-width:520px'],
          ],
          [
            'label'=>'Tạm thu (từng DV)',
            'format'=>'raw',
            'value'=> function($m) use ($model){
              $lines = [];
              if (method_exists($m, 'getDvItems')) {
                foreach (($m->dvItems ?? []) as $dv) {
                  $dvBsId = (int)($dv->bs_id ?? 0);
                  $belongs = $dvBsId === (int)$model->id
                      || ($dvBsId === 0 && (int)($m->bs_id ?? 0) === (int)$model->id);
                  if (!$belongs) continue;

                  $val = (int)($dv->tam_thu ?? 0);
                  $lines[] = number_format($val, 0, '.', ',') . ' ₫';
                }
              }
              return $lines ? implode('<br>', $lines) : '<span class="text-muted">—</span>';
            },
            'contentOptions' => ['style' => 'white-space:nowrap; text-align:right;'],
            'headerOptions'  => ['style' => 'white-space:nowrap;text-align:right;'],
          ],
          [
            'label'=>'Tỷ lệ hoa hồng',
            'format'=>'raw',
            'value'=> function($m) use ($model){
              $lines = [];
              if (method_exists($m, 'getDvItems')) {
                foreach (($m->dvItems ?? []) as $dv) {
                  $dvBsId = (int)($dv->bs_id ?? 0);
                  $belongs = $dvBsId === (int)$model->id
                      || ($dvBsId === 0 && (int)($m->bs_id ?? 0) === (int)$model->id);
                  if (!$belongs) continue;

                  $tyLe = $model->getTyLeHoaHongForService($dv->dich_vu_id ? (int)$dv->dich_vu_id : null);
                  $formatted = rtrim(rtrim(number_format((float)$tyLe, 2, '.', ''), '0'), '.');
                  $lines[] = ($formatted === '' ? '0' : $formatted) . '%';
                }
              }
              return $lines ? implode('<br>', $lines) : '<span class="text-muted">—</span>';
            },
            'contentOptions' => ['style' => 'text-align:center;white-space:nowrap;'],
            'headerOptions'  => ['style' => 'text-align:center;white-space:nowrap;'],
          ],
          [
            'label'=>'Hoa hồng',
            'format'=>'raw',
            'value'=> function($m) use ($model){
              $lines = [];
              if (method_exists($m, 'getDvItems')) {
                foreach (($m->dvItems ?? []) as $dv) {
                  $dvBsId = (int)($dv->bs_id ?? 0);
                  $belongs = $dvBsId === (int)$model->id
                      || ($dvBsId === 0 && (int)($m->bs_id ?? 0) === (int)$model->id);
                  if (!$belongs) continue;

                  $tamThu = (int)($dv->tam_thu ?? 0);
                  $tyLe   = $model->getTyLeHoaHongForService($dv->dich_vu_id ? (int)$dv->dich_vu_id : null);
                  $hoa    = (int)round($tamThu * ($tyLe/100));
                  $lines[] = number_format($hoa, 0, '.', ',') . ' ₫';
                }
              }
              return $lines ? implode('<br>', $lines) : '<span class="text-muted">—</span>';
            },
            'contentOptions' => ['style' => 'text-align:right;white-space:nowrap;'],
            'headerOptions'  => ['style' => 'text-align:right;white-space:nowrap;'],
          ],
          [
            'class' => 'yii\grid\ActionColumn',
            'controller' => 'my-dieu-tri',
            'template' => '{view}',
            'visible' => !$print,
            'buttons' => [
              'view' => function ($url, $m) {
                  return Html::a(
                      Html::tag('span', '', ['class' => 'glyphicon glyphicon-eye-open']),
                      ['/my-khach-hang/view', 'id' => (int)$m->id_kh],
                      [
                        'class' => 'btn-link',
                        'title' => 'Xem chi tiết KH',
                        'aria-label' => 'Xem chi tiết',
                        'data-pjax' => '0',
                      ]
                  );
              },
            ],
            'contentOptions' => ['style'=>'white-space:nowrap;text-align:center;width:90px;max-width:90px;'],
            'headerOptions'  => ['style'=>'white-space:nowrap;text-align:center;'],
          ],
        ],
        'summary' => '<span class="subtle">Hiển thị {begin}–{end} / {totalCount} dòng</span>',
        'pager' => ['firstPageLabel' => '«','lastPageLabel'  => '»','maxButtonCount' => 5],
      ]) ?>
      <div class="subtle" style="margin-top:6px">
        * Số liệu hoa hồng tính trên <b>tạm thu từng dịch vụ</b>.
      </div>
    </div>
  </div>
</div>
