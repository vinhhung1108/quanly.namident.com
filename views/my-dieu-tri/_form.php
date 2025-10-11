<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\MaskedInput;
use yii\bootstrap\Modal; // nếu dùng bootstrap5: use yii\bootstrap5\Modal;
use app\models\BacSi;
use app\models\DichVu;
use app\models\MyDieuTriDv;

/* @var $model app\models\MyDieuTri|app\models\DieuTri */
/* @var $kh app\models\MyKhachHang|app\models\KhachHang|null */
/* @var $imagesForm app\models\MyDieuTriImage|app\models\DieuTriImage */

$this->registerCss(<<<CSS
.dt-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:visible;}
.dt-header{display:flex;justify-content:space-between;align-items:center;padding:14px 16px;border-bottom:1px solid #eef0f2;background:#f9fafb}
.dt-title{font-size:18px;font-weight:600;margin:0}
.dt-body{padding:16px}

/* Layout 2 cột: trái (form) | phải (ảnh) */
.dt-layout{display:grid;grid-template-columns: 1fr 220px;gap:18px;align-items:start}
@media (max-width: 1024px){ .dt-layout{ grid-template-columns:1fr; } }

/* Cột trái */
.dt-main .table-responsive{overflow:visible !important;}
@media (max-width:768px){
  .dt-main .table-responsive{overflow-x:auto !important; overflow-y:visible !important;}
}

/* Cột phải (aside ảnh) */
.dt-aside{position:sticky; top:16px;}
.aside-card{border:1px solid #e5e7eb;border-radius:12px;padding:12px}
.aside-title{font-weight:600;margin:0 0 10px;font-size:14px}
.aside-muted{font-size:12px;color:#6b7280;margin-bottom:8px}

/* Inputs gọn */
.dt-body .form-control{height:36px;padding:6px 10px}
.dt-body textarea.form-control{height:auto;min-height:84px;line-height:1.4}

/* Date-time */
.row-datetime{ display:grid; grid-template-columns: 220px 260px 1fr; gap:14px; align-items:end; }
@media (max-width:768px){ .row-datetime{ grid-template-columns:1fr; } }
.time-group{ display:flex; align-items:center; gap:8px; }
.time-group .form-control{ max-width:180px; height:36px; }

/* Bảng dịch vụ */
#dv-table{margin-top:8px}
#dv-table th,#dv-table td{vertical-align:middle}
#dv-table tfoot th,#dv-table tfoot td{background:#fafafa}
.dv-thanh-tien{font-weight:600}

/* Dịch vụ combo */
.dv-combo{position:relative;display:flex;gap:6px;align-items:center; z-index:1;}
.dv-combo .dv-ten{flex:1}
.dv-combo .dv-lookup{flex:0 0 auto}
.dv-menu{position:absolute;z-index:9999;top:100%;left:0;right:0;background:#fff;border:1px solid #e5e7eb;border-radius:10px;margin-top:6px;padding:8px;box-shadow:0 8px 24px rgba(0,0,0,.08);display:none}
.dv-menu.open{display:block}
.dv-menu.dropup{ top:auto; bottom:100%; margin-top:0; margin-bottom:6px; }
.dv-menu .dv-search{height:32px;margin-bottom:6px}
.dv-menu .dv-list{max-height:260px;overflow:auto}
.dv-opt{display:flex;justify-content:space-between;gap:10px;padding:6px 8px;border-radius:8px;cursor:pointer}
.dv-opt:hover{background:#f3f4f6}
.dv-opt .dv-name{font-size:14px}
.dv-opt .dv-price{font-size:12px;color:#6b7280;white-space:nowrap}

/* Chọn răng */
.dv-rang-box{display:flex;align-items:center;gap:6px;flex-wrap:wrap}
.dv-rang-text{font-size:12px;color:#374151;background:#f3f4f6;border-radius:999px;padding:4px 8px}
.dv-rang-holder{display:none}
.btn-tooth{ display:inline-flex;align-items:center;justify-content:center; width:32px;height:28px;padding:0;border-radius:6px; }
.btn-tooth .tooth-img{ width:18px;height:18px; object-fit:contain; display:block; }

/* Preview ảnh dọc */
.preview{display:flex;flex-direction:column;gap:8px;max-height:50vh;overflow:auto;margin-top:8px}
.preview .thumb{width:100%;height:90px;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#fafafa}
.preview .thumb img{max-width:100%;max-height:100%;object-fit:cover}

/* Progress nhỏ gọn */
.progress-wrap{display:none;margin-top:10px}
.progress{height:6px;background:#eef2f7;border-radius:999px;overflow:hidden}
.progress .bar{height:100%;width:0;background:#3b82f6;transition:width .2s}

/* Sticky actions (nằm dưới bảng) */
.actions-sticky{position:sticky;bottom:0;background:#fff;border-top:1px solid #eef0f2;padding:12px 0;display:flex;gap:8px}

/* ===== Modal chọn răng: 4 cụm (Q1–Q4) ===== */
.tooth-wrap{display:grid;grid-template-columns:repeat(2,minmax(260px,1fr));gap:16px}
.tooth-card{border:1px solid #e5e7eb;border-radius:10px;padding:10px}
.tooth-card h5{margin:0 0 8px;font-size:14px;font-weight:600}
.tooth-grid{display:grid;grid-template-columns:repeat(8,1fr);gap:6px}
@media (max-width:768px){
  .tooth-wrap{grid-template-columns:1fr}
  .tooth-grid{grid-template-columns:repeat(4,1fr)}
}
.tooth-item{display:flex;gap:6px;align-items:center;font-size:12px;padding:4px;background:#f9fafb;border:1px solid #eef0f2;border-radius:6px}
.badge-hint{font-size:12px;color:#6b7280}
CSS);

/** Danh sách bác sĩ */
$dsBacSi = ArrayHelper::map(
    BacSi::find()->select(['id','ho_ten'])->orderBy('ho_ten')->asArray()->all(),
    'id', 'ho_ten'
);

/** Danh mục dịch vụ (active) */
$dvCatalog = DichVu::find()->select(['id','ten','don_gia'])
    ->where(['active'=>1])->orderBy('ten')->asArray()->all();

/** FDI */
$TOOTH_Q1 = ['18','17','16','15','14','13','12','11']; // Hàm trên phải
$TOOTH_Q2 = ['21','22','23','24','25','26','27','28']; // Hàm trên trái
$TOOTH_Q3 = ['38','37','36','35','34','33','32','31']; // Hàm dưới trái
$TOOTH_Q4 = ['41','42','43','44','45','46','47','48']; // Hàm dưới phải

/** Tạm thu hiển thị ban đầu */
$tamThuDisplay = $model->tam_thu !== null ? number_format((int)$model->tam_thu, 0, '.', ',') : null;
$hasDvTamThu = MyDieuTriDv::hasTamThuColumn();
?>

<div class="dt-card">
  <?php $form = ActiveForm::begin([
    'id' => 'dt-form',
    'options' => ['enctype' => 'multipart/form-data', 'autocomplete'=>'off'],
    'fieldConfig' => [
      'template' => "{label}\n{input}\n{error}",
      'labelOptions' => ['style' => 'font-weight:500;margin-bottom:6px'],
      'errorOptions' => ['class' => 'help-block text-danger'],
    ],
  ]); ?>

  <div class="dt-body">
    <div class="dt-layout">
      <!-- ===== CỘT TRÁI: FORM CHÍNH ===== -->
      <div class="dt-main">
        <!-- Ngày · Giờ -->
        <div class="row-datetime" style="display:flex;align-items:flex-end;gap:14px;flex-wrap:nowrap">
          <div style="display:flex;align-items:center;gap:8px;width:120px">
            <?= $form->field($model, 'ngay_dieu_tri', [
                  'template' => "{input}\n{error}", 'options'  => ['tag' => false],
              ])->input('date', [
                  // NEW: tạo mới -> để trống; JS sẽ tự điền ngày local
                  'value' => $model->isNewRecord ? '' : $model->ngay_dieu_tri,
                  'data-autodate' => $model->isNewRecord ? '1' : '0',
                  'style' => 'width:100%',
              ])->label(false) ?>
          </div>
          <div class="time-group" style="display:flex;align-items:center;gap:8px;">
            <?= $form->field($model, 'gio', [
                  'template' => "{input}\n{error}", 'options'  => ['tag' => false],
              ])->input('time', [
                  // NEW: tạo mới -> để trống; JS sẽ tự điền giờ local
                  'value' => $model->isNewRecord ? '' : $model->gio,
                  'data-autonow' => $model->isNewRecord ? '1' : '0',
                  'style'=>'width:100%'
              ])->label(false) ?>
          </div>
          <?= $form->field($model, 'hinh_thuc_thanh_toan',[
            'options'=>['class'=>'time-group'],
          ])->dropDownList([
                'TM'   => 'Tiền mặt',
                'CK'   => 'Chuyển khoản',
                'QR'   => 'QR/Thẻ',
                'KHAC' => 'Khác',
            ], ['prompt' => '- Hình thức TT -'])->label(false) ?>
        </div>

        <!-- Bảng dịch vụ -->
        <div class="table-responsive">
          <table class="table table-bordered" id="dv-table">
            <thead>
              <tr>
                <th></th>
                <th style="min-width:280px">Dịch vụ (gõ để nhập / bấm ▾ để chọn)</th>
                <th style="width:150px">Bác sĩ</th>
                <th style="width:140px">Răng số</th>
                <th style="width:80px">SL</th>
                <th style="min-width:210px">Đơn giá</th>
                <th style="width:140px;text-align:right">Thành tiền</th>
                <th style="width:160px;text-align:right">Tạm thu <?= Html::button('= Phí', ['class' => 'btn btn-default btn-sm', 'id' => 'btnEqual', 'style'=>'margin-top:6px']) ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($model->isNewRecord ? [] : $model->dvItems as $i => $it): ?>
                <?php
                  $sel = array_filter(explode(',', (string)$it->rang_so));
                  $tenHienThi = $it->ten_dv ?: ($it->dichVu->ten ?? '');
                ?>
                <tr data-idx="<?= $i ?>">
                  <td><button type="button" class="btn btn-link text-danger dv-remove" title="Xóa">&times;</button></td>
                  <td>
                    <div class="dv-combo">
                      <input type="text"
                             name="dv_ten[]"
                             class="form-control dv-ten"
                             placeholder="Nhập tên dịch vụ hoặc chọn từ danh mục"
                             value="<?= Html::encode($tenHienThi) ?>">
                      <input type="hidden" name="dv_id[]" class="dv-id-hidden" value="<?= (int)($it->dich_vu_id ?: 0) ?>">
                      <button type="button" class="btn btn-default btn-sm dv-lookup" title="Chọn từ danh mục">▾</button>

                      <div class="dv-menu">
                        <input type="text" class="form-control dv-search" placeholder="Tìm dịch vụ...">
                        <div class="dv-list">
                          <?php foreach ($dvCatalog as $dv): ?>
                            <div class="dv-opt"
                                 data-id="<?= $dv['id'] ?>"
                                 data-ten="<?= Html::encode($dv['ten']) ?>"
                                 data-price="<?= (int)$dv['don_gia'] ?>">
                              <span class="dv-name"><?= Html::encode($dv['ten']) ?></span>
                              <span class="dv-price"><?= number_format((int)$dv['don_gia'],0,'.',',') ?> ₫</span>
                            </div>
                          <?php endforeach; ?>
                        </div>
                      </div>
                    </div>
                  </td>

                  <td>
                    <select name="dv_bs[]" class="form-control dv-bs">
                      <option value="">— Chọn bác sĩ —</option>
                      <?php foreach ($dsBacSi as $bsId => $bsName): ?>
                        <option value="<?= $bsId ?>" <?= (isset($it->bs_id) && (int)$it->bs_id === (int)$bsId) ? 'selected' : '' ?>>
                          <?= Html::encode($bsName) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </td>

                  <td>
                    <div class="dv-rang-box">
                      <button type="button"
                              class="btn btn-default btn-sm dv-rang-open btn-tooth"
                              data-index="<?= $i ?>"
                              aria-label="Chọn răng" title="Chọn răng">
                        <img src="<?= Yii::getAlias('@web') ?>/images/tooth.png" alt="" class="tooth-img">
                      </button>
                      <span class="dv-rang-text"><?= $sel ? Html::encode(implode(',', $sel)) : '—' ?></span>
                      <div class="dv-rang-holder" data-index="<?= $i ?>">
                        <?php foreach ($sel as $t): ?>
                          <input type="hidden" name="dv_rang[<?= $i ?>][]" value="<?= Html::encode($t) ?>">
                        <?php endforeach; ?>
                      </div>
                    </div>
                  </td>

                  <td><input type="number" name="dv_qty[]" class="form-control dv-qty" min="1" value="<?= (int)$it->so_luong ?>"></td>
                  <td><input type="text" name="dv_don_gia[]" class="form-control money dv-don-gia" value="<?= number_format((int)$it->don_gia,0,'.',',') ?>"></td>
                  <td class="dv-thanh-tien" style="text-align:right"><?= number_format((int)$it->thanh_tien,0,'.',',') ?></td>
                  <?php $itemTamThu = method_exists($it, 'hasAttribute') && $it->hasAttribute('tam_thu') ? (int)$it->getAttribute('tam_thu') : 0; ?>
                  <td><input type="text" name="dv_tam_thu[]" class="form-control money dv-tam-thu" value="<?= number_format($itemTamThu,0,'.',',') ?>" style="text-align:right"></td>
                </tr>
              <?php endforeach; ?>
            </tbody>

            <tfoot>
              <tr>
                <td colspan="2">
                  <button type="button" class="btn btn-default" id="dv-add">+ Thêm dịch vụ</button>
                </td>
                <th style="text-align:right" colspan="4">Tổng phí dịch vụ</th>
                <th style="text-align:right" id="dv-total">0</th>
                <?php if ($hasDvTamThu): ?>
                  <!-- <th style="text-align:right">Tổng tạm thu</th> -->
                  <th style="text-align:right" id="dv-tamthu-total">0</th>
                <?php else: ?>
                  <th style="text-align:right">&nbsp;</th>
                <?php endif; ?>
              </tr>
              <tr>
                <td colspan="8">
                  <div class="actions-sticky">
                    <?= Html::submitButton('Lưu', ['class'=>'btn btn-success', 'id'=>'btnSubmit']) ?>
                    <?php if ($kh): ?>
                      <?= Html::a('Về khách hàng', ['/my-khach-hang/view','id'=>$kh->id], ['class'=>'btn btn-default']) ?>
                    <?php endif; ?>
                    <?= Html::a('Hủy', ['/my-khach-hang/view', 'id'=>$kh->id], ['class'=>'btn btn-link']) ?>
                  </div>
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- Template hàng mới (ẩn) -->
        <table style="display:none"><tbody id="dv-row-tpl">
          <tr data-idx="__i__">
            <td><button type="button" class="btn btn-link text-danger dv-remove" title="Xóa">&times;</button></td>
            <td>
              <div class="dv-combo">
                <input type="text" name="dv_ten[]" class="form-control dv-ten" placeholder="Nhập tên dịch vụ hoặc chọn từ danh mục" value="">
                <input type="hidden" name="dv_id[]" class="dv-id-hidden" value="">
                <button type="button" class="btn btn-default btn-sm dv-lookup" title="Chọn từ danh mục">▾</button>

                <div class="dv-menu">
                  <input type="text" class="form-control dv-search" placeholder="Tìm dịch vụ...">
                  <div class="dv-list">
                    <?php foreach ($dvCatalog as $dv): ?>
                      <div class="dv-opt"
                           data-id="<?= $dv['id'] ?>"
                           data-ten="<?= Html::encode($dv['ten']) ?>"
                           data-price="<?= (int)$dv['don_gia'] ?>">
                        <span class="dv-name"><?= Html::encode($dv['ten']) ?></span>
                        <span class="dv-price"><?= number_format((int)$dv['don_gia'],0,'.',',') ?> ₫</span>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            </td>
            <td>
              <select name="dv_bs[]" class="form-control dv-bs">
                <option value="">— Chọn bác sĩ —</option>
                <?php foreach ($dsBacSi as $bsId => $bsName): ?>
                  <option value="<?= $bsId ?>"><?= Html::encode($bsName) ?></option>
                <?php endforeach; ?>
              </select>
            </td>
            <td>
              <div class="dv-rang-box">
                <button type="button" class="btn btn-default btn-sm dv-rang-open btn-tooth" data-index="__i__" aria-label="Chọn răng" title="Chọn răng">
                  <img src="<?= Yii::getAlias('@web') ?>/images/tooth.png" alt="" class="tooth-img">
                </button>
                <span class="dv-rang-text">—</span>
                <div class="dv-rang-holder" data-index="__i__"></div>
              </div>
            </td>
            <td><input type="number" name="dv_qty[]" class="form-control dv-qty" min="1" value="1"></td>
            <td><input type="text" name="dv_don_gia[]" class="form-control money dv-don-gia" value="0"></td>
            <td class="dv-thanh-tien" style="text-align:right">0</td>
            <?php if ($hasDvTamThu): ?>
              <td><input type="text" name="dv_tam_thu[]" class="form-control money dv-tam-thu" value="" style="text-align:right"></td>
            <?php endif; ?>
          </tr>
        </tbody></table>

        <!-- Tổng phí -> field phi (ẩn) -->
        <?= Html::activeHiddenInput($model, 'phi', ['id'=>'phi-total']) ?>
      </div>

      <!-- ===== CỘT PHẢI: ẢNH ===== -->
      <aside class="dt-aside">
        <div class="aside-card">
          <div class="aside-title">Ảnh</div>
          <div class="aside-muted"></div>
          <?= $form->field($imagesForm, 'files[]', [
                'template' => "{input}\n{error}",
                'options' => ['tag'=>false]
              ])->fileInput([
                'multiple'=>true, 'accept'=>'image/*', 'id'=>'fileInput', 'class'=>'form-control'
              ]) ?>
          <div id="preview" class="preview"></div>

          <!-- Progress upload -->
          <div class="progress-wrap" id="uploadBarWrap">
            <div class="progress"><div class="bar" id="uploadBar"></div></div>
            <div class="aside-muted" style="margin-top:6px">
              <span id="uploadText">Đang tải lên...</span>
              <strong id="uploadPct" style="float:right">0%</strong>
            </div>
          </div>
        </div>
      </aside>
    </div>
  </div>

  <?php ActiveForm::end(); ?>
</div>

<?php
// Modal chọn răng: 4 cụm Q1–Q4
Modal::begin([
  'id' => 'toothModal',
  'header' => '<h4 class="modal-title">Chọn răng</h4>',
  'size' => Modal::SIZE_LARGE,
]);
?>
<div>
  <div class="tooth-actions" style="margin-bottom:8px">
    <button type="button" class="btn btn-default btn-sm" id="tooth-select-all">Chọn tất cả</button>
    <button type="button" class="btn btn-default btn-sm" id="tooth-clear-all">Bỏ chọn</button>
    <span class="badge-hint">Mẹo: Giữ Ctrl/Cmd để tick nhanh.</span>
  </div>

  <div class="tooth-wrap">
    <div class="tooth-card">
      <div class="tooth-grid" id="Q1">
        <?php foreach ($TOOTH_Q1 as $t): ?>
          <label class="tooth-item"><input type="checkbox" class="tooth" value="<?= $t ?>"> <?= $t ?></label>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="tooth-card">
      <div class="tooth-grid" id="Q2">
        <?php foreach ($TOOTH_Q2 as $t): ?>
          <label class="tooth-item"><input type="checkbox" class="tooth" value="<?= $t ?>"> <?= $t ?></label>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="tooth-card">
      <div class="tooth-grid" id="Q4">
        <?php foreach ($TOOTH_Q4 as $t): ?>
          <label class="tooth-item"><input type="checkbox" class="tooth" value="<?= $t ?>"> <?= $t ?></label>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="tooth-card">
      <div class="tooth-grid" id="Q3">
        <?php foreach ($TOOTH_Q3 as $t): ?>
          <label class="tooth-item"><input type="checkbox" class="tooth" value="<?= $t ?>"> <?= $t ?></label>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div style="display:flex;gap:8px;margin-top:12px">
    <button type="button" class="btn btn-primary" id="tooth-apply">Xong</button>
    <button type="button" class="btn btn-default" data-dismiss="modal" data-bs-dismiss="modal">Đóng</button>
  </div>
</div>
<?php Modal::end(); ?>

<?php
$js = <<<JS
(function(){
  function pad(n){ return String(n).padStart(2,'0'); }
  function parseMoney(s){ s=(s||'').toString().replace(/[^\\d\\-]/g,''); return s?parseInt(s,10):0; }
  function fmtMoney(n){ n=parseInt(n||0,10); return n.toLocaleString('en-US'); }
  var hasPerServiceTamThu = <?= $hasDvTamThu ? 'true' : 'false' ?>;
  var toothModalEl = document.getElementById('toothModal');
  var toothModalInstance = null;
  var toothModalBackdrop = null;

  function createToothBackdrop(){
    if (toothModalBackdrop || !document.body) { return toothModalBackdrop; }
    toothModalBackdrop = document.createElement('div');
    toothModalBackdrop.className = 'modal-backdrop fade show';
    document.body.appendChild(toothModalBackdrop);
    return toothModalBackdrop;
  }

  function removeToothBackdrop(){
    if (toothModalBackdrop && toothModalBackdrop.parentNode) {
      toothModalBackdrop.parentNode.removeChild(toothModalBackdrop);
    }
    toothModalBackdrop = null;
  }

  function showToothModal(){
    toothModalEl = toothModalEl || document.getElementById('toothModal');
    if (!toothModalEl) { return; }
    if (window.jQuery && jQuery.fn && typeof jQuery.fn.modal === 'function') {
      jQuery(toothModalEl).modal('show');
      return;
    }
    if (window.bootstrap && window.bootstrap.Modal) {
      toothModalInstance = window.bootstrap.Modal.getOrCreateInstance(toothModalEl);
      toothModalInstance.show();
      return;
    }
    toothModalEl.style.display = 'block';
    toothModalEl.removeAttribute('aria-hidden');
    toothModalEl.setAttribute('aria-modal', 'true');
    toothModalEl.classList.add('in', 'show');
    if (document.body) {
      document.body.classList.add('modal-open');
    }
    createToothBackdrop();
  }

  function hideToothModal(){
    toothModalEl = toothModalEl || document.getElementById('toothModal');
    if (!toothModalEl) { return; }
    if (window.jQuery && jQuery.fn && typeof jQuery.fn.modal === 'function') {
      jQuery(toothModalEl).modal('hide');
      return;
    }
    if (window.bootstrap && window.bootstrap.Modal) {
      (toothModalInstance || window.bootstrap.Modal.getOrCreateInstance(toothModalEl)).hide();
      return;
    }
    toothModalEl.style.display = 'none';
    toothModalEl.setAttribute('aria-hidden', 'true');
    toothModalEl.removeAttribute('aria-modal');
    toothModalEl.classList.remove('in', 'show');
    if (document.body) {
      document.body.classList.remove('modal-open');
    }
    removeToothBackdrop();
  }

  window.__NamidentToothModal = window.__NamidentToothModal || {};
  window.__NamidentToothModal.show = showToothModal;
  window.__NamidentToothModal.hide = hideToothModal;
  window.__NamidentToothModal.getEl = function(){
    toothModalEl = toothModalEl || document.getElementById('toothModal');
    return toothModalEl;
  };

  // ===== Auto-fill ngày/giờ local cho bản ghi mới =====
  (function(){
    var timeEl = document.querySelector('[data-autonow="1"]');
    if (timeEl && !timeEl.value){
      var d = new Date();
      timeEl.value = pad(d.getHours()) + ':' + pad(d.getMinutes());
      timeEl.dispatchEvent(new Event('change'));
    }
    var dateEl = document.querySelector('[data-autodate="1"]');
    if (dateEl && !dateEl.value){
      var d = new Date();
      dateEl.value = d.getFullYear() + '-' + pad(d.getMonth()+1) + '-' + pad(d.getDate());
      dateEl.dispatchEvent(new Event('change'));
    }
  })();

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

    // Fallback: tự chèn dấu phẩy khi Inputmask chưa được load
    \$inputs.each(function(){
      var el = this;
      if (el._moneyFallbackHandler) {
        el.removeEventListener('input', el._moneyFallbackHandler);
      }
      var handler = function(){
        var raw = el.value || '';
        var negative = raw.trim().charAt(0) === '-';
        var digits = raw.replace(/\D/g, '');
        if (!digits) {
          el.value = negative ? '-' : '';
          return;
        }
        var withCommas = digits.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        el.value = negative ? '-' + withCommas : withCommas;
      };
      el._moneyFallbackHandler = handler;
      el.addEventListener('input', handler);
      handler();
    });
  }

  function recalcRow(\$tr){
    var price = parseMoney(\$tr.find('.dv-don-gia').val());
    var qty   = parseInt(\$tr.find('.dv-qty').val()||1,10);
    if (isNaN(qty) || qty < 1) qty = 1;
    var amt   = price * qty;
    \$tr.find('.dv-thanh-tien').text(fmtMoney(amt));
    return amt;
  }
  function recalcTotal(){
    var sumPhi = 0;
    var sumTamThu = 0;
    \$('#dv-table tbody tr').each(function(){
      var \$tr = \$(this);
      sumPhi += recalcRow(\$tr);
      if (hasPerServiceTamThu) {
        sumTamThu += parseMoney(\$tr.find('.dv-tam-thu').val());
      }
    });
    \$('#dv-total').text(fmtMoney(sumPhi));
    if (hasPerServiceTamThu) {
      \$('#dv-tamthu-total').text(fmtMoney(sumTamThu));
    }
    \$('#phi-total').val(sumPhi);
    var \$tam = \$('#tamthu-mask');
    var currentTamThu = sumTamThu;
    if (\$tam.length) {
      if (hasPerServiceTamThu) {
        if (\$tam.inputmask) { \$tam.inputmask('setvalue', sumTamThu); }
        else { \$tam.val(sumTamThu ? fmtMoney(sumTamThu) : ''); }
      } else {
        currentTamThu = parseMoney(\$tam.val());
      }
    }
    return { phi: sumPhi, tamThu: currentTamThu };
  }

  // ===== COMBO DỊCH VỤ =====
  \$(document).on('click', '.dv-lookup', function(e){
    e.stopPropagation();
    var \$combo = \$(this).closest('.dv-combo');
    var \$menu  = \$combo.find('.dv-menu');

    \$('.dv-menu').not(\$menu).removeClass('open dropup');
    \$menu.toggleClass('open');

    if (\$menu.hasClass('open')) {
      var rect = \$combo[0].getBoundingClientRect();
      var menuH = 300; // ước lượng
      var spaceBelow = window.innerHeight - rect.bottom;
      var spaceAbove = rect.top;
      if (spaceBelow < menuH && spaceAbove > spaceBelow){ \$menu.addClass('dropup'); } else { \$menu.removeClass('dropup'); }

      var \$s = \$menu.find('.dv-search'); \$s.val(''); \$s.trigger('input'); \$s.focus();
    }
  });

  \$(document).on('input', '.dv-menu .dv-search', function(){
    var q = \$(this).val().toLowerCase();
    \$(this).closest('.dv-menu').find('.dv-opt').each(function(){
      var name = (\$(this).data('ten') || '').toString().toLowerCase();
      \$(this).toggle(name.indexOf(q) >= 0);
    });
  });

  \$(document).on('click', '.dv-menu .dv-opt', function(){
    var id = \$(this).data('id');
    var ten = \$(this).data('ten');
    var price = parseInt(\$(this).data('price')||0,10);

    var \$combo = \$(this).closest('.dv-combo');
    var \$tr = \$combo.closest('tr');

    \$combo.find('.dv-ten').val(ten);
    \$combo.find('.dv-id-hidden').val(id);

    var \$price = \$tr.find('.dv-don-gia');
    if (\$price.inputmask) { \$price.inputmask('setvalue', price); } else { \$price.val(fmtMoney(price)); }

    \$combo.find('.dv-menu').removeClass('open');
    recalcRow(\$tr); recalcTotal();
  });

  // Gõ tay => nội dung khác
  \$(document).on('input', '.dv-ten', function(){
    \$(this).closest('.dv-combo').find('.dv-id-hidden').val('');
  });

  // Click ngoài => đóng menu
  \$(document).on('click', function(e){
    if (\$(e.target).closest('.dv-combo').length === 0) {
      \$('.dv-menu').removeClass('open dropup');
    }
  });

  // Thêm/xoá dòng
  $(document).on('click', '#dv-add', function(){
    var idx = \$('#dv-table tbody tr').length;
    var html = \$('#dv-row-tpl').html().replaceAll('__i__', idx);
    var \$row = \$(html);
    \$('#dv-table tbody').append(\$row);
    var maskSelector = hasPerServiceTamThu ? '.dv-don-gia, .dv-tam-thu' : '.dv-don-gia';
    applyMoneyMask(\$row.find(maskSelector));
  });
  \$(document).on('click', '#dv-table .dv-remove', function(){
    \$(this).closest('tr').remove();
    recalcTotal();
  });

  // Thay đổi giá/SL
  \$(document).on('input change', '#dv-table .dv-don-gia, #dv-table .dv-qty', function(){
    var \$tr = \$(this).closest('tr');
    recalcRow(\$tr); recalcTotal();
  });
  if (hasPerServiceTamThu) {
    \$(document).on('input change', '#dv-table .dv-tam-thu', function(){
      recalcTotal();
    });
  }

  // ===== CHỌN RĂNG =====
  var currentToothIdx = null;
  \$(document).on('click', '.dv-rang-open', function(){
    currentToothIdx = \$(this).data('index');
    \$('#toothModal input.tooth').prop('checked', false);
    var \$holder = \$('.dv-rang-holder[data-index=\"'+currentToothIdx+'\"]').first();
    var selected = [];
    \$holder.find('input[type=\"hidden\"]').each(function(){ selected.push(\$(this).val()); });
    selected.forEach(function(val){ \$('#toothModal input.tooth[value=\"'+val+'\"]').prop('checked', true); });
    showToothModal();
  });
  \$(document).off('click.toothModalSelectAll', '#tooth-select-all')
    .on('click.toothModalSelectAll', '#tooth-select-all', function(){
      \$('#toothModal input.tooth').prop('checked', true);
    });
  \$(document).off('click.toothModalClearAll', '#tooth-clear-all')
    .on('click.toothModalClearAll', '#tooth-clear-all', function(){
      \$('#toothModal input.tooth').prop('checked', false);
    });
  \$(document).off('click.toothModalApply', '#tooth-apply')
    .on('click.toothModalApply', '#tooth-apply', function(){
      if (currentToothIdx === null) { hideToothModal(); return; }
      var vals = [];
      \$('#toothModal input.tooth:checked').each(function(){ vals.push(\$(this).val()); });
      var \$holder = \$('.dv-rang-holder[data-index=\"'+currentToothIdx+'\"]').first();
      var \$text   = \$holder.closest('.dv-rang-box').find('.dv-rang-text');
      \$holder.empty();
      vals.forEach(function(v){ \$holder.append('<input type=\"hidden\" name=\"dv_rang['+currentToothIdx+'][]\" value=\"'+v+'\">'); });
      \$text.text(vals.length ? vals.join(',') : '—');
      hideToothModal();
    });
  \$(document).off('click.toothModalClose', '#toothModal [data-dismiss=\"modal\"], #toothModal [data-bs-dismiss=\"modal\"]')
    .on('click.toothModalClose', '#toothModal [data-dismiss=\"modal\"], #toothModal [data-bs-dismiss=\"modal\"]', function(e){
      e.preventDefault();
      hideToothModal();
    });

  // Tạm thu = Tổng phí
  var btnEqual = document.getElementById('btnEqual');
  if (btnEqual) {
    btnEqual.addEventListener('click', function(){
      if (hasPerServiceTamThu) {
        \$('#dv-table tbody tr').each(function(){
          var \$tr = \$(this);
          var amt = recalcRow(\$tr);
          var \$tam = \$tr.find('.dv-tam-thu');
          if (\$tam.length) {
            if (\$tam.inputmask) { \$tam.inputmask('setvalue', amt); }
            else { \$tam.val(fmtMoney(amt)); }
          }
        });
        recalcTotal();
      } else {
        var totals = recalcTotal();
        var \$tam = \$('#tamthu-mask');
        if (\$tam.length) {
          if (\$tam.inputmask) { \$tam.inputmask('setvalue', totals.phi); }
          else { \$tam.val(fmtMoney(totals.phi)); }
          \$tam.trigger('input').trigger('change');
        }
      }
    });
  }

  // Khởi tạo
  var initMaskSelector = hasPerServiceTamThu ? '#dv-table .dv-don-gia, #dv-table .dv-tam-thu' : '#dv-table .dv-don-gia';
  applyMoneyMask(\$(initMaskSelector));
  recalcTotal();

  // Preview ảnh (dọc bên phải)
  var fileInput = document.getElementById('fileInput');
  var preview = document.getElementById('preview');
  if (fileInput && preview) {
    fileInput.addEventListener('change', function(e){
      preview.innerHTML = '';
      var files = e.target.files || [];
      Array.from(files).slice(0, 40).forEach(function(f){
        var wrap = document.createElement('div');
        wrap.className = 'thumb';
        if (f.type && f.type.indexOf('image') === 0) {
          var reader = new FileReader();
          reader.onload = function(ev){
            var img = document.createElement('img'); img.src = ev.target.result; wrap.appendChild(img);
          };
          reader.readAsDataURL(f);
        } else {
          wrap.innerHTML = '<span style="font-size:12px;color:#666">FILE</span>';
        }
        preview.appendChild(wrap);
      });
    });
  }

  // Upload có progress (nếu có file)
  var barWrap = document.getElementById('uploadBarWrap');
  var bar = document.getElementById('uploadBar');
  var pct = document.getElementById('uploadPct');
  var txt = document.getElementById('uploadText');
  var btnSubmit = document.getElementById('btnSubmit');
  var \$form = jQuery('#dt-form');

  \$form.off('beforeSubmit.dtProgress');
  \$form.on('beforeSubmit.dtProgress', function(e){
    recalcTotal(); // đẩy tổng vào field phi trước submit
    if (\$form.data('uploading')) { return false; }
    var hasFiles = fileInput && fileInput.files && fileInput.files.length > 0;
    if (!hasFiles) { return true; }

    e.preventDefault();
    \$form.data('uploading', true);
    if (btnSubmit) { btnSubmit.disabled = true; btnSubmit.innerText = 'Đang lưu...'; }
    if (barWrap) { barWrap.style.display = 'block'; }
    if (bar) { bar.style.width = '0%'; }
    if (pct) { pct.innerText = '0%'; }
    if (txt) { txt.innerText = 'Đang tải lên...'; }

    var xhr = new XMLHttpRequest();
    xhr.open(\$form.attr('method') || 'POST', \$form.attr('action'), true);

    xhr.upload.addEventListener('progress', function(ev){
      if (ev.lengthComputable && bar && pct) {
        var percent = Math.round((ev.loaded / ev.total) * 100);
        bar.style.width = percent + '%'; pct.innerText = percent + '%';
      }
    });

    xhr.onreadystatechange = function(){
      if (xhr.readyState === 4) {
        if (xhr.status >= 200 && xhr.status < 400) {
          if (txt) txt.innerText = 'Hoàn tất xử lý, đang chuyển trang...';
          var to = xhr.responseURL || window.location.href; window.location.href = to;
        } else {
          if (txt) txt.innerText = 'Có lỗi xảy ra khi lưu. Vui lòng thử lại.';
          \$form.data('uploading', false);
          if (btnSubmit) { btnSubmit.disabled = false; btnSubmit.innerText = 'Lưu'; }
        }
      }
    };

    var fd = new FormData(\$form.get(0));
    xhr.send(fd);
    return false;
  });
})();
JS;
$this->registerJs($js);
?>
