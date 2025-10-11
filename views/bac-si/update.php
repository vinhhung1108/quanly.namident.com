<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BacSi */

$this->title = 'Cập nhật thông tin';
$this->params['breadcrumbs'][] = ['label' => 'Danh sách bác sĩ', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ho_ten, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bac-si-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
