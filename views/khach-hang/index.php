<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\KhachHangSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Danh Sách Khách Hàng';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="khach-hang-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Tạo Khách Hàng Mới', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            // [
			// 	'attribute'=>'id',
			// 	'contentOptions'=>['style'=>'max-width:30px;'],
			// 	'headerOptions' => ['width' => '30px'],
			// 	'contentOptions'=>['style'=>'text-align:center;'],
			// ],
			[
				'attribute'=>'ma_the',
				'headerOptions' => ['width' => '80px'],
				'contentOptions'=>['style'=>'text-align:center;max-width:150px'],
				'value'=>'ma_the',
				'label'=>'Mã BN',
			],
			[
				'format'=>['date', 'php:d/m/Y'],
				'filterInputOptions' => ['type'=>'date', 'data-date-format'=>'dd/mm/yyyy'],
				'attribute'=>'ngay_dieu_tri',
				'value' => function($data) {
					return ($data->ngay_dieu_tri === null || $data->ngay_dieu_tri === '') ? null : $data->ngay_dieu_tri;
			  	}
			],
            'ho_ten',
            /*[
				'attribute'=>'sdt',
				//'format'=>['a','tel:sdt'],
			],*/
			'sdt:url',
            //'ngay_sinh',
			[
				'format'=>['date', 'php:d/m/Y'],
				'filterInputOptions' => ['type'=>'date', 'data-date-format'=>'dd/mm/yyyy'],
				'attribute'=>'ngay_sinh',
				'value' => function($data) {
					return ($data->ngay_sinh === null || $data->ngay_sinh === '') ? null : $data->ngay_sinh;
			  	}
			],
            //'dieu_tri:ntext',
            [				
				'filterInputOptions' => ['type'=>'date', 'data-date-format'=>'dd/mm/yyyy'],
				'attribute'=>'ngay_hen',
				'format'=>['date', 'php:d/m/Y'],
				'value' => function($data) {
					return ($data->ngay_hen === null || $data->ngay_hen === '') ? null : $data->ngay_hen;
			  	}
			],

			//'ngay_dieu_tri',
            //'tong_phi',
			/** [
				'format'=>['decimal',0],
				'attribute' => 'tong_phi',
				'contentOptions'=>['style'=>'text-align:right;'],
			], **/
            [
				'format'=>['decimal',0],
				'attribute' => 'tam_thu_last',
				'contentOptions'=>['style'=>'text-align:right;'],
				'label' => 'Thu (Last)',
			],		
			[
				'format'=>['decimal',0],
				'attribute' => 'con_lai',
				'contentOptions'=>['style'=>'text-align:right;'],
			],
            //'bs_dieu_tri',
            //'gioi_thieu',
            //'hinh_anh',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
