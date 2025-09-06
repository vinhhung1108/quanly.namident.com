<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
// use yii\jui\DatePicker;
use kartik\date\DatePicker;
use yii\web\View;
use app\models\NhomKhachHang;
use yii\helpers\ArrayHelper;
use yii\widgets\MaskedInput;
/* @var $this yii\web\View */
/* @var $model app\models\KhachHang */
/* @var $form yii\widgets\ActiveForm */
$nhomkhachhang = ArrayHelper::merge([0=>'Tất cả'],ArrayHelper::map(NhomKhachHang::find()->all(), 'id','ten_nhom'));

$user = Yii::$app->user->identity;
$current_date = date('Y-m-d');
// $current_time = date("Y-m-d",time());
$isDisabled = false;

$jsform = <<<JS
    $(".dynamicform_wrapper").on("afterInsert", function(e, item) {
		var today = new Date().toISOString().substring(0, 10);
		$(item).find('.remove-item').each(function(index,element){
			$(element).attr('disabled',false)  
        });
        $(item).find('input,textarea,select').each(function(index,element){
		   $(element).attr('disabled',false);
        });
		$(item).find("[type='date']").each(function(index, element) {
			$(element).val(today);
			if($(element).hasClass("admin-check")){
				$(element).attr('disabled',false);
			}else{
				$(element).attr('disabled',true);
			}
		});
		 $(item).find('#dieutri-0-phi_t,#dieutri-0-tam_thu_t').inputmask({
            "alias":"decimal",
            "groupSeparator":",",
            "autoGroup":true,
            "rightAlign":false,
        });
    });
JS;
$this->registerJs($jsform);
?>

