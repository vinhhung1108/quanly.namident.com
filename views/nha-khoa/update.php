<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\NhaKhoa */

$this->title = 'Cập nhật thông tin nha khoa';
$this->params['breadcrumbs'][] = ['label' => 'Nha Khoa', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="nha-khoa-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
