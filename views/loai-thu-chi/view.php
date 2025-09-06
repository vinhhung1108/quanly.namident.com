<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\LoaiThuChi */

$this->title = $model->loai_thu_chi;
$this->params['breadcrumbs'][] = ['label' => 'Loại Thu Chi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loai-thu-chi-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'loai_thu_chi',
            'ghi_chu',
        ],
    ]) ?>

</div>
