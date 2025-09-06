<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Json;
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
		 // áp cho mọi index: -0, -1, -2...
  $(item).find("[id$='-phi_t'], [id$='-tam_thu_t']").inputmask({
    alias: "decimal",
    groupSeparator: ",",
    autoGroup: true,
    rightAlign: false
  });
    });
JS;
$this->registerJs($jsform);

$this->registerJs(
    'window.__BS_LIST = '.Json::htmlEncode(array_values($bac_si)).';',
    \yii\web\View::POS_HEAD
);

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
									<?= $form->field($modelDieuTri, "[{$i}]ngay_dieu_tri", ['options'=>['class'=>'col-xs-12 col-sm-2']])
									->input('date',[
										'disabled'=> !($user->role === 'admin'),
										'class' => ($user->role === 'admin') ? 'form-control admin-check' : 'form-control',
									])->label('Ngày điều trị') ?>
												
									<?= $form->field($modelDieuTri, "[{$i}]noi_dung", ['options'=>['class'=>'col-xs-12 col-sm-3']])->textarea(['row'=>'1','disabled'=>$isDisabled])->label('Nội dung') ?>

									<?= $form->field($modelDieuTri, "[{$i}]bs", ['options'=>['class'=>'col-xs-12 col-sm-2']])->dropdownList($bac_si, ['disabled'=>$isDisabled])->label('Bác sĩ') ?>

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
									])->label('Phí điều trị') ?>

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

									])->label('Tạm thu') ?>

									<?= $form->field($modelDieuTri, "[{$i}]hinh_thuc_thanh_toan", ['options'=>['class'=>'col-xs-12 col-sm-1']])
										->dropDownList(['TM'=>'TM', 'CK'=>'CK'],['disabled'=>$isDisabled])->label('Hình thức TT') ?>
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
        <?php
$fixDynamicLabels = <<<'JS'
(function($){
  // map label đúng → ép lại cho chắc
  var LABELS = {
    'ngay_dieu_tri':'Ngày điều trị',
    'noi_dung':'Nội dung',
    'phi_t':'Phí điều trị',
    'tam_thu_t':'Tạm thu',
    'hinh_thuc_thanh_toan':'Hình thức TT',
    'bs':'Bác sĩ'
  };

  function forceLabels(scope){
    Object.keys(LABELS).forEach(function(k){
      $(scope).find('label[for$="-'+k+'"]').text(LABELS[k]);
    });
  }

  function htmlDecode(s){
    var ta = document.createElement('textarea'); ta.innerHTML = s; return ta.value;
  }
  function rescueUtf8(s){
    if (/Ã|Â/.test(s)) { try { return decodeURIComponent(escape(s)); } catch(e){} }
    return s;
  }

  function patchTemplate(){
    var $wrap = $('.dynamicform_wrapper');
    var inst = $wrap.data('yiiDynamicForm');
    if (inst && inst.template){
      var dec = htmlDecode(inst.template).replace(/&nbsp;/g,' ');
      dec = rescueUtf8(dec);
      inst.template = dec; // ghi đè template đã lỗi
      return true;
    }
    return false;
  }

  $(function(){
    // 1) Thử patch ngay lập tức (nếu chưa init thì không sao)
    patchTemplate();

    // 2) Đợi widget init xong rồi patch lại cho chắc
    var tries = 0, t = setInterval(function(){
      if (patchTemplate() || ++tries > 60){ // ~3s
        clearInterval(t);
        // ép label cho mọi item hiện có (trang update)
        var $root = $('.dynamicform_wrapper');
        if ($root.length) forceLabels($root);
      }
    }, 50);

    // 3) Nếu user bấm "Thêm nội dung" rất sớm, vẫn patch + ép label kịp thời
    $(document)
      .off('click.fixLabels', '.add-item')
      .on('click.fixLabels', '.add-item', function(){
        patchTemplate();
        // sau khi item được chèn xong, ép label cho item mới
        setTimeout(function(){
          var $last = $('.container-items .item').last();
          if ($last.length) forceLabels($last);
        }, 0);
      });

    // 4) Mỗi lần afterInsert cũng ép lại (trường hợp plugin tự chèn)
    $('.dynamicform_wrapper')
      .off('afterInsert.fixLabels')
      .on('afterInsert.fixLabels', function(e, item){
        forceLabels(item);
      });
  });
})(jQuery);

