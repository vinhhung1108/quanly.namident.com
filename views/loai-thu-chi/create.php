<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\LoaiThuChi */

$this->title = 'Tạo loại Thu Chi';
$this->params['breadcrumbs'][] = ['label' => 'Loại Thu Chi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loai-thu-chi-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,		
    ]) ?>

</div>
