<?php
use yii\helpers\Html;
/* @var $model app\models\KhachHang */
$this->title = 'Sửa thông tin khách hàng: ' . $model->ho_ten;
$this->params['breadcrumbs'][] = ['label' => 'Danh Sách Khách Hàng', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ma_the, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<h1><?= Html::encode($this->title) ?></h1>
<?= $this->render('_form', ['model' => $model]) ?>
