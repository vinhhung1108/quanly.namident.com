<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\NhaKhoa */

$this->title = $model->ten_nha_khoa;
$this->params['breadcrumbs'][] = ['label' => 'Nha Khoa', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="nha-khoa-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'id',
            'ten_nha_khoa',
            'dia_chi',
            'so_dien_thoai',
            'ma_so_thue',
            // 'created_at',
            // 'updated_at',
        ],
    ]) ?>
  <div>
    <h5>Logo: </h5>
    <?php if ($model->logo): ?>
      <img src="<?= $model->getLogoUrl() ?>" alt="Logo" style="height:80px;">
    <?php endif; ?>
  </div>
</div>
