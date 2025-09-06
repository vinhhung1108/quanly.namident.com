<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model app\models\ThuChiSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<?php // array_unshift($loaithuchi,''); ?>
<div class="thong-ke-thu-chi">
	<h3><span style="padding-left:10px;">Chọn khoảng thời gian thống kê</span></h3>
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
		]); 
	?>

    <?php // echo $form->field($model, 'id') ?>
	<div class="row" style="padding-left:10px;">

		<?php // echo $form->field($model, 'thu_chi', ['options'=>['class'=>'col-xs-5 col-sm-2']])->radioList(array(''=>'Tất cả','Thu'=>'Thu', 'Chi'=>'Chi')) ?>

		<?= $form->field($model, 'ngay_bat_dau',['options'=>['class'=>'col-xs-6 col-sm-2']])->input('date') ?>
		<?= $form->field($model, 'ngay_ket_thuc',['options'=>['class'=>'col-xs-6 col-sm-2']])->input('date') ?>
		
		<?= $form->field($model, 'thu_chi', ['options'=>['class'=>'col-xs-6 col-sm-2']])->radioList(array(''=>'Tất cả','Thu'=>'Thu', 'Chi'=>'Chi')) ?>
					
		<?php //echo $form->field($model,'loai',['options' =>['class'=>'col-xs-4 col-sm-3']])->dropdownList($loaithuchi) ?>
		<?php  echo $form->field($model, 'loai',  ['options'=>['class'=>'col-xs-4 col-sm-3']])->widget(Select2::classname(), [
					'data' => $loaithuchi,
					'options' => ['placeholder' => 'Chọn loại thu chi...'],
					'pluginOptions' => [
						'allowClear' => true,
					],
				])  ?>
		
		<?php // echo $form->field($model, 'so_tien') ?>   

		<?php // echo $form->field($model, 'noi_dung') ?>

		<div class="form-group col-xs-12 col-sm-12">
			<?= Html::submitButton('Xem', ['class' => 'btn btn-primary']) ?>
			<?= Html::a("Reset", ['thu-chi/index'], ['class' => 'btn btn-default']);?>			
		</div>
	</div>
	
    <?php ActiveForm::end(); ?>

</div>
