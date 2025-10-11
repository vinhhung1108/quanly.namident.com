<?php
use yii\helpers\Html;

/**
 * @var yii\web\View            $this
 * @var app\models\BacSiHoaHong $model
 * @var app\models\BacSi        $bacSi
 * @var array                   $dichVuOptions
 */

$this->title = 'Thêm hoa hồng theo dịch vụ';
$this->params['breadcrumbs'][] = ['label' => 'Bác sĩ', 'url' => ['bac-si/index']];
$this->params['breadcrumbs'][] = ['label' => $bacSi->ho_ten, 'url' => ['bac-si/view', 'id' => $bacSi->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="bac-si-hoa-hong-create">
  <?= $this->render('_form', [
      'model'         => $model,
      'bacSi'         => $bacSi,
      'dichVuOptions' => $dichVuOptions,
      'isUpdate'      => false,
  ]) ?>
</div>
