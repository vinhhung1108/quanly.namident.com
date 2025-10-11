<?php
/* @var $model app\models\DichVu */
$this->title = 'Cập nhật dịch vụ: ' . $model->ten;
$this->params['breadcrumbs'][] = ['label'=>'Danh sách dịch vụ','url'=>['index']];
$this->params['breadcrumbs'][] = $this->title;
echo $this->render('_form', compact('model'));
