<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\BacSi;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\LoaiThuChi */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bao-cao-thu-form">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
		]); 
	?>
    <div class="row">
        <?= $form->field($model, 'tu_ngay',['options'=>['class'=>'form-group col-xs-4 col-sm-3']])->input('date')->label('Từ ngày') ?>
        <?= $form->field($model, 'den_ngay',['options'=>['class'=>'form-group col-xs-4 col-sm-3']])->input('date')->label('Đến ngày') ?>
    </div>
    <div class="row">
        <?= $form->field($model, 'bs',['options'=>['class' => 'col-md-3 col-sm-6 form-group']])
        ->dropdownList(ArrayHelper::merge(
            ['all'=>'Tất cả'],
            ArrayHelper::map(BacSi::find()->all(), 'ho_ten','ho_ten')
            ))
        ->label('Bác sĩ'); ?>
        <?= $form->field($model, 'hinh_thuc_thanh_toan',['options'=>['class' => 'col-md-3 col-sm-6 form-group']])
        ->dropdownList(['all' =>'Tất cả','TM'=>'TM','CK'=>'CK'])
        ->label('Hình thức thanh toán'); ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Báo cáo', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
