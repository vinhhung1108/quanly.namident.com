<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\NguoiThuChi;
use app\models\LoaiThuChi;

/* @var $this yii\web\View */
/* @var $model app\models\ThuChi */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="thu-chi-form">

    <?php $form = ActiveForm::begin(); ?>
	<div class="row">

    <?= $form->field($model, 'thu_chi', ['options'=>['class'=>'form-group col-xs-4 col-sm-2']])->radioList(array('Thu'=>'Thu', 'Chi'=>'Chi')) ?>

    <?= $form->field($model, 'ngay_thu',['options'=>['class'=>'form-group col-xs-4 col-sm-2']])->input('date') ?>
	
	<?php //echo $form->field($model,'loai',['options' =>['class'=>'form-group col-xs-4 col-sm-2']])->dropdownList($model->loaithuchi) ?>
	<?php $plusLTC = Html::a(
          '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
          ['/loai-thu-chi/create'],
          [
              'class' => 'label-add',
              'title' => 'Thêm loại thu/chi',
              'data-pjax' => 0,
              'style' => 'margin-left:6px; font-size:12px; display:inline-block;'
          ]
      );
      ?>
	<?php  echo $form->field($model, 'loai',  ['options'=>['class'=>'form-group col-xs-4 col-sm-2']])
  ->dropDownList(
        ArrayHelper::map(LoaiThuChi::find()->orderBy(['loai_thu_chi'=>SORT_ASC])->all(), 'id', 'loai_thu_chi'),
        ['prompt' => '— Chọn loại thu/chi —']
    )->label('Loại thu/chi ' . $plusLTC, ['encode'=>false]); ?>

    <?= $form->field($model, 'so_tien_t', ['options'=>['class'=>'form-group col-xs-12 col-sm-3']])->widget(\yii\widgets\MaskedInput::className(),[										
										'clientOptions' => [
											'alias' =>  'decimal',
											'groupSeparator' => ',',
											'autoGroup' => true,
											'rightAlign' => false
										],
      ]) ?>
      <?php $plus = Html::a(
          '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
          ['/nguoi-thu-chi/create'],
          [
              'class' => 'label-add',
              'title' => 'Thêm người thu/chi',
              'data-pjax' => 0,
              'style' => 'margin-left:6px; font-size:12px; display:inline-block;'
          ]
      );
      ?>
    <?= $form->field($model, 'nguoi_thu_chi_id', ['options'=>['class'=>'form-group col-xs-12 col-sm-3']])
    ->dropDownList(
        ArrayHelper::map(NguoiThuChi::find()->orderBy(['ho_ten'=>SORT_ASC])->all(), 'id', 'ho_ten'),
        ['prompt' => '— Chọn người thu/chi —']
    )->label('Người thu/chi ' . $plus, ['encode'=>false]) ?>

    <?= $form->field($model, 'noi_dung', ['options'=>['class'=>'form-group col-xs-12 col-sm-12']])->textarea(['rows' => 6]) ?>
	</div>
	
	<!-- Hình ảnh -->
	<div class="row" id="list_img">			
	<?php
		$image = '';
		$burl = '';
		$path = '';
		$burl = Yii::$app->request->BaseUrl.'/';
		$textname = '';
		foreach ($model->getimageurl() as $key => $url){			
			if($url <> null){
			$path = $burl . $url;
			$textname = ltrim($url,"uploads/thu-chi/" . $model->id . "/");
			$image = $image."<input type='checkbox'  class='rm_image' name='id_img[]' value='".$textname."' style='margin-left:20px;'>".Html::img($path,['width' =>'100', 'height' => 'auto']);
			}
		}
		echo '<div >'.$image.'</div>';
	?>
	<?php if($url <> null){ ?>
			<?= Html::a('Xóa hình đã chọn', ['deleteimg', 'id' => $model->id], [
					'class' => 'btn btn-danger',
					'data' => [
						'confirm' => 'Xoa hinh da chon',
						'method' => 'post',
					],
				]) 
			?>		
	<?php } ?>
	</div>
	<div class="row">
	<?= $form->field($model, 'upload[]', ['options'=>['class' => 'col-md-12 form-group']])->fileInput(['multiple' => true, 'accept' => 'image/*']) ?>	
	</div>
	<!--End hình ảnh -->
	
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
