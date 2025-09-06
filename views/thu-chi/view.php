<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\ThuChi;

/* @var $this yii\web\View */
/* @var $model app\models\ThuChi */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Quản Lý Thu Chi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="thu-chi-view">

    <h1><?= "Chi tiết khoản thu/chi" ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
		<?= Html::a('Tạo khoản Thu/Chi', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'thu_chi',
            //'loai',
            'loaiThuChi.loai_thu_chi',
                  [
              'attribute'=>'ngay_thu',
              'format'=>['date', 'dd/MM/yyyy'],
            ],
                  [
              'label'=>'Số tiền',
              'attribute'=>'so_tien',
              'format'=>['decimal',0],
            ],
            'noi_dung:ntext',
            [
              'attribute' => 'nguoiThuChiName',
              'label' => 'Người thu/chi',
            ],
          ],
    ]) ?>
	
	<!--Hình ảnh-->
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
<!-- End hình ảnh -->
</div>
