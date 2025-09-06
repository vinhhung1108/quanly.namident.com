<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\DieuTriSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Danh Sách Điều Trị';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dieu-tri-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php //echo Html::a('Create Dieu Tri', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
				'class' => 'yii\grid\SerialColumn',				
			],
			[
				'attribute'=>'ma_the',
				'value'=>'khach_hang.ma_the',
				'label'=>'Mã khách hàng',
			],
			//  [
			// 	'attribute'=>'ma_khach_hang',
			// 	'contentOptions'=>['style'=>'max-width:30px;'],
			// 	'headerOptions' => ['width' => '30px'],
			// 	'contentOptions'=>['style'=>'text-align:center;'],
			// 	'filter'=> true,
			// 	'label'=>'Mã khách hàng',
			// 	'value'=>'khach_hang.id',
			// ],			
			[
				'format'=>['date','php:d/m/Y'],
				'filterInputOptions' => ['type'=>'date', 'data-date-format'=>'dd/MM/yyyy'],
				'attribute'=>'ngay_dieu_tri',
				// 'value' => function($data) {
				// 	return ($data->ngay_dieu_tri === null || $data->ngay_dieu_tri === '') ? null : $data->ngay_dieu_tri;
			  	// }
			],
			[
				'attribute'=>'ho_ten_khach_hang',
				'filter'=>true,
				'label'=>'Họ tên khách hàng',
				// 'value' => function ($searchModel) {
				//    return $searchModel->ten_kh();
				// }
				'value'=>'khach_hang.ho_ten',
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
            'tam_thu:integer',
			[
				'attribute'=>'id_kh',
				'filter'=>true,
				'label'=>'Còn lại',
				'format'=>['Integer'],
				'value' => function ($searchModel) {
				   return $searchModel->con_lai();
				}
			],
            //'id_kh',
            //'gio',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
