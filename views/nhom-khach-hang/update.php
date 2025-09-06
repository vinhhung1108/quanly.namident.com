<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\NhomKhachHang */

$this->title = 'Cập nhật Nhóm khách hàng: ' . $model->ten_nhom;
$this->params['breadcrumbs'][] = ['label' => 'Danh sách nhóm khách hàng', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="nhom-khach-hang-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
