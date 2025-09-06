<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\KhachHang */

$this->title = 'Tạo Khách Hàng Mới';
$this->params['breadcrumbs'][] = ['label' => 'Danh Sách Khách Hàng', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="khach-hang-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
		'modelsDieuTri' => $modelsDieuTri,
		'bac_si'=>$bac_si,
    ]) ?>

</div>
