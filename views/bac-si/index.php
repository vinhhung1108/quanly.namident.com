<?php
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel app\models\BacSiSearch|null */

$this->title = 'Danh sách bác sĩ';
$this->params['breadcrumbs'][] = $this->title;

// helper tiền tệ: 1,234,567 ₫
$fmtVnd = fn($n) => number_format((int)$n, 0, '.', ',') . ' ₫';

$this->registerCss(<<<CSS
.bs-index .card{background:#fff;border:1px solid #e5e7eb;border-radius:12px}
.bs-index .card + .card{margin-top:16px}
.bs-index .card-body{padding:16px 18px}
.bs-index .header{display:flex;justify-content:space-between;align-items:center;padding:18px;border-bottom:1px solid #eef0f2;background:#f9fafb;border-top-left-radius:12px;border-top-right-radius:12px}
.bs-index .title{font-size:20px;font-weight:700;margin:0}
.bs-index .small{color:#6b7280;font-size:12px}
.bs-index .table-card .card-body{padding:0}
.bs-index .table-card .tbl-head{padding:16px 18px;border-bottom:1px solid #eef0f2;display:flex;justify-content:space-between;align-items:center}
@media (max-width:768px){
  .bs-index .header{flex-direction:column;align-items:flex-start;gap:12px}
  .bs-index .table-card .tbl-head{flex-direction:column;align-items:flex-start;gap:10px}
}
CSS);
?>

<div class="bs-index">
  <div class="card">
    <div class="header">
      <div class="title"><?= Html::encode($this->title) ?></div>
      <div>
        <?= Html::a('➕ Thêm bác sĩ', ['create'], ['class' => 'btn btn-success']) ?>
      </div>
    </div>

    <!-- <div class="card-body small">
      * Có thể gõ để tìm theo Họ tên. Bạn cũng có thể lọc theo Ngày sinh nếu dùng ô filter bên dưới tiêu đề cột.
    </div> -->
  </div>

  <div class="card table-card">
    <!-- <div class="tbl-head">
      <div class="title" style="font-size:16px;margin:0">Danh sách</div>
      <div>
        <?= Html::a('Làm mới', ['index'], ['class'=>'btn btn-default btn-sm']) ?>
      </div>
    </div> -->
    <div class="card-body">
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // Truyền $searchModel từ controller nếu muốn bật filter theo cột
        'filterModel'  => $searchModel ?? null,
        'tableOptions' => ['class'=>'table table-hover'],
        'columns' => [
          ['class' => 'yii\grid\SerialColumn'],

          [
            'attribute' => 'ho_ten',
            'label' => 'Họ tên',
            'format' => 'raw',
            'value' => function($m){
              return Html::a(Html::encode($m->ho_ten), ['view','id'=>$m->id]);
            },
            // filter text dùng mặc định nếu có $searchModel
            'contentOptions' => ['style'=>'min-width:200px'],
          ],

          [
            'attribute' => 'nam_sinh',
            'label' => 'Ngày sinh',
            'format' => ['date','php:d/m/Y'],
            // Ô filter kiểu date (nếu có $searchModel)
            'filter' => isset($searchModel) ? Html::input('date', Html::getInputName($searchModel, 'nam_sinh'), $searchModel->nam_sinh, ['class'=>'form-control']) : false,
            'contentOptions' => ['style'=>'white-space:nowrap'],
          ],

          [
            'attribute' => 'luong_co_dinh',
            'label' => 'Lương cố định',
            'value' => fn($m) => $fmtVnd($m->luong_co_dinh ?? 0),
            'contentOptions' => ['style'=>'text-align:right;white-space:nowrap'],
            'headerOptions'  => ['style'=>'text-align:right'],
            'filter' => false, // tắt filter cột tiền cho gọn
          ],

          [
            'attribute' => 'ty_le_hoa_hong',
            'label' => 'Hoa hồng (%)',
            'value' => function($m){
              // hiển thị gọn: tối đa 2 số lẻ, bỏ .00
              $p = rtrim(rtrim(number_format((float)($m->ty_le_hoa_hong ?? 0), 2, '.', ''), '0'), '.');
              return $p . ' %';
            },
            'contentOptions' => ['style'=>'text-align:right;white-space:nowrap'],
            'headerOptions'  => ['style'=>'text-align:right'],
            'filter' => false,
          ],

          [
            'class' => 'yii\grid\ActionColumn',
            'controller' => 'bac-si',
            'template' => '{view} {update} {delete}',
          ],
        ],
        'summary' => '<span class="small">Hiển thị {begin}–{end} / {totalCount} bác sĩ</span>',
        'pager' => [
          'firstPageLabel' => '«',
          'lastPageLabel'  => '»',
          'maxButtonCount' => 5,
        ],
      ]) ?>
    </div>
  </div>
</div>
