<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\ThuChi;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ThuChiSearch */
/* @var $thongkeModel app\models\ThuChiThongke */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quản Lý Thu Chi';
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
?>
<div class="thu-chi-index">

    <h1><?= Html::encode($this->title) ?></h1>
	<?php Pjax::begin(); ?>
	<div class="row">
		
			<button class="btn btn-primary" style="pointer-events: none;" type="button" disabled>Thống kê thu/chi</button>
		
			<?= Html::a('Tạo khoản Thu/Chi', ['create'], ['class' => 'btn btn-success']) ?>
	
	</div>
<?php 
	if(isset($user) && $user->role === "admin") {
?>

	<div class="row thu-chi-header">	
	
		<?php echo $this->render('_thongke', ['model' => $thongkeModel, 'loaithuchi'=>$loaithuchi]); ?>
		
			<div class="col-xs-12 col-sm-6">
				<table class="table table-stripeds thu-chi-table-info">		
					<tbody>
						<tr class="bg-danger"><th colspan="3">Thống kê Thu</th></tr>
					  <tr>
						<th>Thu từ khách hàng</th>
						<td><strong><?php echo number_format($thu_kh); ?></strong></td>
						<td></td>
					  </tr>
					  <tr>
						<th>Các khoản thu khác</th>
						<td><strong><?php echo number_format($thu_khac); ?></strong></td>
						<td></td>
					  </tr>
					  <tr>
						<th>Tổng thu</th>
						<td><strong><?php echo number_format($thu_kh + $thu_khac); ?></strong></td>
						<td></td>
					  </tr>
					</tbody>
				</table>
			</div>
			<div class="col-xs-12 col-sm-6">
				<table class="table table-stripeds thu-chi-table-info">		
					<tbody>
					<tr class="bg-success"><th colspan="3">Các khoản khác</th></tr>			
					<tr>
						<th>Tổng Chi</th>
						<td><strong><?php echo number_format($tong_chi); ?></strong></td>						
					</tr>									
					<tr>
						<th>KH còn thiếu</th>
						<td><strong><?php echo number_format($con_lai); ?></strong></td>						
					</tr>
					<tr >
						<th>Lợi nhuận</th>
						<td><strong><?php echo number_format($thu_kh + $thu_khac - $tong_chi); ?></strong></td>						
					</tr>
					
					</tbody>
				</table>
			</div>		
	</div>
<?php } ?>
	
		<p>
			<h2>Chi tiết các khoản thu chi</h2>
		</p>
	
		<?php //echo $this->render('_search', ['model' => $searchModel]); ?>    
	
		<?= GridView::widget([
			'dataProvider' => $dataProvider,
			//'filterModel' => $searchModel,
			'showFooter'=>true,
			'columns' => [
				//['class' => 'yii\grid\SerialColumn'],

				//'id',
				//'thu_chi',
				[
					'attribute'=>'thu_chi',
					'filter'=>[''=>'','Thu'=>'Thu','Chi'=>'Chi'],
					'contentOptions'=>['style'=>'max-width:30px;'],
				],
				[
					'attribute'=>'ngay_thu',
					'filterInputOptions' => ['type'=>'date', 'data-date-format'=>'dd/MM/yyyy'],
					'format'=>['date', 'dd/MM/yyyy'],
				],
				//'loai',
				'loaiThuChi.loai_thu_chi',
				[
					'attribute'=>'so_tien',
					'label'=>'Số tiền',
					'format'=>['decimal',0],
					'footer' => '<strong>Tổng: ' . ThuChi::getTotal($dataProvider->models, 'so_tien') . '</strong>',
				],            
				'noi_dung:ntext',

				['class' => 'yii\grid\ActionColumn',
				'contentOptions'=>['style'=>'max-width:40px;'],],
			],
		]); ?>
	
    <?php Pjax::end(); ?>
</div>