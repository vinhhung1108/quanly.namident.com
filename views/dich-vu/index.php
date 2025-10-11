<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $searchModel app\models\DichVuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Danh sách dịch vụ';
$this->params['breadcrumbs'][] = $this->title;
$fmtVnd = fn($n)=> number_format((int)$n, 0, '.', ',') . ' ₫';
?>
<div class="dv-index">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:12px">
    <h1 style="margin:0;font-size:20px"><?= Html::encode($this->title) ?></h1>
    <div style="display:flex;gap:8px;align-items:center">
      <form method="get" action="" class="form-inline" style="display:flex;gap:6px">
        <input type="text" name="DichVuSearch[q]" value="<?= Html::encode($searchModel->q) ?>"
               class="form-control" placeholder="Tìm theo tên" style="min-width:240px">
        <button class="btn btn-default">Tìm</button>
        <a href="" class="btn btn-link">Xoá lọc</a>
      </form>
      <?= Html::a('Thêm dịch vụ', ['create'], ['class'=>'btn btn-success']) ?>
    </div>
  </div>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'tableOptions' => ['class'=>'table table-striped table-bordered table-hover'],
    'columns' => [
      ['class' => 'yii\grid\SerialColumn'],

      [
        'attribute' => 'ten',
        'label'     => 'Tên dịch vụ',
        'format'    => 'raw',
        'value'     => fn($m)=> Html::a(Html::encode($m->ten), ['view','id'=>$m->id]),
      ],
      [
        'attribute' => 'don_gia',
        'label' => 'Đơn giá',
        'value' => fn($m)=> $fmtVnd($m->don_gia),
        'contentOptions' => ['style'=>'text-align:right;white-space:nowrap;width:140px'],
        'headerOptions'  => ['style'=>'text-align:right;width:140px'],
        'filter' => false,
      ],
      [
        'attribute' => 'active',
        'label' => 'Trạng thái',
        'format' => 'raw',
        'value' => fn($m)=> $m->active
            ? '<span class="label label-success">Đang dùng</span>'
            : '<span class="label label-default">Ẩn</span>',
        'filter' => Html::activeDropDownList(
            $searchModel, 'active', ['1'=>'Đang dùng','0'=>'Ẩn'],
            ['class'=>'form-control','prompt'=>'— Tất cả —']
        ),
        'contentOptions' => ['style'=>'white-space:nowrap;text-align:center;width:120px'],
        'headerOptions'  => ['style'=>'white-space:nowrap;text-align:center;width:120px'],
      ],

      [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view} {update} {delete}',
        'contentOptions' => ['style'=>'white-space:nowrap;text-align:center;width:90px'],
        'headerOptions'  => ['style'=>'white-space:nowrap;text-align:center;width:90px'],
      ],
    ],
    'summary' => '<div style="margin:6px 0;color:#6b7280">Hiển thị {begin}–{end} / {totalCount} dịch vụ</div>',
    'emptyText' => 'Chưa có dịch vụ.',
  ]) ?>
</div>
