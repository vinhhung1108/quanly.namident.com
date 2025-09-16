<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ThuChi */

$this->title = 'Tạo Khoản Thu/Chi';
$this->params['breadcrumbs'][] = ['label' => 'Quản Lý Thu Chi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="thu-chi-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
