<?php
use yii\helpers\Html;
/* @var $model app\models\KhachHang */
$this->title = 'Thêm khách hàng mới';
$this->params['breadcrumbs'][] = ['label' => 'Danh Sách Khách Hàng', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($this->title) ?></h1>
<?= $this->render('_form', ['model' => $model]) ?>
