<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\NhomKhachHang;

/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel app\models\MyKhachHangSearch */

$this->title = 'Danh sách khách hàng';
$this->params['breadcrumbs'][] = $this->title;

$fmtMoney = fn($n)=> $n===null ? '—' : Yii::$app->formatter->asDecimal((int)$n, 0) . ' ₫';

/** Danh sách nhóm */
$dsNhom = ArrayHelper::map(
    NhomKhachHang::find()->orderBy(['ten_nhom'=>SORT_ASC])->asArray()->all(),
    'id','ten_nhom'
);

/** Giá trị đã chọn (để giữ checked) */
$checked = is_array($searchModel->nhom_ids ?? null) ? array_map('strval', $searchModel->nhom_ids) : [];
$selCount = count($checked);
?>
<?php
// CSS cho dropdown checkbox
$this->registerCss(<<<CSS
.nhom-dropdown{ position:relative; display:inline-block; }
.nhom-dd-btn{
  display:inline-flex; align-items:center; gap:6px;
  border:1px solid #e5e7eb; background:#fff; padding:6px 10px; border-radius:8px;
}
.nhom-dd-btn .badge{
  background:#eef2ff; color:#4338ca; border-radius:999px; padding:0 8px; font-size:12px;
}
.nhom-dd-menu{
  position:absolute; top:100%; left:0; z-index:50; min-width:320px; max-width:480px;
  margin-top:6px; background:#fff; border:1px solid #e5e7eb; border-radius:10px;
  box-shadow:0 10px 25px rgba(0,0,0,.08); padding:10px; display:none;
}
.nhom-dd-menu.open{ display:block; }
.nhom-dd-grid{ display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:6px 12px;
  max-height:180px; overflow:auto; padding:6px 0; }
.nhom-dd-foot{ display:flex; justify-content:space-between; align-items:center; gap:8px; margin-top:8px; }
.nhom-dd-actions{ display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
CSS);
?>

<div class="kh-index">
  <div style="display:flex;gap:10px;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap">
    <h1 style="margin:0;font-size:20px"><?= Html::encode($this->title) ?></h1>

    <div style="display:flex;gap:8px;align-items:flex-start;flex-wrap:wrap">
      <form method="get" action="<?= Url::to(['index']) ?>" class="form-inline" id="kh-filter-form" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-start">

        <!-- Dropdown chọn nhóm (checkbox nhiều lựa chọn) -->
        <div class="nhom-dropdown">
          <button type="button" class="nhom-dd-btn" id="btn-nhom-dd">
            <span>Nhóm KH</span>
            <span class="badge" id="nhom-count"><?= $selCount ?></span>
            <span class="caret" style="border-top:4px solid #374151;border-left:4px solid transparent;border-right:4px solid transparent;display:inline-block;height:0;width:0"></span>
          </button>

          <div class="nhom-dd-menu" id="menu-nhom-dd">
            <div class="nhom-dd-actions" style="margin-bottom:6px">
              <label style="cursor:pointer; margin:0">
                <input type="checkbox" id="chk-all-nhom"> Chọn tất cả
              </label>
              <a href="#" id="btn-clear-nhom" class="btn btn-xs btn-default">Bỏ chọn</a>
            </div>

            <div class="nhom-dd-grid" id="nhom-dd-grid">
              <?php foreach ($dsNhom as $gid => $gname): ?>
                <label style="display:flex;gap:6px;align-items:center;margin:0">
                  <input
                    type="checkbox"
                    name="MyKhachHangSearch[nhom_ids][]"
                    value="<?= (int)$gid ?>"
                    <?= in_array((string)$gid, $checked, true) ? 'checked' : '' ?>
                  >
                  <span><?= Html::encode($gname) ?></span>
                </label>
              <?php endforeach; ?>
            </div>

            <div class="nhom-dd-foot">
              <div class="text-muted" style="font-size:12px">
                Đã chọn: <b id="nhom-count-inline"><?= $selCount ?></b>
              </div>
              <div class="nhom-dd-actions">
                <button type="submit" class="btn btn-primary btn-sm">Áp dụng</button>
              </div>
            </div>
          </div>
        </div>
        <!-- /Dropdown nhóm -->

        <!-- Tìm nhanh -->
        <input type="text"
               name="MyKhachHangSearch[q]"
               value="<?= Html::encode($searchModel->q) ?>"
               class="form-control"
               placeholder="Tìm nhanh: tên / SĐT / mã BN / địa chỉ"
               style="min-width:260px">

        <button class="btn btn-default" type="submit">Lọc</button>
        <a href="<?= Url::to(['index']) ?>" class="btn btn-link">Xoá lọc</a>
      </form>

      <?= Html::a('Thêm khách hàng', ['create'], ['class' => 'btn btn-success']) ?>
    </div>
  </div>

  <?= GridView::widget([
      'dataProvider' => $dataProvider,
      'filterModel'  => $searchModel,
      'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
      'headerRowOptions' => ['style'=>'white-space:nowrap'],
      'columns' => [
          ['class' => 'yii\grid\SerialColumn'],

          [
              'attribute' => 'ma_the',
              'label' => 'Mã KH',
              'contentOptions' => ['style' => 'white-space:nowrap; width:90px; max-width:90px;'],
              'headerOptions'  => ['style' => 'white-space:nowrap; width:90px;'],
          ],
          [
              'attribute' => 'last_ngay_dieu_tri',
              'label'     => 'Ngày điều trị',
              'format'    => 'raw',
              'value'     => fn($m) => $m->last_ngay_dieu_tri
                  ? Yii::$app->formatter->asDate($m->last_ngay_dieu_tri, 'php:d/m/Y')
                  : '—',
              'filter' => Html::input(
                  'date',
                  'MyKhachHangSearch[last_ngay_dieu_tri]',
                  $searchModel->last_ngay_dieu_tri,
                  ['class'=>'form-control', 'style'=>'width:100%;min-width:0;box-sizing:border-box']
              ),
              'contentOptions' => ['style'=>'white-space:nowrap;text-align:center; width:110px; max-width:110px;'],
              'headerOptions'  => ['style'=>'white-space:nowrap;text-align:center;width:110px; max-width:110px;'],
              'filterOptions'  => ['style'=>'white-space:nowrap;text-align:center;width:110px;max-width:110px;'],
          ],
          [
              'attribute' => 'ho_ten',
              'format' => 'raw',
              'value' => fn($m) => \yii\helpers\Html::a(\yii\helpers\Html::encode($m->ho_ten), ['view','id'=>$m->id]),
          ],
          [
              'attribute' => 'sdt',
              'format' => 'raw',
              'value' => fn($m) => $m->sdt
                  ? \yii\helpers\Html::a(\yii\helpers\Html::encode($m->sdt), 'tel:' . preg_replace('/\s+/', '', $m->sdt))
                  : '—',
              'contentOptions' => ['style' => 'white-space:nowrap; width:110px; max-width:110px; text-align:center;'],
              'headerOptions'  => ['style' => 'white-space:nowrap; width:110px; text-align:center;'],
          ],
          [
              'attribute'=>'ngay_sinh',
              'label' => 'Ngày sinh',
              'format' => 'raw',
              'value' => fn($m) => $m->ngay_sinh ? Yii::$app->formatter->asDate($m->ngay_sinh, 'php:d/m/Y') : null,
              'filter' => \yii\helpers\Html::input('date', 'MyKhachHangSearch[ngay_sinh]', $searchModel->ngay_sinh,
                ['class'=>'form-control', 'style'=>'width:100%;min-width:0;box-sizing:border-box']
              ),
              'contentOptions' => ['style'=>'white-space:nowrap; text-align:center;width:110px; max-width:110px;'],
              'headerOptions'  => ['style'=>'white-space:nowrap; text-align:center;width:110px;'],
              'filterOptions'  => ['style'=>'white-space:nowrap;text-align:center;width:110px;max-width:110px;'],
          ],

          [
              'attribute' => 'last_phi',
              'label'     => 'Phí điều trị gần nhất',
              'value'     => fn($m) => $fmtMoney($m->last_phi),
              'contentOptions' => ['style'=>'text-align:right;white-space:nowrap;'],
              'headerOptions'  => ['style'=>'text-align:right;white-space:nowrap;'],
              'filter' => false,
          ],
          [
              'attribute' => 'last_tam_thu',
              'label'     => 'Tạm thu gần nhất',
              'value'     => fn($m) => $fmtMoney($m->last_tam_thu),
              'contentOptions' => ['style'=>'text-align:right;white-space:nowrap;'],
              'headerOptions'  => ['style'=>'text-align:right;white-space:nowrap;'],
              'filter' => false,
          ],
          [
              'attribute' => 'con_lai',
              'label' => 'Còn lại',
              'value' => fn($m) => Yii::$app->formatter->asDecimal((int)$m->con_lai, 0) . ' ₫',
              'contentOptions' => ['style'=>'text-align:right;white-space:nowrap;'],
              'headerOptions'  => ['style'=>'text-align:right;white-space:nowrap;'],
              'filter' => false,
          ],
          [
              'attribute' => 'tinh_trang',
              'label' => 'Tình trạng',
              'format' => 'raw',
              'value' => function($m){
                  $val = (int)$m->con_lai;
                  return $val > 0
                      ? '<span style="background:#fef2f2;color:#991b1b;border-radius:999px;padding:2px 6px;font-size:12px">Chưa tất toán</span>'
                      : '<span style="background:#ecfdf5;color:#065f46;border-radius:999px;padding:2px 6px;font-size:12px">Đã tất toán</span>';
              },
              'contentOptions' => ['style'=>'white-space:nowrap;text-align:center;width:100px; max-width:100px;'],
              'headerOptions'  => ['style'=>'white-space:nowrap;text-align:center;'],
              'filter' => Html::activeDropDownList(
                  $searchModel,
                  'tinh_trang',
                  ['no'=>'Chưa tất toán','yes'=>'Đã tất toán'],
                  ['class'=>'form-control','prompt'=>'— Tất cả —']
              ),
          ],

          [
              'class' => 'yii\grid\ActionColumn',
              'controller' => 'my-khach-hang',
              'template' => '{view} {update} {delete}',
              'contentOptions' => ['style'=>'white-space:nowrap;text-align:center;width:80px;max-width:80px;'],
              'headerOptions'  => ['style'=>'white-space:nowrap;text-align:center;width:80px;max-width:80px;'],
              'filterOptions'  => ['style'=>'width:80px;max-width:80px;'],
          ],
      ],

      'summary' => '<div style="margin:6px 0;color:#6b7280">Hiển thị {begin}–{end} / {totalCount} khách hàng</div>',
      'emptyText' => 'Chưa có khách hàng.',
      'pager' => [
          'firstPageLabel' => '«',
          'lastPageLabel'  => '»',
          'maxButtonCount' => 7,
      ],
  ]) ?>
</div>

<?php
// JS: toggle dropdown + chọn tất cả/bỏ chọn + cập nhật badge
$js = <<<JS
(function(){
  var btn = document.getElementById('btn-nhom-dd');
  var menu = document.getElementById('menu-nhom-dd');
  var form = document.getElementById('kh-filter-form');
  var chkAll = document.getElementById('chk-all-nhom');
  var clearBtn = document.getElementById('btn-clear-nhom');
  var countBadge = document.getElementById('nhom-count');
  var countInline = document.getElementById('nhom-count-inline');

  function getChecks(){
    return Array.prototype.slice.call(document.querySelectorAll('input[name="MyKhachHangSearch[nhom_ids][]"]'));
  }
  function updateCount(){
    var n = getChecks().filter(c => c.checked).length;
    countBadge.textContent = n;
    countInline.textContent = n;
    // đồng bộ trạng thái "chọn tất cả"
    var all = getChecks();
    chkAll.checked = all.length>0 && n === all.length;
    chkAll.indeterminate = n>0 && n<all.length;
  }

  // Toggle dropdown
  btn.addEventListener('click', function(e){
    e.preventDefault();
    e.stopPropagation();
    menu.classList.toggle('open');
  });

  // Click ngoài thì đóng
  document.addEventListener('click', function(){
    menu.classList.remove('open');
  });
  // Click trong menu không đóng
  menu.addEventListener('click', function(e){ e.stopPropagation(); });

  // Chọn tất cả
  chkAll.addEventListener('change', function(){
    getChecks().forEach(function(c){ c.checked = chkAll.checked; });
    updateCount();
  });

  // Bỏ chọn
  clearBtn.addEventListener('click', function(e){
    e.preventDefault();
    getChecks().forEach(function(c){ c.checked = false; });
    updateCount();
  });

  // Thay đổi từng checkbox
  document.getElementById('nhom-dd-grid').addEventListener('change', function(e){
    if (e.target && e.target.type === 'checkbox') {
      updateCount();
    }
  });

  // Cập nhật số ngay khi load
  updateCount();
})();
JS;
$this->registerJs($js);
?>
