<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ThuChiSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="thu-chi-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?php // echo $form->field($model, 'id') ?>
	<div class="row">

		<?= $form->field($model, 'thu_chi', ['options'=>['class'=>'col-xs-4 col-sm-2']])->radioList(array(''=>'Tất cả','Thu'=>'Thu', 'Chi'=>'Chi')) ?>

		<?= $form->field($model, 'ngay_thu',['options'=>['class'=>'col-xs-4 col-sm-2']])->input('date') ?>
		
		<?= $form->field($model,'loai',['options' =>['class'=>'form-group col-xs-4 col-sm-2']])->dropdownList(array(''=>'','Sinh hoạt'=>'Sinh hoạt', 'Vật tư'=>'Vật tư','Labo'=>'Labo','Lương'=>'Lương', 'Tạm ứng'=>'Tạm ứng','Hoàn ứng'=>'Hoàn ứng')) ?>
		
		<?php // echo $form->field($model, 'so_tien') ?>   

		<?php // echo $form->field($model, 'noi_dung') ?>

		<div class="form-group col-xs-12 col-sm-12">
			<?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
			<?= Html::a("Search All", ['thu-chi/index#search'], ['class' => 'btn btn-default']);?>
			<?php // echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
		</div>
	</div>
	
    <?php ActiveForm::end(); ?>

</div>