JS;

$this->registerJs($fixDynamicLabels, View::POS_END);
?>
<?php
$fixRemoveButton = <<<'JS'
(function($){
  // thay chữ trong nút remove về đúng " Xóa "
  function fixRemove(scope){
    $(scope).find('.remove-item i.glyphicon-minus').each(function(){
      // xóa mọi text node hiện tại rồi gắn lại " Xóa"
      var $i = $(this);
      $i.contents().filter(function(){ return this.nodeType === 3; }).remove();
      $i.append(document.createTextNode(' Xóa'));
    });
  }

  $(function(){
    var $root = $('.dynamicform_wrapper');
    if (!$root.length) return;

    // 1) Sửa các item đang có (trường hợp form update)
    fixRemove($root);

    // 2) Sau khi plugin chèn item mới
    $root.off('afterInsert.fixRemove').on('afterInsert.fixRemove', function(e, item){
      fixRemove(item);
    });

    // 3) Người dùng bấm "Thêm nội dung" rất sớm
    $(document).off('click.fixRemove', '.add-item').on('click.fixRemove', '.add-item', function(){
      setTimeout(function(){
        var $last = $('.container-items .item').last();
        if ($last.length) fixRemove($last);
      }, 0);
    });
  });
})(jQuery);
JS;

$this->registerJs($fixRemoveButton, \yii\web\View::POS_END);
?>

        <?php
$rebuildBsOptions = <<<'JS'
(function($){
  function looksBroken(s){ return /[ÃÂâÄ»]|áº|á»/.test(s||''); }
  function repair(s){
    try{
      var bytes = new Uint8Array(Array.from(s||'', ch => ch.charCodeAt(0) & 0xFF));
      var out = new TextDecoder('utf-8').decode(bytes);
      return out;
    }catch(e){}
    try{ return decodeURIComponent(escape(s||'')); }catch(e){}
    return (s||'').replace(/Â/g,' ');
  }

  function rebuild(scope){
    var list = window.__BS_LIST || [];
    $(scope).find('select[id$="-bs"]').each(function(){
      var $s = $(this);
      var current = $s.val() || '';
      var currentFixed = looksBroken(current) ? repair(current) : current;

      // Đổ lại toàn bộ option
      $s.empty();
      list.forEach(function(name){
        $s.append($('<option>', {value: name, text: name}));
      });

      // Khớp lại lựa chọn cũ (kể cả khi giá trị cũ đang lỗi mã)
      if (current){
        var pick = list.find(function(n){
          return n === current || n === currentFixed || repair(n) === current;
        });
        if (pick) $s.val(pick);
      }

      // Refresh plugin nếu có
      if ($s.data('selectpicker')) { // bootstrap-select
        $s.selectpicker('refresh');
      }
      if ($s.hasClass('select2-hidden-accessible')) { // select2
        $s.trigger('change.select2');
      } else {
        $s.trigger('change'); // mặc định
      }
    });
  }

  $(function(){
    var $root = $('.dynamicform_wrapper');
    if (!$root.length) return;

    // Sửa lần đầu khi trang load
    rebuild($root);

    // Sửa mỗi khi add item mới của DynamicForm
    $root.off('afterInsert.rebuildBs')
         .on('afterInsert.rebuildBs', function(e, item){ rebuild(item); });
  });
})(jQuery);
JS;
$this->registerJs($rebuildBsOptions, \yii\web\View::POS_READY);
?>



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
			$ipos = strlen("uploads/posts/" . $model->id . "/");
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