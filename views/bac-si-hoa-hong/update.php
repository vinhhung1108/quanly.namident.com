<?php
use yii\helpers\Html;

/**
 * @var yii\web\View            $this
 * @var app\models\BacSiHoaHong $model
 * @var app\models\BacSi        $bacSi
 * @var array                   $dichVuOptions
 */

$this->title = 'Cập nhật hoa hồng';
$this->params['breadcrumbs'][] = ['label' => 'Bác sĩ', 'url' => ['bac-si/index']];
$this->params['breadcrumbs'][] = ['label' => $bacSi->ho_ten, 'url' => ['bac-si/view', 'id' => $bacSi->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="bac-si-hoa-hong-update">
  <?= $this->render('_form', [
      'model'         => $model,
      'bacSi'         => $bacSi,
      'dichVuOptions' => $dichVuOptions,
      'isUpdate'      => true,
  ]) ?>
</div>
