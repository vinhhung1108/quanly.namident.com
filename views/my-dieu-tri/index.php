<?php
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel app\models\MyDieuTriSearch */
/* @var $khId int|null */

$this->title = $khId ? ('Điều trị của KH #' . (int)$khId) : 'Danh sách điều trị';
$this->params['breadcrumbs'][] = $this->title;

$fmtMoney = fn($n) => Yii::$app->formatter->asDecimal((int)$n, 0) . ' ₫';

?>
<div class="dt-index">
  <h1 style="margin-bottom:14px"><?= Html::encode($this->title) ?></h1>

  <?= GridView::widget([
      'dataProvider' => $dataProvider,
      'filterModel'  => $searchModel,
      'tableOptions' => ['class' => 'table table-striped table-hover'],
      'layout' => "{items}\n<div class=\"row\" style=\"margin-top:8px\"><div class=\"col-sm-6\">{summary}</div><div class=\"col-sm-6\" style=\"text-align:right\">{pager}</div></div>",
      'pager' => [
          'firstPageLabel' => '«',
          'lastPageLabel'  => '»',
          'maxButtonCount' => 7,
      ],
      'columns' => [
          ['class'=>'yii\grid\SerialColumn'],

          // Mã KH — nhỏ, không wrap, có filter text
          [
              'attribute' => 'ma_the',
              'label' => 'Mã KH',
              'value' => fn($m) => $m->khachHang->ma_the ?? null,
              'contentOptions' => ['style' => 'white-space:nowrap;width:90px;max-width:90px'],
              'headerOptions'  => ['style' => 'white-space:nowrap;width:90px'],
              'filterInputOptions' => ['class'=>'form-control','placeholder'=>'Tìm mã'],
          ],

          // Họ tên — link sang trang KH, có filter text
          [
              'attribute' => 'ho_ten',
              'label' => 'Họ tên',
              'format' => 'raw',
              'value' => function($m){
                  if (!$m->khachHang) return '—';
                  return Html::a(Html::encode($m->khachHang->ho_ten), ['/my-khach-hang/view','id'=>$m->khachHang->id]);
              },
              'filterInputOptions' => ['class'=>'form-control','placeholder'=>'Tìm tên'],
          ],

          // Ngày điều trị — hiển thị d/m/Y, filter bằng input date
          [
              'attribute' => 'ngay_dieu_tri',
              'label' => 'Ngày',
              'format' => 'raw',
              'value' => fn($m) => $m->ngay_dieu_tri ? Yii::$app->formatter->asDate($m->ngay_dieu_tri, 'php:d/m/Y') : null,
              'contentOptions' => ['style'=>'white-space:nowrap;text-align:center;'],
              'headerOptions'  => ['style'=>'text-align:center;white-space:nowrap'],
              'filter' => Html::input('date', Html::getInputName($searchModel, 'ngay_dieu_tri'),
                          $searchModel->ngay_dieu_tri, ['class'=>'form-control']),
          ],

          // Giờ
          [
              'attribute' => 'gio',
              'label' => 'Giờ',
              'contentOptions' => ['style'=>'white-space:nowrap;text-align:center;'],
              'headerOptions'  => ['style'=>'text-align:center;white-space:nowrap'],
              'filter' => false, // không cần lọc theo giờ
          ],

          // Bác sĩ (ưu tiên tên từ quan hệ)
          [
              'attribute' => 'bs',
              'label' => 'Bác sĩ',
              'value' => function($m){
                  if (method_exists($m, 'getBacSi') && $m->bacSi) return $m->bacSi->ho_ten;
                  return $m->bs ?: null;
              },
              'contentOptions' => ['style'=>'min-width:140px;'],
              'filter' => false,
          ],

          // Phí
          [
              'attribute' => 'phi',
              'label' => 'Phí điều trị',
              'format' => 'raw',
              'value' => fn($m) => $fmtMoney($m->phi),
              'contentOptions' => ['style'=>'text-align:right;white-space:nowrap;'],
              'headerOptions'  => ['style'=>'text-align:right;white-space:nowrap;'],
              'filter' => false,
          ],

          // Tạm thu
          [
              'attribute' => 'tam_thu',
              'label' => 'Tạm thu',
              'format' => 'raw',
              'value' => fn($m) => $fmtMoney($m->tam_thu),
              'contentOptions' => ['style'=>'text-align:right;white-space:nowrap;'],
              'headerOptions'  => ['style'=>'text-align:right;white-space:nowrap;'],
              'filter' => false,
          ],

          [
              'class'=>'yii\grid\ActionColumn',
              'controller' => 'my-dieu-tri',
              'template' => '{view} {update} {delete}',
          ],
      ],
      'summary' => '<span class="text-muted">Hiển thị {begin}–{end} / {totalCount} dòng</span>',
  ]) ?>
</div>
