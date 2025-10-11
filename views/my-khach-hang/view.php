<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal; // Bootstrap 5: use yii\bootstrap5\Modal;

/* @var $model app\models\MyKhachHang|app\models\KhachHang */
/* @var $dieuTriProvider yii\data\ActiveDataProvider */

$this->title = 'Khách hàng: ' . $model->ho_ten;
$this->params['breadcrumbs'][] = ['label' => 'Danh sách Khách hàng', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$fmtVnd = fn($n) => number_format((int)$n, 0, '.', ',') . ' ₫';

/** Ưu tiên dùng tổng đã lưu ở bảng KH để tránh SUM mỗi lần render */
$sumPhi    = (int)($model->tong_phi ?? 0);
$sumTamThu = (int)($model->tam_thu  ?? 0);
$conLai    = (int)($model->con_lai  ?? ($sumPhi - $sumTamThu));

$gioiTinhLabel = (function($gt){
    if ($gt === '0' || $gt === 0) return 'Nam';
    if ($gt === '1' || $gt === 1) return 'Nữ';
    if (is_string($gt) && trim($gt) !== '') return $gt;
    return '—';
})($model->gioi_tinh);

$ngaySinhText = $model->ngay_sinh ? Yii::$app->formatter->asDate($model->ngay_sinh, 'php:d/m/Y') : '—';
$maTheText    = $model->ma_the ?: '—';
$diaChiText   = $model->dia_chi ?: '—';
$gioiThieuTx  = $model->gioi_thieu ?: '—';
$ghiChuText   = $model->ghi_chu ?: '—';

$this->registerCss(<<<CSS
/* ====== SCOPED TO THIS PAGE ====== */
.kh-view .wrap > .container,
.kh-view .container{max-width:100%!important;width:100%!important;padding:0 12px;}

/* Cards */
.kh-view .card{background:#fff;border:1px solid #e5e7eb;border-radius:14px}
.kh-view .card + .card{margin-top:18px}
.kh-view .card-body{padding:18px}
.kh-view .card-title{font-size:18px;font-weight:600;margin:0 0 12px}
.kh-view .subtle{color:#6b7280;font-size:13px}

/* Header 3 cột: left | appointment | buttons */
.kh-view .kh-header{
  display:grid;
  grid-template-columns: minmax(320px,1fr) minmax(360px,1.2fr) auto;
  grid-template-areas: "left appt btns";
  gap:16px; align-items:start; padding:16px 18px; border-bottom:1px solid #eef0f2;
}
.kh-view .header-left{ grid-area:left; min-width:260px; }
.kh-view #appointment-box.appt-inline{ grid-area:appt; min-width:340px; margin:0 !important; overflow:hidden; }
.kh-view .btns{ grid-area:btns; display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end; }

/* Title, meta */
.kh-view .kh-title{font-size:20px;font-weight:700;margin:0 0 4px}
.kh-view .kh-meta{font-size:12px;font-weight:500}

/* Info key-value (gọn, không dùng bảng) */
.kh-view .kv{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
.kh-view .item{display:flex;gap:8px;align-items:flex-start;padding:10px;border:1px solid #eef0f2;border-radius:12px;background:#fafafa}
.kh-view .item .k{min-width:110px;color:#6b7280;font-size:12px;white-space:nowrap}
.kh-view .item .v{font-size:14px;line-height:1.5}

/* Bảng điều trị */
.kh-view .table-card .tbl-head{
  padding:14px 18px;border-bottom:1px solid #eef0f2;display:flex;justify-content:space-between;align-items:center
}
.kh-view .table-card .tbl-body{padding:12px 18px}

/* Responsive */
@media (max-width:1200px){
  .kh-view .kh-header{
    grid-template-columns: 1fr auto;
    grid-template-areas:
      "left btns"
      "appt btns";
  }
  .kh-view .btns{ align-self:start; }
}
@media (max-width:768px){
  .kh-view .kv{grid-template-columns:1fr}
  .kh-view .kh-header{
    grid-template-columns: 1fr;
    grid-template-areas:
      "left"
      "appt"
      "btns";
    gap:12px;
  }
  .kh-view .btns{ justify-content:flex-start; }
}
CSS);
?>

<div class="kh-view page-wrap">
  <!-- HEADER -->
  <div class="card">
    <div class="kh-header">
      <div class="header-left">
        <div class="kh-title">
          <?= Html::encode($model->ho_ten) ?>
          <span class="kh-meta">
            <?= $model->ma_the ? ('/ Mã KH: ' . Html::encode($model->ma_the)) : '' ?>
            <strong> <?= $model->sdt ? '/ Sđt: ' . Html::encode($model->sdt) : 'Chưa có SĐT' ?></strong>
          </span>
        </div>
        <div class="subtle" style="margin-top:4px">
          <strong><?= Html::encode($ngaySinhText) ?></strong> / <?= Html::encode($gioiTinhLabel) ?>
          <?= $gioiThieuTx ? ' / Nguồn GT: ' . Html::encode($gioiThieuTx) : '' ?><br>
          <?= $diaChiText ? 'Đ/c: '.nl2br(Html::encode($diaChiText)) : '' ?><br>
          <?= Html::a('Sửa thông tin KH', ['update', 'id' => $model->id], ['class'=>'link']) ?>
        </div>
      </div><!--end header-left-->
      <div id="appointment-box" class="appt-inline">
        <?= $this->render('_appointment_box', ['model' => $model]) ?>
      </div>
      <div class="pull-right">
        <div class="btns">
          <?= Html::a('Lịch hẹn', '#', [
            'class' => 'btn btn-info',
            'id'    => 'btn-add-appt',
            'data-url' => \yii\helpers\Url::to(['appointment-modal', 'id' => $model->id]),
          ]) ?>
          <?= Html::a('Xoá KH', ['delete', 'id' => $model->id], [
            'class'=>'btn btn-danger',
            'data' => ['confirm'=>'Xoá khách hàng này?', 'method'=>'post']
          ]) ?>
          <?= Html::a('Tạo phiếu thu', ['/my-khach-hang/receipt', 'id' => $model->id], ['class'=>'btn btn-warning']) ?>
        </div>
          <?php if ($model->nhoms): ?>
              <div style="margin-top:12px">
                <span class="subtle">Nhóm:</span>
                <?php foreach ($model->nhoms as $nh): ?>
                  <span class="label label-default" style="margin-right:6px"><?= Html::encode($nh->ten_nhom) ?></span>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
      </div><!--end pull-right-->
    </div><!-- end kh-header-->
    <div class="card-body">
      <?php if($ghiChuText && $ghiChuText !== '—'): ?>
      <div class="kv">
        <div class="item" style="grid-column:1 / -1">
          <div class="k">Ghi chú:</div>
          <div class="v">
            <?= $ghiChuText === '—' ? '<span class="subtle">—</span>' : nl2br(Html::encode($ghiChuText)) ?>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- HỒ SƠ Y KHOA (chỉ hiện khi có dữ liệu) -->
      <?php if ($model->chan_doan || $model->tien_su_benh): ?>
        <div style="margin-top:16px">
            <?php if ($model->chan_doan): ?>
              <div style="margin-bottom:8px">
                <span class="subtle" style="display:inline-block;min-width:110px">Chẩn đoán</span>
                <span><?= nl2br(Html::encode($model->chan_doan)) ?></span>
              </div>
            <?php endif; ?>
            <?php if ($model->tien_su_benh): ?>
              <div>
                <span class="subtle" style="display:inline-block;min-width:110px">Tiền sử bệnh</span>
                <span style="color:#b91c1c;font-weight:600"><?= nl2br(Html::encode($model->tien_su_benh)) ?></span>
              </div>
            <?php endif; ?>
        </div>
      <?php endif; ?>
      <!-- /HỒ SƠ Y KHOA -->
    </div>
  </div>

  <!-- FORM ĐIỀU TRỊ (INLINE) -->
  <div id="dt-inline-box" class="card" style="display:none;margin-top:16px">
    <div class="kh-view">
      <div class="tbl-head">
        <div class="card-title" style="margin:0" id="dt-inline-title"></div>
        <div class="btns">
          <button type="button" class="btn btn-default btn-sm" id="btn-hide-inline">Đóng</button>
        </div>
      </div>
    </div>
    <div class="card-body" id="dt-inline-body">
      <div id="dt-inline-loading" class="text-center subtle" style="padding:20px;display:none">Đang tải form…</div>
      <div id="dt-inline-content"></div>
    </div>
  </div>
  <!-- /FORM ĐIỀU TRỊ -->

  <!-- BẢNG ĐIỀU TRỊ -->
  <div class="card table-card">
    <div class="tbl-head">
      <div class="card-title" style="margin:0">
        <?= Html::a('Thêm điều trị', '#', [
          'class' => 'btn btn-success',
          'id'    => 'btn-inline-dt',
          'data-url' => \yii\helpers\Url::to(['/my-dieu-tri/create', 'khId' => $model->id, 'inline' => 1]),
        ]) ?>
      </div>
      <div class="subtle">
          <?= Html::a('Xem tất cả ảnh', '#', [
              'id' => 'btn-all-images',
              'class' => 'btn btn-link btn-xs',
              'style' => 'padding:0;vertical-align:baseline',
              'data-url' => \yii\helpers\Url::to(['my-khach-hang/all-images', 'id' => (int)$model->id]),
          ]) ?>
            &nbsp;·&nbsp;
        Tổng phí: <b><?= $fmtVnd($sumPhi) ?></b> ·
        Tạm thu: <b><?= $fmtVnd($sumTamThu) ?></b> ·
        Còn lại: <b><?= $fmtVnd($conLai) ?></b>
      </div>
    </div>

    <div class="tbl-body">
      <?= GridView::widget([
        'dataProvider' => $dieuTriProvider,
        'tableOptions' => ['class'=>'table table-hover'],
        'columns' => [
          ['class'=>'yii\grid\SerialColumn'],
          [
            'attribute'=>'ngay_dieu_tri',
            'format'=>['date','php:d/m/Y'],
            'contentOptions'=>['style'=>'white-space:nowrap;'],
          ],
          [
            'attribute'=>'gio',
            'contentOptions'=>['style'=>'white-space:nowrap;'],
          ],
          [
            'label' => 'Nội dung',
            'format' => 'raw',
            'value' => function($m){
              $parts = [];
              if (method_exists($m, 'getDvItems')) {
                foreach (($m->dvItems ?? []) as $dv) {
                  /** @var \app\models\MyDieuTriDv $dv */
                  $name = $dv->ten_dv ?: ($dv->dichVu->ten ?? '');
                  $meta = [];
                  if ((int)$dv->so_luong > 1) $meta[] = 'x' . (int)$dv->so_luong;
                  if (!empty($dv->rang_so))   $meta[] = 'răng ' . $dv->rang_so;
                  $line = trim($name);
                  if ($meta) $line .= ' (' . implode(' · ', $meta) . ')';
                  if ($line !== '') $parts[] = \yii\helpers\Html::encode($line);
                }
              }
              if (!empty($m->noi_dung)) {
                foreach (preg_split("/\\r?\\n/", trim((string)$m->noi_dung)) as $row) {
                  $row = trim($row);
                  if ($row !== '') $parts[] = \yii\helpers\Html::encode($row);
                }
              }
              if (!$parts) return '<span class="text-muted">—</span>';
              return '<div style="white-space:normal;max-width:520px">'.implode('<br>', $parts).'</div>';
            },
            'contentOptions'=>['style'=>'white-space:normal;max-width:520px'],
          ],
          [
            'label' => 'Bác sĩ',
            'format' => 'raw',
            'value' => function($m){
                $names = [];
                if (method_exists($m, 'getDvItems')) {
                    foreach (($m->dvItems ?? []) as $dv) {
                        if (!empty($dv->bs_id) && isset($dv->bacSi)) {
                            $names[] = Html::a(\yii\helpers\Html::encode($dv->bacSi->ho_ten),['bac-si/view','id'=>$dv->bs_id],[
                                'data-pjax' => '0','target'=>'_blank','title'=>'Xem thông tin bác sĩ',
                            ]);
                        } else {
                            $names[] = '<span class="text-muted">—</span>';
                        }
                    }
                }
                $names = array_values(array_filter($names));
                return $names ? implode('<br>', $names) : '<span class="text-muted">—</span>';
            },
            'contentOptions' => ['style' => 'white-space:nowrap;'],
            'headerOptions'  => ['style' => 'white-space:nowrap;'],
          ],
          [
            'label'=>'Phí điều trị',
            'format'=>'raw',
            'value'=> function($m){
              $phis = [];
              if(method_exists($m, 'getDvItems')){
                foreach (($m->dvItems ?? []) as $dv) {
                  $phis[] = $dv->thanh_tien !== null
                    ? number_format((int)$dv->thanh_tien, 0, '.', ',') . ' ₫'
                    : 0;
                }
              }
              return ($phis !== [] && $phis !== null) ? implode('<br>', $phis): '<span class="text-muted">—</span>';
            },
            'contentOptions' => ['style' => 'white-space:nowrap; text-align:right;'],
            'headerOptions'  => ['style' => 'white-space:nowrap;text-align:right;'],
          ],
          [
            'attribute'=>'phi',
            'label'=>'Tổng Phí',
            'value'=>fn($m)=> number_format((int)$m->phi, 0, '.', ',') . ' ₫',
            'contentOptions'=>['style'=>'text-align:right;white-space:nowrap;'],
            'headerOptions'=>['style'=>'text-align:right'],
          ],
          [
            'attribute'=>'tam_thu',
            'label'=>'Tạm thu',
            'value'=>fn($m)=> number_format((int)$m->tam_thu, 0, '.', ',') . ' ₫',
            'contentOptions'=>['style'=>'text-align:right;white-space:nowrap;'],
            'headerOptions'=>['style'=>'text-align:right'],
          ],
          [
            'class' => 'yii\grid\ActionColumn',
            'controller' => 'my-dieu-tri',
            'template' => '{update} {view} {delete}',
            'buttons' => [
              'update' => function ($url, $m) {
                  $inlineUrl = \yii\helpers\Url::to(['/my-dieu-tri/update', 'id' => $m->id, 'inline' => 1]);
                  return Html::a(
                      Html::tag('span', '', ['class' => 'glyphicon glyphicon-pencil']),
                      $inlineUrl,
                      [
                        'class' => 'btn-link dt-edit-inline',
                        'title' => 'Cập nhật (inline)',
                        'aria-label' => 'Cập nhật',
                        'data-id' => (int)$m->id,
                        'data-url' => $inlineUrl,
                        'data-pjax' => '0',
                      ]
                  );
              },
              'view' => function ($url, $m) {
                  $modalUrl = \yii\helpers\Url::to(['/my-dieu-tri/view-image', 'id' => $m->id, 'modal' => 1]);
                  return Html::a(
                      Html::tag('span', '', ['class' => 'glyphicon glyphicon-eye-open']),
                      $modalUrl,
                      [
                        'class' => 'btn-link dt-open-modal',
                        'title' => 'Xem chi tiết (ảnh) trong modal',
                        'aria-label' => 'Xem chi tiết',
                        'data-url' => $modalUrl,
                        'data-pjax' => '0',
                      ]
                  );
              },
              'delete' => function ($url, $m) {
                  return Html::a(
                      Html::tag('span', '', ['class' => 'glyphicon glyphicon-trash']),
                      ['/my-dieu-tri/delete', 'id' => $m->id],
                      [
                        'class' => 'btn-link text-danger',
                        'title' => 'Xoá điều trị',
                        'aria-label' => 'Xoá điều trị',
                        'data' => [
                          'confirm' => 'Xoá điều trị này?',
                          'method'  => 'post',
                        ],
                        'data-pjax' => '0',
                      ]
                  );
              },
            ],
            'contentOptions' => [
              'style'=>'white-space:nowrap;text-align:center;width:90px;max-width:90px;'
            ],
            'headerOptions'  => [
              'style'=>'white-space:nowrap;text-align:center;'
            ],
          ],
        ],
        'summary' => '<span class="subtle">Hiển thị {begin}–{end} / {totalCount} dòng</span>',
        'pager' => [
          'firstPageLabel' => '«',
          'lastPageLabel'  => '»',
          'maxButtonCount' => 5,
        ],
      ]) ?>
      <div class="subtle" style="margin-top:6px">
        * Số liệu tổng dựa trên hồ sơ khách hàng (không phụ thuộc phân trang).
      </div>
    </div>
  </div>
</div>

<?php
// ===== Modal Bootstrap để load form lịch hẹn (AJAX) =====
Modal::begin([
  'id' => 'appModal',
  'header' => '<h4 class="modal-title">Lịch hẹn</h4>',
  'size' => Modal::SIZE_DEFAULT,
]);
echo '<div class="modal-body"><div class="text-center" style="padding:20px">Đang tải…</div></div>';
Modal::end();

// ===== Modal xem điều trị (ảnh) =====
Modal::begin([
  'id' => 'dtViewModal',
  'header' => '<h4 class="modal-title">Chi tiết điều trị</h4>',
  'size' => Modal::SIZE_LARGE,
  'options' => ['tabindex' => false],
  'clientOptions' => ['backdrop' => 'static', 'keyboard' => true],
]);
echo '<div class="modal-body"><div class="text-center" style="padding:20px">Đang tải…</div></div>';
Modal::end();

Modal::begin([
  'id' => 'khAllImgModal',
  'header' => '<h4 class="modal-title">Tất cả ảnh</h4>',
  'size' => Modal::SIZE_LARGE,   // script trong view sẽ tự đẩy lên modal-xl + 95vw
  'options' => ['tabindex' => false],
  'clientOptions' => ['backdrop' => 'static', 'keyboard' => true],
]);
echo '<div class="modal-body"><div class="text-center" style="padding:20px">Đang tải…</div></div>';
Modal::end();

// ===== JS: mở modal & submit form AJAX =====
$js = <<<JS
// Mở modal và tải form
$('#btn-add-appt').on('click', function(e){
  e.preventDefault();
  var url = $(this).data('url');
  var \$modal = $('#appModal');
  \$modal.find('.modal-body').html('<div class="text-center" style="padding:20px">Đang tải…</div>');
  \$modal.modal('show').find('.modal-body').load(url);
});

// Submit form trong modal (AJAX)
$(document).on('submit', '#appointment-form', function(e){
  e.preventDefault();
  var \$form = $(this);
  $.post(\$form.attr('action'), \$form.serialize())
    .done(function(res){
      if (res && res.ok) {
        $('#appModal').modal('hide');
        if (res.html) { $('#appointment-box').html(res.html); }
        $('<div class="alert alert-success" style="position:fixed;right:16px;bottom:16px;z-index:9999">Đã lưu lịch hẹn</div>')
          .appendTo('body').delay(1000).fadeOut(400, function(){ $(this).remove(); });
      } else {
        if (typeof res === 'string') {
          $('#appModal .modal-body').html(res);
        }
      }
    })
    .fail(function(xhr){
      var ct = xhr.getResponseHeader('Content-Type') || '';
      if (ct.indexOf('text/html') >= 0 && xhr.responseText) {
        $('#appModal .modal-body').html(xhr.responseText);
      } else {
        alert('Không thể lưu lịch hẹn. Vui lòng thử lại.');
      }
    });
});

// Click "Xoá lịch hẹn" trong modal
$(document).on('click', '#btn-clear-appt', function(){
  if (!confirm('Xoá rỗng toàn bộ lịch hẹn của khách hàng này?')) return;
  var \$form = $('#appointment-form');
  $('#appt-clear').val('1');
  \$form.trigger('submit');
});
JS;
$this->registerJs($js);

// ===== JS Inline: Tải form điều trị (AJAX) + mở modal chọn răng theo HƯỚNG 1 =====
$jsInline = <<<JS
(function(){
  var \$btnCreate = \$('#btn-inline-dt');            // nút "Thêm điều trị"
  var \$box       = \$('#dt-inline-box');
  var \$title     = \$('#dt-inline-title');
  var \$loading   = \$('#dt-inline-loading');
  var \$content   = \$('#dt-inline-content');

  // === HƯỚNG 1: Hàm mở modal chọn răng bằng API gốc Bootstrap ===
  function openToothModal() {
    var el = document.getElementById('toothModal');
    if (!el) return;
    // Bootstrap 3 (jQuery)
    if (window.jQuery && jQuery.fn && typeof jQuery.fn.modal === 'function') {
      jQuery(el).modal('show'); return;
    }
    // Bootstrap 5 (no jQuery)
    if (window.bootstrap && window.bootstrap.Modal) {
      window.bootstrap.Modal.getOrCreateInstance(el).show(); return;
    }
    // Fallback
    el.style.display = 'block';
    el.classList.add('in','show');
    document.body && document.body.classList.add('modal-open');
  }

  function evalInlineScripts(\$ctx){
    try {
      \$ctx.find('script').each(function(){
        var \$script = \$(this);
        var src = \$script.attr('src');
        if (src) {
          $.getScript(src);
        } else {
          var code = \$script.html();
          if (code) { $.globalEval(code); }
        }
      });
    } catch(e){ console.error(e); }
  }

  function initInlineDieuTriForm(\$container){
    if (!\$container || !\$container.length) return;
    var \$form = \$container.find('#dt-form');
    if (!\$form.length) return;
    if (\$form.data('inlineInit')) return;

    // Đưa #toothModal ra <body> để modal hoạt động ổn định (overlay/z-index)
    var \$tooth = \$container.find('#toothModal');
    if (\$tooth.length) { \$tooth.appendTo(document.body); }

    \$container.off('.dtInline');
    \$form.data('inlineInit', true);

    // ====== TỰ ĐIỀN NGÀY/GIỜ KHI TẠO MỚI ======
    var now = new Date();
    var pad = function(n){ return String(n).padStart(2, '0'); };
    var \$autoDate = \$form.find('[data-autodate="1"]');
    if (\$autoDate.length && !\$autoDate.val()) {
      \$autoDate.val(now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate()));
      \$autoDate.trigger('change');
    }
    var \$autoTime = \$form.find('[data-autonow="1"]');
    if (\$autoTime.length && !\$autoTime.val()) {
      \$autoTime.val(pad(now.getHours()) + ':' + pad(now.getMinutes()));
      \$autoTime.trigger('change');
    }

    var hasPerService = \$form.find('.dv-tam-thu').length > 0;

    function parseMoney(val){
      if (val === null || val === undefined) return 0;
      var cleaned = String(val).replace(/[^\\d\\-]/g, '');
      return cleaned ? parseInt(cleaned, 10) : 0;
    }
    function fmtMoney(num){
      var n = parseInt(num || 0, 10);
      return n.toLocaleString('en-US');
    }
    function applyMoneyMask(\$inputs){
      if (!\$inputs || !\$inputs.length) return;

      if (\$.fn && \$.fn.inputmask) {
        \$inputs.each(function(){
          var \$el = \$(this);
          try { \$el.inputmask('remove'); } catch(e){}
          \$el.inputmask({
            alias: 'decimal',
            groupSeparator: ',',
            digits: 0,
            autoGroup: true,
            rightAlign: false,
            removeMaskOnSubmit: true
          });
        });
        return;
      }

      if (window.Inputmask) {
        \$inputs.each(function(){
          try {
            var inst = new window.Inputmask({
              alias: 'decimal',
              groupSeparator: ',',
              digits: 0,
              autoGroup: true,
              rightAlign: false,
              removeMaskOnSubmit: true
            });
            inst.remove(this);
            inst.mask(this);
          } catch(e){}
        });
        return;
      }

      \$inputs.each(function(){
        var el = this;
        if (el._moneyFallbackHandler) {
          el.removeEventListener('input', el._moneyFallbackHandler);
        }
        var handler = function(){
          var raw = el.value || '';
          var negative = raw.trim().charAt(0) === '-';
          var digits = raw.replace(/\\D/g, '');
          if (!digits) {
            el.value = negative ? '-' : '';
            return;
          }
          var withCommas = digits.replace(/\\B(?=(\\d{3})+(?!\\d))/g, ',');
          el.value = negative ? '-' + withCommas : withCommas;
        };
        el._moneyFallbackHandler = handler;
        el.addEventListener('input', handler);
        handler();
      });
    }
    function recalcRow(\$tr){
      var price = parseMoney(\$tr.find('.dv-don-gia').val());
      var qty = parseInt(\$tr.find('.dv-qty').val() || 1, 10);
      if (isNaN(qty) || qty < 1) qty = 1;
      var amt = price * qty;
      \$tr.find('.dv-thanh-tien').text(fmtMoney(amt));
      return amt;
    }
    function recalcTotals(){
      var sumPhi = 0;
      var sumTam = 0;
      \$form.find('#dv-table tbody tr').each(function(){
        var \$tr = \$(this);
        sumPhi += recalcRow(\$tr);
        if (hasPerService) {
          sumTam += parseMoney(\$tr.find('.dv-tam-thu').val());
        }
      });
      \$form.find('#dv-total').text(fmtMoney(sumPhi));
      if (hasPerService) {
        \$form.find('#dv-tamthu-total').text(fmtMoney(sumTam));
      }
      \$form.find('#phi-total').val(sumPhi);
      var \$tam = \$form.find('#tamthu-mask');
      if (\$tam.length && hasPerService) {
        if ($.fn.inputmask) {
          try { \$tam.inputmask('setvalue', sumTam); }
          catch(e){ \$tam.val(sumTam ? fmtMoney(sumTam) : ''); }
        } else {
          \$tam.val(sumTam ? fmtMoney(sumTam) : '');
        }
      }
      return {
        phi: sumPhi,
        tamThu: hasPerService ? sumTam : parseMoney(\$form.find('#tamthu-mask').val())
      };
    }

    function closeMenus(except){
      \$form.find('.dv-menu').not(except || null).removeClass('open dropup');
    }
    $(document).off('.dtInlineCombo');
    $(document).on('click.dtInlineCombo', function(e){
      if (!$(e.target).closest('.dv-combo').length) {
        closeMenus();
      }
    });

    var maskSelector = hasPerService ? '.dv-don-gia, .dv-tam-thu' : '.dv-don-gia';
    applyMoneyMask(\$form.find(maskSelector));
    recalcTotals();

    // ====== COMBO DỊCH VỤ ======
    \$container.on('click.dtInline', '.dv-lookup', function(e){
      e.preventDefault();
      e.stopPropagation();
      var \$combo = \$(this).closest('.dv-combo');
      var \$menu = \$combo.find('.dv-menu');
      if (!\$menu.length) return;
      var wasOpen = \$menu.hasClass('open');
      closeMenus(\$menu);
      if (!wasOpen) {
        \$menu.addClass('open');
        var rect = \$combo.get(0).getBoundingClientRect();
        var estimated = \$menu.outerHeight() || 320;
        var spaceBelow = window.innerHeight - rect.bottom;
        var spaceAbove = rect.top;
        if (spaceBelow < estimated && spaceAbove > spaceBelow) {
          \$menu.addClass('dropup');
        } else {
          \$menu.removeClass('dropup');
        }
        var \$search = \$menu.find('.dv-search');
        if (\$search.length) {
          \$search.val('').trigger('input');
          setTimeout(function(){ \$search.trigger('focus'); }, 0);
        }
      }
    });

    \$container.on('input.dtInline', '.dv-menu .dv-search', function(){
      var query = (\$(this).val() || '').toString().toLowerCase();
      \$(this).closest('.dv-menu').find('.dv-opt').each(function(){
        var name = (\$(this).data('ten') || '').toString().toLowerCase();
        \$(this).toggle(name.indexOf(query) >= 0);
      });
    });

    \$container.on('click.dtInline', '.dv-menu .dv-opt', function(){
      var \$opt = \$(this);
      var id = \$opt.data('id');
      var ten = \$opt.data('ten');
      var price = parseInt(\$opt.data('price') || 0, 10);
      var \$combo = \$opt.closest('.dv-combo');
      var \$tr = \$combo.closest('tr');
      \$combo.find('.dv-ten').val(ten || '');
      \$combo.find('.dv-id-hidden').val(id || '');
      var \$price = \$tr.find('.dv-don-gia');
      if ($.fn.inputmask && \$price.inputmask) {
        try { \$price.inputmask('setvalue', price); }
        catch(e){ \$price.val(fmtMoney(price)); }
      } else {
        \$price.val(fmtMoney(price));
      }
      closeMenus();
      recalcRow(\$tr);
      recalcTotals();
    });

    \$container.on('input.dtInline', '.dv-ten', function(){
      \$(this).closest('.dv-combo').find('.dv-id-hidden').val('');
    });

    \$container.on('click.dtInline', '#dv-add', function(e){
      e.preventDefault();
      var \$tpl = \$form.find('#dv-row-tpl');
      if (!\$tpl.length) return;
      var html = \$tpl.html();
      if (!html) return;
      var idx = \$form.find('#dv-table tbody tr').length;
      html = html.replace(/__i__/g, idx);
      var \$row = \$(html);
      \$form.find('#dv-table tbody').append(\$row);
      applyMoneyMask(\$row.find(maskSelector));
      recalcRow(\$row);
      recalcTotals();
    });

    \$container.on('click.dtInline', '#dv-table .dv-remove', function(e){
      e.preventDefault();
      \$(this).closest('tr').remove();
      recalcTotals();
    });

    \$container.on('input.dtInline change.dtInline', '#dv-table .dv-don-gia, #dv-table .dv-qty', function(){
      var \$tr = \$(this).closest('tr');
      recalcRow(\$tr);
      recalcTotals();
    });

    if (hasPerService) {
      \$container.on('input.dtInline change.dtInline', '#dv-table .dv-tam-thu', function(){
        recalcTotals();
      });
    }

    \$container.on('click.dtInline', '#btnEqual', function(e){
      e.preventDefault();
      if (hasPerService) {
        \$form.find('#dv-table tbody tr').each(function(){
          var \$tr = \$(this);
          var amt = recalcRow(\$tr);
          var \$tam = \$tr.find('.dv-tam-thu');
          if (\$tam.length) {
            if ($.fn.inputmask) {
              try { \$tam.inputmask('setvalue', amt); }
              catch(err){ \$tam.val(fmtMoney(amt)); }
            } else {
              \$tam.val(fmtMoney(amt));
            }
          }
        });
        recalcTotals();
      } else {
        var totals = recalcTotals();
        var \$tam = \$form.find('#tamthu-mask');
        if (\$tam.length) {
          if ($.fn.inputmask) {
            try { \$tam.inputmask('setvalue', totals.phi); }
            catch(err){ \$tam.val(fmtMoney(totals.phi)); }
          } else {
            \$tam.val(fmtMoney(totals.phi));
          }
          \$tam.trigger('input').trigger('change');
        }
      }
    });

    // ====== CHỌN RĂNG (dùng openToothModal của HƯỚNG 1) ======
    var currentToothIdx = null;

    \$container.on('click.dtInline', '.dv-rang-open', function(){
      currentToothIdx = \$(this).data('index');
      var \$modal = \$('#toothModal');
      if (!\$modal.length) { return; }
      \$modal.find('input.tooth').prop('checked', false);

      var \$holder = \$('.dv-rang-holder[data-index="'+currentToothIdx+'"]').first();
      var selected = [];
      \$holder.find('input[type="hidden"]').each(function(){ selected.push(\$(this).val()); });
      selected.forEach(function(val){
        \$modal.find('input.tooth[value="'+val+'"]').prop('checked', true);
      });

      openToothModal(); // <— Hướng 1
    });

    // Áp dụng chọn răng
    \$(document).off('click.toothApply','#tooth-apply').on('click.toothApply','#tooth-apply', function(){
      if (currentToothIdx === null) return;
      var \$modal = \$('#toothModal');
      var vals = [];
      \$modal.find('input.tooth:checked').each(function(){ vals.push(\$(this).val()); });
      var \$holder = \$('.dv-rang-holder[data-index="'+currentToothIdx+'"]').first();
      var \$text   = \$holder.closest('.dv-rang-box').find('.dv-rang-text');
      \$holder.empty();
      vals.forEach(function(v){ \$holder.append('<input type="hidden" name="dv_rang['+currentToothIdx+'][]" value="'+v+'">'); });
      \$text.text(vals.length ? vals.join(',') : '—');

      // Đóng modal theo cả BS3/BS5
      if (window.jQuery && jQuery.fn && typeof jQuery.fn.modal === 'function') { \$modal.modal('hide'); }
      else if (window.bootstrap && window.bootstrap.Modal) { window.bootstrap.Modal.getOrCreateInstance(\$modal[0]).hide(); }
      else { \$modal.hide().removeClass('in show'); document.body && document.body.classList.remove('modal-open'); }
    });

    \$(document).off('click.toothClose','#toothModal [data-dismiss="modal"], #toothModal [data-bs-dismiss="modal"]')
      .on('click.toothClose','#toothModal [data-dismiss="modal"], #toothModal [data-bs-dismiss="modal"]', function(e){
        e.preventDefault();
        var \$modal = \$('#toothModal');
        if (window.jQuery && jQuery.fn && typeof jQuery.fn.modal === 'function') { \$modal.modal('hide'); }
        else if (window.bootstrap && window.bootstrap.Modal) { window.bootstrap.Modal.getOrCreateInstance(\$modal[0]).hide(); }
        else { \$modal.hide().removeClass('in show'); document.body && document.body.classList.remove('modal-open'); }
      });
  }

  function setBtnCreateText(open){
    try { \$btnCreate.text(open ? 'Đóng form' : 'Thêm điều trị'); } catch(e){}
  }
  function openInline(url, titleText){
    \$box.show();
    \$loading.show();
    \$content.empty();
    if (titleText) \$title.text(titleText);
    $.get(url)
      .done(function(html){
        \$loading.hide();
        try {
          var nodes = $.parseHTML(html, document, true) || [];
          \$content.empty().append(nodes);

          // Thực thi <script> trong partial, rồi init form
          (function evalInlineScripts(\$ctx){
            try {
              \$ctx.find('script').each(function(){
                var \$script = \$(this);
                var src = \$script.attr('src');
                if (src) { $.getScript(src); }
                else { var code = \$script.html(); if (code) { $.globalEval(code); } }
              });
            } catch(e){ console.error(e); }
          }) (\$content);

          // Di chuyển #toothModal ra body nếu có (đảm bảo modal mở chính xác)
          var \$tooth = \$content.find('#toothModal');
          if (\$tooth.length) { \$tooth.appendTo(document.body); }

          initInlineDieuTriForm(\$content);
        } catch(e){
          console.error(e);
          \$content.html(html);
        }
        // Scroll tới box
        try {
          var top = \$box.offset().top - 12;
          $('html,body').animate({scrollTop: top}, 200);
        } catch(e){}
      })
      .fail(function(){
        \$loading.text('Không tải được form. Thử lại.');
      });
    setBtnCreateText(true);
  }
  function closeInline(){
    \$box.hide();
    \$content.off('.dtInline').empty();
    $(document).off('.dtInlineCombo');
    setBtnCreateText(false);
  }

  // Đóng form
  \$('#btn-hide-inline').on('click', function(){ closeInline(); });

  // Thêm điều trị (Create inline)
  \$btnCreate.on('click', function(e){
    e.preventDefault();
    if (\$box.is(':visible')) { closeInline(); return; }
    var url = \$(this).data('url'); // đã có ?inline=1
    openInline(url, ' ');
  });

  // Cập nhật điều trị (Update inline)
  $(document).on('click', '.dt-edit-inline', function(e){
    e.preventDefault();
    var url = \$(this).data('url');     // /my-dieu-tri/update?id=...&inline=1
    var id  = \$(this).data('id');
    openInline(url, 'Cập nhật điều trị #' + id);
  });
})();
JS;
$this->registerJs($jsInline);

// ===== JS: mở modal xem điều trị + xoá ảnh trong modal =====
$jsModalView = <<<JS
(function(){
  var \$modal = $('#dtViewModal');
  var currentUrl = null;

  // Mở modal xem điều trị (ảnh)
  $(document).on('click', '.dt-open-modal', function(e){
    e.preventDefault();
    currentUrl = $(this).data('url') || $(this).attr('href');
    if (!currentUrl) return;
    \$modal.find('.modal-body').html('<div class="text-center" style="padding:20px">Đang tải…</div>');
    \$modal.modal('show').find('.modal-body').load(currentUrl, function(){
      var title = \$modal.find('.dtv-title').text() || 'Chi tiết điều trị';
      \$modal.find('.modal-title').text(title);
    });
  });

  // Xoá ảnh trong modal rồi reload lại modal
  $(document).on('click', '#dtViewModal .dt-del-img, #dtViewModal .thumb .del', function(e){
    e.preventDefault();
    if (!confirm('Xoá ảnh này?')) return;
    var url = $(this).attr('href') || $(this).data('url');
    if (!url) return;

    $.post(url)
      .always(function(){
        if (currentUrl) {
          \$modal.find('.modal-body').load(currentUrl, function(){
            var title = \$modal.find('.dtv-title').text() || 'Chi tiết điều trị';
            \$modal.find('.modal-title').text(title);
          });
        }
      });
  });
})();
JS;
$this->registerJs($jsModalView);
?>
<?php
$jsAll = <<<JS
(function(){
  var \$modal = $('#khAllImgModal');
  $(document).on('click', '#btn-all-images', function(e){
    e.preventDefault();
    var url = $(this).data('url');
    if(!url) return;
    \$modal.find('.modal-body').html('<div class="text-center" style="padding:20px">Đang tải…</div>');
    \$modal.modal('show').find('.modal-body').load(url, function(){
      // tiêu đề set trong view all-images (kh-title-hidden) -> đã có script đặt hộ
    });
  });
})();
JS;
$this->registerJs($jsAll);
?>
