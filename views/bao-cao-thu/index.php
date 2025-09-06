<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\KhachHangSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Báo cáo thu từ khách hàng';
$this->params['breadcrumbs'][] = $this->title;
?>
   
	<h1><?= Html::encode($this->title) ?></h1>
		<div class="panel-body">	
			<h3>Lọc báo cáo</h3>
			<?php echo $this->render('_form', ['model' => $searchModel]); ?>		
		</div>
	<div class="panel panel-default">
		<div class="panel-body">
			<table class="table table-striped">	
				<thead><tr><th>Kết quả báo cáo</th></tr></thead>
				<tbody>
					<tr>
						<td>
							<strong>Tổng thu: </strong>
							<?php echo Yii::$app->formatter->asInteger($dataProvider->query->sum('tam_thu')); ?>
							
						</td>
					</tr>
					<tr>
						<td>
							
						</td>
					</tr>
				</tbody>
			</table>
			
		</div>
	</div>

<div class="bao-cao-thu-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
		'filterModel' => null,
        'columns' => [
            [
				'class' => 'yii\grid\SerialColumn',				
			],			
			 [
				'attribute'=>'id_kh',
				'contentOptions'=>['style'=>'max-width:30px;'],
				'headerOptions' => ['width' => '30px'],
				'contentOptions'=>['style'=>'text-align:center;'],
			],			
			[
				'format'=>['date', 'dd/MM/yyyy'],
				'filterInputOptions' => ['type'=>'date', 'data-date-format'=>'dd/MM/yyyy'],
				'attribute'=>'ngay_dieu_tri',
			],
			[
				'attribute'=>'id_kh',
				'filter'=>true,
				'label'=>'Họ tên khách hàng',
				'value' => function ($searchModel) {
				   return $searchModel->ten_kh();
				}
			],
			[
				'attribute'=>'dia_chi',
				'filter'=>true,
				'label'=>'Địa chỉ',
				'value'=>'khach_hang.dia_chi',
			],
            //'id',
            //'lan_dieu_tri',           
            'noi_dung:ntext',
            'bs',
            //'phi',
            'tam_thu',
			[
				'attribute'=>'id_kh',
				'filter'=>true,
				'label'=>'Còn lại',
				'value' => function ($searchModel) {
				   return $searchModel->con_lai();
				}
			],
			[ 
				'attribute'=>'hinh_thuc_thanh_toan',
				'label' => 'Thanh toán',
			],
            //'id_kh',
            //'gio',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>