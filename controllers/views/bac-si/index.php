<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\BacSiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Danh sách bác sĩ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bac-si-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Thêm Bác sĩ', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
           // ['class' => 'yii\grid\SerialColumn'],

           // 'id',
            'ho_ten',
            //'nam_sinh',
			[
					'attribute'=>'nam_sinh',
					'filterInputOptions' => ['type'=>'date', 'data-date-format'=>'dd/MM/yyyy'],
					'format'=>['date', 'dd/MM/yyyy'],
			],

            ['class' => 'yii\grid\ActionColumn',
			'contentOptions'=>['style'=>'max-width:40px;'],
			],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
