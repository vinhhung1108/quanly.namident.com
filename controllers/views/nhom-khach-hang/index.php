<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\NhomKhachHangSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Danh sách nhóm khách hàng';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nhom-khach-hang-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Thêm Nhóm khách hàng', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
           ['class' => 'yii\grid\SerialColumn'],

           // 'id',
            'ten_nhom',
			'ghi_chu',
            ['class' => 'yii\grid\ActionColumn',
			'contentOptions'=>['style'=>'max-width:40px;'],
			],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
