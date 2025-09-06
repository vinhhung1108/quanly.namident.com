<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\KhachHang */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Danh Sách Khách Hàng', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="khach-hang-view">

    <!-- <h1>Mã khách hàng: <?= Html::encode($this->title) ?></h1> -->
	<h1> Thông tin khách hàng</h1>

   <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Tạo Phiếu thu', ['receipt', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
    
		<?= Html::a('Tạo Khách Hàng Mới', ['create'], ['class' => 'btn btn-success pull-right']) ?>
	</p>
	
	<?php
	

		$modelsDieuTri = $model->dieuTris;
		
	?>
	
	<div id="view_kh">
							
		<div id="thong_tin_kh">
			<div class="row row-tt">								
				<div class="col-xs-4 col-sm-1 cell-tt cell-tt-label">Họ và tên:</div>
				<div class="col-xs-8 col-sm-3 cell-tt"><?= $model->ho_ten; ?></div>
				<div class="col-xs-4 col-sm-1 cell-tt cell-tt-label">SĐT:</div>
				<div class="col-xs-8 col-sm-3 cell-tt">&nbsp;<?= $model->sdt; ?></div>
				<div class="col-xs-4 col-sm-1 cell-tt cell-tt-label">Ngày sinh:</div>
				<div class="col-xs-8 col-sm-3 cell-tt">&nbsp;<?php if($model->ngay_sinh <> null){$date = date_create($model->ngay_sinh); echo date_format($date,"d/m/Y");} ?></div>
				
			</div>
			<div class="row row-tt">
				<div class="col-xs-4 col-sm-1 cell-tt cell-tt-label">Giới tính:</div>
				<div class="col-xs-8 col-sm-3 cell-tt">&nbsp;<?php if($model->gioi_tinh =='0'){echo "Nam";}else{if($model->gioi_tinh == '1'){echo "Nữ";}else{echo " ";}} ?></div>
				<div class="col-xs-4 col-sm-1 cell-tt cell-tt-label">Công việc:</div>
				<div class="col-xs-8 col-sm-3 cell-tt">&nbsp;<?= $model->nghe_nghiep; ?></div>
				<div class="col-xs-4 col-sm-1 cell-tt cell-tt-label">Mã BN:</div>
				<div class="col-xs-8 col-sm-3 cell-tt">&nbsp;<?= $model->ma_the; ?></div>				
			</div>
			<div class="row row-tt">				
								
				<div class="col-xs-4 col-sm-1 cell-tt cell-tt-label">Giới thiệu:</div>
				<div class="col-xs-8 col-sm-3 cell-tt">&nbsp;<?= $model->gioi_thieu; ?></div>				
				<div class="col-xs-4 col-sm-1 cell-tt cell-tt-label">Địa chỉ:</div>
				<div class="col-xs-8 col-sm-7 cell-tt">&nbsp;<?= $model->dia_chi; ?></div>
			</div>						
			<div class="row row-tt">
				<div class="col-xs-4 col-sm-1 cell-tt cell-tt-label">Ngày hẹn:</div>
				<div class="col-xs-8 col-sm-1 cell-tt">&nbsp;<?php if($model->ngay_hen <> null){$date = date_create($model->ngay_hen); echo date_format($date,"d/m/Y");} ?></div>
				<div class="col-xs-4 col-sm-1 cell-tt cell-tt-label">Giờ hẹn:</div>
				<div class="col-xs-8 col-sm-1 cell-tt"><?= $model->gio_hen ?> </div>
				<div class="col-xs-4 col-sm-2 cell-tt cell-tt-label">Nội dung hẹn:</div>
				<div class="col-xs-8 col-sm-6 cell-tt cell-tt-label"><?= $model->noi_dung_hen ?></div>
			</div>			
			
			<table  class="row row-tt" id="chan_doan">
				<tbody>
				<?php if($model->tien_su_benh <> ''){ ?>
					<tr class="row">
						<th class="col-xs-4 col-sm-1 cell-tt cell-tt-label" scope="row">Tiền sử bệnh:</th>
						<td class="col-xs-8 col-sm-11 cell-tt"><?php echo '<span class="wraptext" style ="color:red"><mark>'.$model->tien_su_benh . '</mark></span>'; ?></td>
					</tr>
				<?php } ?>
				<?php if($model->chan_doan <> ''){ ?>
					<tr class="row">
						<th scope="row" class="col-xs-4 col-sm-1 cell-tt cell-tt-label">Chẩn đoán:</th>
						<td class="col-xs-8 col-sm-11 cell-tt"><?php echo '<span class="wraptext">'.$model->chan_doan . '</span>'; ?></td>
					</tr>
				<?php } ?>
				<?php if($model->ghi_chu <> '') { ?>
					<tr class="row">
						<th scope="row" class="col-xs-4 col-sm-1 cell-tt cell-tt-label">Ghi chú:</th>
						<td class="col-xs-8 col-sm-11 cell-tt"><?php echo '<span class="wraptext">'.$model->ghi_chu . '</span>'; ?></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
			<div class="row row-tt">				
				<div class="col-xs-4 col-sm-1 cell-tt cell-tt-label">Tổng phí:</div>
				<div class="col-xs-8 col-sm-2 cell-tt"><?= number_format($model->tong_phi); ?></div>			
				<div class="col-xs-4 col-sm-1 cell-tt cell-tt-label">Tạm thu:</div>
				<div class="col-xs-8 col-sm-2 cell-tt"><?= number_format($model->tam_thu); ?></div>
				<div class="col-xs-4 col-sm-1 cell-tt cell-tt-label">Còn lại:</div>
				<div class="col-xs-8 col-sm-5 cell-tt"><?= number_format($model->con_lai); ?></div>
			</div>
		</div>
		
		<div id="dieu_tri_kh">
			<div class="title-dieu-tri">Lịch Sử Điều Trị</div>
			<div class="ls-dieu-tri">
				
				<table class="row" id="dieu_tri">
					<tr class="row"  style="background-color: #25466e;color: #ffffff;">
						<td class="col-xs-1 col-sm-1 cell-tt cell-tt-label" style="text-align:center">Ngày điều trị</td>
						<td class="col-xs-4 col-sm-4 cell-tt cell-tt-label" style="text-align:center">Nội dung điều trị</td>
						<td class="col-xs-1 col-sm-1 cell-tt cell-tt-label" style="text-align:center">Bác sĩ</td>
						<td class="col-xs-2 col-sm-2 cell-tt cell-tt-label" style="text-align:center">Phí điều trị</td>
						<td class="col-xs-2 col-sm-2 cell-tt cell-tt-label" style="text-align:center">Tạm thu</td>
						<td class="col-xs-2 col-sm-2 cell-tt cell-tt-label" style="text-align:center">Hình thức thanh toán</td>
					</tr>
					
					<?php foreach ($modelsDieuTri as $key => $modelDieuTri): ?>
					
					<tr  class="row row-dieu-tri">
						<td class="col-xs-1 col-sm-1 cell-tt">
							<?php if($modelDieuTri->ngay_dieu_tri <> null){$date = date_create($modelDieuTri->ngay_dieu_tri); echo date_format($date,"d/m/Y");} ?>
						</td>
						<td class="col-xs-4 col-sm-4 cell-tt">
							<?php echo '<span class="wraptext">'. $modelDieuTri->noi_dung . '</span>';?>
						</td>
						<td class="col-xs-1 col-sm-2 cell-tt">
							<?= $modelDieuTri->bs;?>
						</td>
						<td class="col-xs-2 col-sm-2 cell-tt view-money">
							<?= number_format($modelDieuTri->phi);?>
						</td>
						<td class="col-xs-2 col-sm-2 cell-tt view-money">
							<?= number_format($modelDieuTri->tam_thu);?>
						</td>
						
						<td class="col-xs-2 col-sm-1 cell-tt">
							<?= $modelDieuTri->hinh_thuc_thanh_toan;?>
						</td>
					</tr>				
						
					
					<?php endforeach; ?>
				</table>
				
			</div>
			<div class="title-dieu-tri">Hình ảnh</div>
			<div class="hinh-anh">
				<div class="imgview_row">
					<?php
						$image= "";
						$path = '';
						$burl = Yii::$app->request->BaseUrl.'/';
						foreach ($model->getimageurl() as $url){
								$path = $burl . $url;
								$image = $image."<div class='imgview_column'>".Html::img($path,['style'=>'width:100%','class'=>'hover-shadow cursor', 'onclick'=>"openModal();currentSlide(1)"])."</div>";							
							}
						echo $image;
					?>
				</div>
				<div id="myModal" class="modal">
					<span class="imgview_close cursor" onclick="closeModal()">&times;</span>
					<div class="modal-content">
						<?php
							$image = "";
							$path = "";
							$burl = Yii::$app->request->BaseUrl.'/';
							$i=0;
							foreach ($model->getimageurl() as $url){
								$i=$i+1;
								$path = $burl . $url;
								$image = $image .'<div class="mySlides"><div class="numbertext">1 / '.$i. '</div>';
								$image = $image . Html::img($path,['style' =>'width:auto']).'</div>';
							}
							echo $image;
						?>
						<a class="prev" onclick="plusSlides(-1)">&#10094;</a>
						<a class="next" onclick="plusSlides(1)">&#10095;</a>
						<div class="caption-container">
						  <p id="caption"></p>
						</div>
						<?php
							$image = "";
							$path = "";
							$burl = Yii::$app->request->BaseUrl.'/';
							$i=0;
							foreach ($model->getimageurl() as $url){
								$i=$i+1;
								$path = $burl . $url;
								$image = $image .'<div class="imgview_column">';
								$image = $image . Html::img($path,['class' =>'demo cursor','style'=>'width:100%','onclick'=>'currentSlide('.$i.')']).'</div>';
							}
							echo $image;
						?>
					</div>
				</div>
			</div>		
<script>
function openModal() {
  document.getElementById('myModal').style.display = "block";
}

function closeModal() {
  document.getElementById('myModal').style.display = "none";
}

var slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  var i;
  var slides = document.getElementsByClassName("mySlides");
  var dots = document.getElementsByClassName("demo");
  var captionText = document.getElementById("caption");
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "flex";
  dots[slideIndex-1].className += " active";
  captionText.innerHTML = dots[slideIndex-1].alt;
}
</script>
			<div class="title-dieu-tri">Video</div>
			<div class="video">
				<?php foreach ($model->getvideo() as $videourl):
					if($videourl <> null){
				?>
					<div>
						<iframe width="560" height="315" src="<?= 'https://www.youtube.com/embed' . $videourl; ?>" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
					</div>
				<?php } endforeach; ?>
			</div>
		</div>
	</div>
</div>