<div class="khach-hang-form">

    <?php $form = ActiveForm::begin(['id'=>'dynamic-form']); ?>
	
	<div class="row">

		<?= $form->field($model, 'ho_ten', ['options'=>['class' => 'col-md-6 form-group']])->textInput(['maxlength' => true]) ?>
		
		<?= $form->field($model, 'sdt',  ['options'=>['class' => 'col-md-2 form-group']])->textInput(['maxlength' => true]) ?>
		
		<?= $form->field($model, 'ma_the',  ['options'=>['class' => 'col-md-2 form-group']])->textInput(['maxlength' => true]) ?>
						
		<?= $form->field($model, 'gioi_tinh',  ['options'=>['class' => 'col-md-2 form-group']])->radioList(array('0'=>'Nam', '1'=>'Nữ')) ?>
	</div>
	<div class="row">
		<?= $form->field($model, 'dia_chi',  ['options'=>['class' => 'col-md-6 form-group']])->textInput(['maxlength' => true]) ?>
	
		<?= $form->field($model, 'nghe_nghiep', ['options'=>['class' => 'form-group col-md-2']])->textInput(['maxlength' => true]); ?>
		<?= $form->field($model, 'gioi_thieu', ['options'=>['class' => 'form-group col-md-2']])->textInput(['maxlength' => true]); ?>
		<?= $form->field($model, 'ngay_sinh', ['options'=>['class' => 'col-md-2 form-group']])->input('date') ?>
	
	</div>
   <!-- <?= $form->field($model, 'tong_phi')->textInput() ?>

    <?= $form->field($model, 'tam_thu')->textInput() ?>

    <?= $form->field($model, 'con_lai')->textInput() ?> -->

	<div class="row">
		<?php // $form->field($model, 'bs_dieu_tri', ['options'=>['class' => 'col-md-6 form-group']])->textInput(['maxlength' => true]) ?>
		
		<?= $form->field($model, 'tien_su_benh', ['options'=>['class' => 'form-group col-md-6']])->textarea(['row'=>'1']); ?>
		<?= $form->field($model, 'chan_doan', ['options'=>['class' => 'form-group col-md-4']])->textarea(['row'=>'1']); ?>		
		<?= $form->field($model, 'nhom_kh', ['options'=>['class' => 'form-group col-md-2']])->dropDownList($nhomkhachhang); ?>
	</div>
	<div class="row">
		<?= $form->field($model, 'ghi_chu', ['options'=>['class' => 'form-group col-md-4']])->textarea(['row' => '2']); ?>
		<?= $form->field($model, 'noi_dung_hen', ['options'=>['class' => 'form-group col-md-4']])->textarea(['row' => '2']); ?>
		<?= $form->field($model, 'ngay_hen', ['options'=>['class' => 'form-group col-md-2']])->input('date'); ?>
		<?= $form->field($model, 'gio_hen', ['options'=>['class' => 'form-group col-md-2']])->input('time'); ?>
		
	</div>
	<!--Dynamic form-->
	<div class="row">
		<div class="panel panel-default">
			<div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i> Nội dung điều trị</h4></div>
			
			<div class="panel-body">
				 <?php DynamicFormWidget::begin([
					'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
					'widgetBody' => '.container-items', // required: css class selector
					'widgetItem' => '.item', // required: css class
					'limit' => 300, // the maximum times, an element can be cloned (default 999)
					'min' => 0, // 0 or 1 (default 1)
					'insertButton' => '.add-item', // css class
					'deleteButton' => '.remove-item', // css class
					'insertPosition' => 'top',
					'model' => $modelsDieuTri[0],					
					'formId' => 'dynamic-form',
					'formFields' => [
						'ngay_dieu_tri',
						'noi_dung',
						'bs',
						'phi_t',						
						'tam_thu_t',
						'hinh_thuc_thanh_toan'
					]
				]); ?>
				<div class="panel-body" style="margin-bottom:10px;">
						<div class="pull-left">
							<button type="button" class="add-item btn btn-success btn-xs">
							<i class="glyphicon glyphicon-plus"><span class="them"> Thêm nội dung điều trị</span></i>
							</button>						
						</div>
						<div class="pull-right">
							<?= Html::submitButton('Lưu thay đổi', ['class' => 'btn btn-success']) ?>
						</div>
						<div class="clearfix"></div>
				</div>
				
				<div class="container-items"><!-- widgetContainer -->
				<?php foreach ($modelsDieuTri as $i => $modelDieuTri): ?>
					
					<div class="item panel panel-default"><!-- widgetBody -->
						
						<div class="panel-body">
							<?php
								// if($user->role === 'admin' || $current_date === $modelDieuTri->ngay_dieu_tri){
								if($user->role === 'admin' || $current_date === date("Y-m-d",$modelDieuTri->created_at)){	
									$isDisabled = false;
								}else{
									$isDisabled = true;
								}
								// necessary for update action.
								if (!$modelDieuTri->isNewRecord) {
									echo Html::activeHiddenInput($modelDieuTri, "[{$i}]id");
								}
								
							?>							
							<div class="row">
									
									<?php  echo $form->field($modelDieuTri, "[{$i}]ngay_dieu_tri", ['options'=>['class'=>'col-xs-12 col-sm-2']])
									->input('date',[
										'disabled'=> !($user->role === 'admin'),
										'class' => ($user->role === 'admin') ? 'form-control admin-check' : 'form-control',
									]);
										
									?>
												
									<?php echo $form->field($modelDieuTri, "[{$i}]noi_dung", ['options'=>['class'=>'col-xs-12 col-sm-3']])->textarea(['row'=>'1','disabled'=>$isDisabled]) ?>
									
									<?= $form->field($modelDieuTri, "[{$i}]bs", ['options'=>['class'=>'col-xs-12 col-sm-2']])->dropdownList($bac_si, ['disabled'=>$isDisabled]) ?>
									<?= $form->field($modelDieuTri, "[{$i}]phi_t", ['options'=>['class'=>'col-xs-6 col-sm-2']])->widget(MaskedInput::class,[										
										'clientOptions' => [
											'alias' =>  'decimal',
											'groupSeparator' => ',',
											'autoGroup' => true,
											'rightAlign' => true,			
										],
										'options'=>[
											'disabled'=>$isDisabled,
										],
									]) ?>
									
									<?= $form->field($modelDieuTri, "[{$i}]tam_thu_t", ['options'=>['class'=>'col-xs-6 col-sm-2']])->widget(MaskedInput::class,[									
										'clientOptions' => [
											'alias' =>  'decimal',
											'groupSeparator' => ',',
											'autoGroup' => true,
											'rightAlign' => true,
										],
										'options'=>[
											'disabled'=>$isDisabled,
										],
										
									]) ?>
									
									<?php echo $form->field($modelDieuTri, "[{$i}]hinh_thuc_thanh_toan", ['options'=>['class'=>'col-xs-12 col-sm-1']])
										->dropDownList(['TM'=>'TM', 'CK'=>'CK'],['disabled'=>$isDisabled])
										->label('TT'); ?>
							</div><!-- .row -->
						</div>

						<div class="panel-heading">
							<h3 class="panel-title pull-left"></h3>
							<div class="pull-left">
								<?php
									//if($user->role === 'admin' || $modelDieuTri->created_at === null || $current_date === date("Y-m-d",$modelDieuTri->created_at) )  {
								?>
									<button type="button" class='<?php echo ($user->role === "admin") ? "remove-item btn btn-danger btn-xs isAdmin" : "remove-item btn btn-danger btn-xs"; ?>'
										<?= ($user->role !== "admin") ? 'disabled' : '' ?>
									>
										<i class="glyphicon glyphicon-minus"> Xóa</i>
									</button>
								<?php
									//}
								?>
									
								
							</div>
							<div class="clearfix"></div>
						</div>
						
					</div><!--End Item-->
				<?php endforeach; ?>
				</div><!--End Container-items -->
						
				<?php DynamicFormWidget::end(); ?>
			</div><!--End panel-body-->
		</div><!--End panel-->
	</div><!-- End Row Dynamic form-->
	
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
			$ipos = strlen("uploads/" . $model->id . "/");
			//$textname = ltrim($url,"uploads/" . $model->id . "/");
			$textname = substr($url,$ipos);
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
	
	<!-- Thêm video từ youtube -->
	<?php
		foreach ($model->getvideo() as $videourl):
		if($videourl <> null){
	?>
		<div>
			<input type='checkbox'  class='rm_video' name='id_video[]' value='<?= $videourl; ?>' style='margin-left:20px;'>
			<iframe width="560" height="315" src="<?= 'https://www.youtube.com/embed' . $videourl; ?>" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
		</div>
		<?php } endforeach; ?>
	<?php if($videourl <> null){ ?>
			<?= Html::a('Xóa video đã chọn', ['deletevideo', 'id' => $model->id], [
					'class' => 'btn btn-danger',
					'data' => [
						'confirm' => 'Xóa video đã chọn',
						'method' => 'post',
					],
				]) 
			?>		
	<?php } ?>
	
	<?= $form->field($model, 'videos', ['options'=>['class' => 'col-md-12 form-group']])->textInput(['maxlength' => true]); ?>
	
    

    <?php ActiveForm::end(); ?>	
	
</div><!--End Khach-hang-form--> 