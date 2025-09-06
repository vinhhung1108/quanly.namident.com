<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\LoaiThuChi */

$this->title = 'Thay đổi loại Thu/Chi:  ' . $model->loai_thu_chi;
$this->params['breadcrumbs'][] = ['label' => 'Loại Thu Chi', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="loai-thu-chi-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
