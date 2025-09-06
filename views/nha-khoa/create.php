<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\NhaKhoa */

$this->title = 'Tạo nha khoa';
$this->params['breadcrumbs'][] = ['label' => 'Nha Khoa', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nha-khoa-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
