<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $model app\models\DieuTri */
/* @var $kh app\models\KhachHang */
/* @var $imagesForm app\models\DieuTriImage */
$this->title = 'Thêm điều trị: ' . ($kh->ho_ten ?? '');
$this->params['breadcrumbs'][] = ['label' => 'Danh Sách Điều Trị', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss(<<<CSS
.page-wrap{max-width:1100px; padding-bottom:100px;}
.card{background:#fff;border:1px solid #e5e7eb;border-radius:14px; margin-top:20px;}
.card + .card{margin-top:18px}
.card-body{padding:18px}
.card-title{font-size:18px;font-weight:600;margin:0 0 12px}

/* Header */
.header{display:flex;align-items:center;justify-content:space-between;padding:16px 18px;border-bottom:1px solid #eef0f2}
.header-left{display:flex;align-items:center;gap:14px}
.avatar{width:48px;height:48px;border-radius:12px;background:#eef2ff;display:flex;align-items:center;justify-content:center;font-weight:700}
.subtle{color:#6b7280;font-size:13px}
.btns .btn{margin-left:6px}

/* Stats */
.stats{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px}
.stat{flex:1;min-width:180px;border:1px solid #eef0f2;border-radius:12px;padding:12px}
.stat .k{font-size:12px;color:#6b7280;margin-bottom:4px}
.stat .v{font-size:18px;font-weight:700}
.badge{display:inline-block;padding:4px 8px;border-radius:999px;font-size:12px}
.badge-success{background:#ecfdf5;color:#065f46}
.badge-danger{background:#fef2f2;color:#991b1b}

/* Grid 2 cột */
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
@media (max-width: 768px){ .grid-2{grid-template-columns:1fr} .header{flex-direction:column;align-items:flex-start;gap:10px} }

/* Bảng điều trị */
.table-card .card-body{padding:0;}
.table-card .tbl-head{padding:14px 18px;border-bottom:1px solid #eef0f2;display:flex;justify-content:space-between;align-items:center}
.table-card .tbl-body{padding:12px 18px}

/* Hộp lịch hẹn */
.appointment{display:flex;gap:12px;align-items:flex-start;background:#f8fafc;border:1px dashed #cbd5e1;border-radius:12px;padding:12px}
.appointment .icon{width:36px;height:36px;border-radius:8px;background:#e0f2fe;display:flex;align-items:center;justify-content:center;font-weight:700}
.appointment .title{font-weight:600;margin-bottom:2px}
.pill{display:inline-block;padding:3px 8px;border-radius:999px;font-size:12px;margin-left:8px}
.pill-info{background:#eff6ff;color:#1d4ed8}
.pill-warning{background:#fff7ed;color:#b45309}
.pill-danger{background:#fef2f2;color:#991b1b}

CSS);

?>
<h3><?= Html::encode($this->title) ?></h3>
<?= $this->render('_form', compact('model','kh','imagesForm')) ?>

<?php
$fmtVnd = function($n){ return number_format((int)$n, 0, '.', ',') . ' ₫'; };
$sumPhi = (int)$dieuTriProvider->query->sum('phi');
$sumTamThu = (int)$dieuTriProvider->query->sum('tam_thu');
$conLai = (int)$kh->con_lai;
$badgeClass = $conLai > 0 ? 'badge-danger' : 'badge-success';
?>
<!-- BẢNG ĐIỀU TRỊ -->
  <div class="card table-card">
    <div class="tbl-head">
      <div class="card-title" style="margin:0">Lịch sử điều trị</div>
      <div class="subtle">
        Tổng phí: <b><?= $fmtVnd($sumPhi) ?></b> · Tạm thu: <b><?= $fmtVnd($sumTamThu) ?></b>
      </div>
    </div>
    <div class="tbl-body">
      <?= GridView::widget([
        'dataProvider' => $dieuTriProvider,
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
            'attribute'=>'bs_id',
            'label'=>'Bác sĩ',
            'value'=>fn($m)=> $m->bacSi ? $m->bacSi->ho_ten : $m->bs,
            'contentOptions'=>['style'=>'min-width:120px'],
          ],
          [
            'attribute'=>'noi_dung',
            'format'=>'ntext',
            'contentOptions'=>['style'=>'max-width:420px;white-space:normal'],
          ],
          [
            'attribute'=>'phi',
            'label'=>'Phí',
            'value'=>fn($m)=> $fmtVnd($m->phi),
            'contentOptions'=>['style'=>'text-align:right;white-space:nowrap;'],
            'headerOptions'=>['style'=>'text-align:right'],
          ],
          [
            'attribute'=>'tam_thu',
            'label'=>'Tạm thu',
            'value'=>fn($m)=> $fmtVnd($m->tam_thu),
            'contentOptions'=>['style'=>'text-align:right;white-space:nowrap;'],
            'headerOptions'=>['style'=>'text-align:right'],
          ],
          [
            'class' => 'yii\grid\ActionColumn',
            'controller' => 'my-dieu-tri',
            'template' => '{view} {update} {delete}',
          ],
        ],
        'summary' => '<span class="subtle">Hiển thị {begin}–{end} / {totalCount} dòng</span>',
        'pager' => [
          'firstPageLabel' => '«',
          'lastPageLabel'  => '»',
          'maxButtonCount' => 5,
        ],
      ]) ?>
      <div class="subtle" style="margin-top:6px">
        * Số liệu tổng tính trên toàn bộ hồ sơ (không phụ thuộc phân trang).
      </div>
    </div>
  </div>
