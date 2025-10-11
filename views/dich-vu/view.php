<?php
use yii\widgets\DetailView;
use yii\helpers\Html;

/* @var $model app\models\DichVu */

$this->title = 'Dịch vụ: ' . $model->ten;
$this->params['breadcrumbs'][] = ['label'=>'Danh sách dịch vụ','url'=>['index']];
$this->params['breadcrumbs'][] = $this->title;

$fmtVnd = fn($n)=> number_format((int)$n, 0, '.', ',') . ' ₫';
?>
<div class="dv-view">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
    <h1 style="margin:0;font-size:20px"><?= Html::encode($this->title) ?></h1>
    <div>
      <?= Html::a('Cập nhật', ['update','id'=>$model->id], ['class'=>'btn btn-primary']) ?>
      <?= Html::a('Xoá', ['delete','id'=>$model->id], [
        'class'=>'btn btn-danger',
        'data' => ['confirm'=>'Xoá dịch vụ này?', 'method'=>'post']
      ]) ?>
    </div>
  </div>

  <?= DetailView::widget([
    'model' => $model,
    'options' => ['class'=>'table table-striped table-bordered'],
    'attributes' => [
      'id',
      'ten',
      ['label'=>'Đơn giá', 'value'=>$fmtVnd($model->don_gia)],
      [
        'attribute'=>'active',
        'value' => $model->active ? 'Đang dùng' : 'Ẩn',
      ],
      [
        'attribute'=>'created_at',
        'format'=>['datetime','php:d/m/Y H:i'],
        'visible' => !empty($model->created_at),
      ],
      [
        'attribute'=>'updated_at',
        'format'=>['datetime','php:d/m/Y H:i'],
        'visible' => !empty($model->updated_at),
      ],
    ],
  ]) ?>
</div>
