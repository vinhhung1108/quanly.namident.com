<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\BacSi */

$this->title = 'Thêm Bác sĩ';
$this->params['breadcrumbs'][] = ['label' => 'Danh sách Bác sĩ', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bac-si-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
