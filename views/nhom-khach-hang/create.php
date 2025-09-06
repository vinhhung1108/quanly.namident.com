<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\BacSi */

$this->title = 'Thêm nhóm khách hàng';
$this->params['breadcrumbs'][] = ['label' => 'Danh sách nhóm khách hàng', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nhom-khach-hang-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
