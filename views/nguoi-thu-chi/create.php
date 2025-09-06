<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\NguoiThuChi */

$this->title = 'Thêm mới người thu/chi';
$this->params['breadcrumbs'][] = ['label' => 'Danh sách người thu chi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nguoi-thu-chi-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
