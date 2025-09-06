<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\NguoiThuChi */

$this->title = 'Cập nhật: ' . $model->ho_ten;
$this->params['breadcrumbs'][] = ['label' => 'Danh sách người thu/chi', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="nguoi-thu-chi-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
