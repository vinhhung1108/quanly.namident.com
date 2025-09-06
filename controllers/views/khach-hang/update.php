<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\KhachHang */

$this->title = 'Cập nhật khách hàng';
$this->params['breadcrumbs'][] = ['label' => 'Danh Sách Khách Hàng', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="khach-hang-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
		'modelsDieuTri' => $modelsDieuTri,
		'bac_si' => $bac_si,
    ]) ?>

</div>
