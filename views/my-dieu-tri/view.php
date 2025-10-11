<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

$isModal = (int)Yii::$app->request->get('modal', 0) === 1;

/* @var $this yii\web\View */
/* @var $model app\models\MyDieuTri */

$kh = $model->khachHang ?? null;
$this->title = 'Điều trị #' . $model->id;
if (!$isModal) {
    $this->params['breadcrumbs'][] = ['label' => 'Khách hàng', 'url' => ['/my-khach-hang/index']];
    if ($kh) {
        $this->params['breadcrumbs'][] = ['label' => 'KH: ' . $kh->ho_ten, 'url' => ['/my-khach-hang/view', 'id' => $kh->id]];
    }
    $this->params['breadcrumbs'][] = $this->title;
}

$fmtVnd = fn($n) => number_format((int)$n, 0, '.', ',') . ' ₫';

/** ===== Chuẩn hoá danh sách dịch vụ từ quan hệ dvItems (tránh N+1) ===== */
$dvItems = [];
$hasTamThuColumn = false;
$rows = method_exists($model, 'getDvItems') ? ($model->dvItems ?? []) : [];

// Thu thập id bác sĩ để load 1 lần
$bsIds = [];
foreach ($rows as $it) {
    if (!empty($it->bs_id)) $bsIds[(int)$it->bs_id] = true;
}
$bsMap = [];
if ($bsIds) {
    $ids = array_keys($bsIds);
    foreach (\app\models\BacSi::find()->select(['id','ho_ten'])->where(['id'=>$ids])->asArray()->all() as $r) {
        $bsMap[(int)$r['id']] = $r['ho_ten'];
    }
}

foreach ($rows as $it) {
    // $it: app\models\MyDieuTriDv
    $ten = $it->ten_dv ?: ($it->dichVu->ten ?? '—');
    $bsName = '—';
    if (!empty($it->bs_id) && isset($bsMap[(int)$it->bs_id])) {
        $bsName = $bsMap[(int)$it->bs_id];
    }
    $tamThuVal = null;
    if (method_exists($it, 'hasAttribute') && $it->hasAttribute('tam_thu')) {
        $tamThuVal = (int)$it->getAttribute('tam_thu');
        $hasTamThuColumn = true;
    }
    $dvItems[] = [
        'ten'         => $ten ?: '—',
        'bs_id'       => $it->bs_id ?? null,
        'bs_name'     => $bsName,
        'is_custom'   => empty($it->dich_vu_id),
        'don_gia'     => (int)$it->don_gia,
        'so_luong'    => (int)$it->so_luong,
        'rang_so'     => $it->rang_so ?: '',
        'thanh_tien'  => (int)$it->thanh_tien,
        'tam_thu'     => $tamThuVal,
    ];
}
$sumDv = array_sum(array_map(fn($r)=>$r['thanh_tien'], $dvItems));
$sumTamThu = $hasTamThuColumn ? array_sum(array_map(fn($r)=> (int)($r['tam_thu'] ?? 0), $dvItems)) : 0;

// Bác sĩ tổng hợp từ các dòng (để hiển thị gọn ở header)
$bsUnique = array_values(array_unique(array_filter(array_map(fn($r)=>$r['bs_name'], $dvItems), fn($v)=>$v && $v !== '—')));

/** Ảnh đính kèm */
$imgs = $model->images ?? [];
$imgUrls = [];
foreach ($imgs as $i) {
    $imgUrls[] = Yii::$app->request->baseUrl . $i->file_path;
}

/** ===== CSS ===== */
if ($isModal) {
    // CSS gọn trong modal: chỉ cần gallery + tí bố cục
    $this->registerCss(<<<CSS
.dtv-title{display:none}
.dt-modal .subtle{color:#6b7280;font-size:13px}
.dt-modal .gallery{
  display:grid;grid-template-columns:repeat(auto-fill, 180px);gap:10px;justify-content:start;
}
.dt-modal .thumb{
  position:relative;width:180px;height:180px;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;background:#fafafa;display:flex;align-items:center;justify-content:center;
}
.dt-modal .thumb img{width:100%;height:100%;object-fit:cover}
.dt-modal .thumb .del{position:absolute;top:6px;right:6px;background:#ef4444;border:none;color:#fff;border-radius:8px;padding:4px 6px;font-size:12px;opacity:.9;text-decoration:none}
.dt-modal .thumb .del:hover{opacity:1}
CSS);
} else {
    // CSS đầy đủ như bản cũ
    $this->registerCss(<<<CSS
/* FULL WIDTH chỉ trong trang này */
.dt-view .wrap > .container,
.dt-view .container{max-width:100%!important;width:100%!important;padding:0 12px}

/* Cards & basics */
.dt-view .card{background:#fff;border:1px solid #e5e7eb;border-radius:14px}
.dt-view .card + .card{margin-top:18px}
.dt-view .card-body{padding:18px}
.dt-view .card-title{font-size:18px;font-weight:600;margin:0 0 12px}
.dt-view .subtle{color:#6b7280;font-size:13px}

/* Header: left | buttons */
.dt-view .dt-header{
  display:grid;
  grid-template-columns: minmax(420px,1fr) auto;
  gap:16px; align-items:start; padding:16px 18px; border-bottom:1px solid #eef0f2; background:#f9fafb;
}
.dt-view .header-left{ }
.dt-view .btns{ display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end; }

/* Title, meta */
.dt-view .dt-title{font-size:20px;font-weight:700;margin:0}
.dt-view .dt-meta{font-size:12px;font-weight:500}

/* Stats */
.dt-view .stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-bottom:10px}
.dt-view .stat{border:1px solid #eef0f2;border-radius:12px;padding:12px}
.dt-view .stat .k{font-size:12px;color:#6b7280;margin-bottom:4px}
.dt-view .stat .v{font-size:18px;font-weight:700}

/* Info grids */
.dt-view .grid-2{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px}

/* Bảng dịch vụ */
.dt-view .sv-card .card-body{padding:0}
.dt-view .sv-head{padding:12px 18px;border-bottom:1px solid #eef0f2;display:flex;justify-content:space-between;align-items:center}
.dt-view .sv-wrap{padding:12px 18px}
.dt-view .table{margin-bottom:0}
.dt-view .table th, .dt-view .table td{vertical-align:middle}
.dt-view .table tfoot th, .dt-view .table tfoot td{background:#fafafa}
.dt-view .rang-pill{display:inline-block;background:#f3f4f6;border-radius:999px;padding:2px 8px;margin:2px 4px 0 0;font-size:12px}

/* Gallery – thumbnail cố định 220px */
.dt-view .gallery{
  display:grid;
  grid-template-columns: repeat(auto-fill, 220px);
  gap:12px;
  justify-content:start;
}
.dt-view .thumb{
  position: relative;
  width:220px;
  height:220px;
  border:1px solid #e5e7eb;
  border-radius:10px;
  overflow:hidden;
  background:#fafafa;
  display:flex;
  align-items:center;
  justify-content:center;
}
.dt-view .thumb img{
  width:100%;
  height:100%;
  object-fit:cover;
  cursor:pointer;
}
.dt-view .thumb .del{
  position:absolute;top:6px;right:6px;background:#ef4444;border:none;color:#fff;border-radius:8px;padding:4px 6px;font-size:12px;opacity:.9;
  z-index: 2;                    
  text-decoration: none; 
  cursor: pointer;  
}
.dt-view .thumb .del:hover{opacity:1}

/* Lightbox */
.lb{position:fixed;inset:0;background:rgba(0,0,0,.85);display:none;align-items:center;justify-content:center;z-index:9999}
.lb img{max-width:92vw;max-height:92vh;border-radius:6px;box-shadow:0 10px 40px rgba(0,0,0,.45)}
.lb .close{position:absolute;top:16px;right:20px;color:#fff;font-size:28px;cursor:pointer}
.lb .nav{position:absolute;top:50%;transform:translateY(-50%);color:#fff;font-size:28px;cursor:pointer;padding:12px}
.lb .prev{left:10px}.lb .next{right:10px}

/* Responsive */
@media (max-width:768px){
  .dt-view .dt-header{ grid-template-columns: 1fr; gap:12px; }
  .dt-view .btns{ justify-content:flex-start; }
}
@media (max-width:480px){
  .dt-view .gallery{ grid-template-columns: repeat(2, 1fr); }
  .dt-view .thumb{ width:100%; height:auto; aspect-ratio:1/1; }
}
CSS);
}

/** ===== JS Lightbox (trang đầy đủ) ===== */
if (!$isModal) {
    $this->registerJs(<<<JS
(function(){
  var lb = document.getElementById('lb');
  if(!lb) return;
  var viewer = document.getElementById('lbImg');
  var idx = 0;
  var list = JSON.parse(document.getElementById('lbData').textContent || '[]');

  function show(i){
    if(!list.length) return;
    if(i < 0) i = list.length - 1;
    if(i >= list.length) i = 0;
    idx = i;
    viewer.src = list[idx];
    lb.style.display = 'flex';
  }
  window._openImg = function(i){ show(i); };
  document.getElementById('lbClose').onclick = function(){ lb.style.display = 'none'; };
  document.getElementById('lbPrev').onclick = function(){ show(idx-1); };
  document.getElementById('lbNext').onclick = function(){ show(idx+1); };
  lb.addEventListener('click', function(e){ if(e.target === lb) lb.style.display='none'; });
})();
JS);
}
?>

<?php if ($isModal): ?>
  <!-- ===== Chế độ hiển thị trong MODAL ===== -->
  <div class="dt-modal">
    <!-- tiêu đề để trang KH lấy set cho modal -->
    <div class="dtv-title">Điều trị #<?= (int)$model->id ?><?= $kh ? (' - ' . Html::encode($kh->ho_ten)) : '' ?></div>

    <div class="subtle" style="margin-bottom:10px">
      <b>Ngày:</b> <?= $model->ngay_dieu_tri ? Yii::$app->formatter->asDate($model->ngay_dieu_tri, 'php:d/m/Y') : '—' ?>
      · <b>Giờ:</b> <?= $model->gio ?: '—' ?>
      <?php if ($bsUnique): ?>
        · <b>Bác sĩ:</b> <?= Html::encode(implode(', ', $bsUnique)) ?>
      <?php elseif ($model->bacSi || $model->bs): ?>
        · <b>Bác sĩ:</b> <?= Html::encode($model->bacSi ? $model->bacSi->ho_ten : $model->bs) ?>
      <?php endif; ?>
      <?php if ($model->hinh_thuc_thanh_toan): ?>
        · <b>HT:</b> <?= Html::encode($model->hinh_thuc_thanh_toan) ?>
      <?php endif; ?>
    </div>

    <div>
      <?php if (!empty($imgs)): ?>
        <div class="gallery">
          <?php foreach ($imgs as $i => $img): ?>
            <div class="thumb">
              <img src="<?= Yii::$app->request->baseUrl . $img->file_path ?>" alt="img">
              <?= Html::a('Xoá', ['delete-image','id'=>$img->id], [
                'class'=>'del dt-del-img',
                'data' => ['confirm'=>'Xoá ảnh này?', 'method'=>'post'],
                'data-url' => \yii\helpers\Url::to(['delete-image','id'=>$img->id]),
              ]) ?>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="subtle" style="margin-top:6px">* Bạn có thể xoá ảnh; modal sẽ tự tải lại sau khi xoá.</div>
      <?php else: ?>
        <div class="subtle">Chưa có ảnh đính kèm.</div>
      <?php endif; ?>
    </div>
  </div>
<?php else: ?>
  <!-- ===== Trang đầy đủ (như bản bạn gửi) ===== -->
  <div class="dt-view page-wrap">
    <!-- HEADER -->
    <div class="card">
      <div class="dt-header">
        <div class="header-left">
          <div class="dt-title">
            <?= Html::encode($this->title) ?>
            <span class="dt-meta">
              <?php if ($kh): ?>
                / Thuộc KH: <b><?= Html::encode($kh->ho_ten) ?></b><?= $kh->sdt ? ' · ' . Html::encode($kh->sdt) : '' ?>
              <?php endif; ?>
            </span>
          </div>
          <div class="subtle" style="margin-top:4px">
            <b>Ngày:</b> <?= $model->ngay_dieu_tri ? Yii::$app->formatter->asDate($model->ngay_dieu_tri, 'php:d/m/Y') : '—' ?>
            · <b>Giờ:</b> <?= $model->gio ?: '—' ?>
            <?php if ($bsUnique): ?>
              · <b>Bác sĩ:</b> <?= Html::encode(implode(', ', $bsUnique)) ?>
            <?php elseif ($model->bacSi || $model->bs): ?>
              · <b>Bác sĩ:</b> <?= Html::encode($model->bacSi ? $model->bacSi->ho_ten : $model->bs) ?>
            <?php endif; ?>
            <?php if ($model->hinh_thuc_thanh_toan): ?>
              · <b>HT thanh toán:</b> <?= Html::encode($model->hinh_thuc_thanh_toan) ?>
            <?php endif; ?>
          </div>
        </div>

        <div class="btns">
          <?= Html::a('Về trang khách hàng', $kh ? ['/my-khach-hang/view','id'=>$kh->id] : ['index'], ['class'=>'btn btn-default']) ?>
          <!-- <?= Html::a('Cập nhật', ['update','id'=>$model->id], ['class'=>'btn btn-primary']) ?>
          <?= Html::a('Xoá', ['delete','id'=>$model->id], [
            'class'=>'btn btn-danger',
            'data' => ['confirm'=>'Xoá điều trị này?', 'method'=>'post']
          ]) ?> -->
        </div>
      </div>

      <div class="card-body">
        <!-- STAT TIỀN -->
        <div class="stats">
          <div class="stat">
            <div class="k">Phí điều trị</div>
            <div class="v"><?= $fmtVnd($model->phi) ?></div>
          </div>
          <div class="stat">
            <div class="k">Tạm thu</div>
            <div class="v"><?= $fmtVnd($model->tam_thu) ?></div>
          </div>
          <?php if ($kh): ?>
          <div class="stat">
            <div class="k">Còn lại (hồ sơ KH)</div>
            <div class="v"><?= $fmtVnd($kh->con_lai) ?></div>
          </div>
          <?php endif; ?>
        </div>

        <!-- THÔNG TIN CƠ BẢN + GHI CHÚ -->
        <div class="grid-2">
          <?= DetailView::widget([
            'model' => $model,
            'options' => ['class'=>'table table-striped table-bordered'],
            'attributes' => [
              [
                'label' => 'Ngày điều trị',
                'value' => $model->ngay_dieu_tri ?: null,
                'format' => $model->ngay_dieu_tri ? ['date','php:d/m/Y'] : 'raw',
              ],
              ['label' => 'Giờ', 'value' => $model->gio ?: '—'],
              // [
              //   'label' => 'Bác sĩ (chung)',
              //   'value' => $model->bacSi ? $model->bacSi->ho_ten : ($model->bs ?: '—'),
              // ],
              [
                'label' => 'Hình thức thanh toán',
                'value' => $model->hinh_thuc_thanh_toan ?: '—',
              ],
            ],
          ]) ?>

          <?= DetailView::widget([
            'model' => $model,
            'options' => ['class'=>'table table-striped table-bordered'],
            'attributes' => [
              [
                'attribute' => 'noi_dung',
                'format' => 'ntext',
                'value' => $model->noi_dung ?: '—',
                'label' => 'Ghi chú',
              ],
            ],
          ]) ?>
        </div>
      </div>
    </div>

    <!-- NỘI DUNG ĐIỀU TRỊ (DỊCH VỤ CHI TIẾT) -->
    <?php if (!empty($dvItems)): ?>
    <div class="card sv-card">
      <div class="sv-head">
        <div class="card-title" style="margin:0">Nội dung điều trị (chi tiết dịch vụ)</div>
        <div class="subtle">Tổng dòng: <?= count($dvItems) ?></div>
      </div>
      <div class="sv-wrap">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th style="min-width:260px">Tên dịch vụ</th>
                <th style="width:220px">Bác sĩ</th>
                <th style="width:160px;text-align:right">Đơn giá</th>
                <th style="width:90px;text-align:center">SL</th>
                <th style="min-width:180px">Răng</th>
                <th style="width:180px;text-align:right">Thành tiền</th>
                <?php if ($hasTamThuColumn): ?>
                  <th style="width:180px;text-align:right">Tạm thu</th>
                <?php endif; ?>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($dvItems as $row): ?>
                <tr>
                  <td>
                    <?= Html::encode($row['ten']) ?>
                    <?php if ($row['is_custom']): ?>
                      <span class="label label-default" style="margin-left:6px">Khác</span>
                    <?php endif; ?>
                  </td>
                  <td><?= Html::encode($row['bs_name']) ?></td>
                  <td style="text-align:right"><?= $fmtVnd($row['don_gia']) ?></td>
                  <td style="text-align:center"><?= (int)$row['so_luong'] ?></td>
                  <td>
                    <?php if ($row['rang_so']): ?>
                      <?php foreach (explode(',', $row['rang_so']) as $t): $t=trim($t); if(!$t) continue; ?>
                        <span class="rang-pill"><?= Html::encode($t) ?></span>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <span class="text-muted">—</span>
                    <?php endif; ?>
                  </td>
                  <td style="text-align:right;font-weight:600"><?= $fmtVnd($row['thanh_tien']) ?></td>
                  <?php if ($hasTamThuColumn): ?>
                    <td style="text-align:right"><?= $fmtVnd((int)($row['tam_thu'] ?? 0)) ?></td>
                  <?php endif; ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
                <th colspan="5" style="text-align:right">Tổng phí dịch vụ</th>
                <th style="text-align:right"><?= $fmtVnd($sumDv) ?></th>
                <?php if ($hasTamThuColumn): ?>
                  <th style="text-align:right">&nbsp;</th>
                <?php endif; ?>
              </tr>
              <?php if ($hasTamThuColumn): ?>
                <tr>
                  <th colspan="6" style="text-align:right">Tổng tạm thu</th>
                  <th style="text-align:right"><?= $fmtVnd($sumTamThu) ?></th>
                </tr>
              <?php endif; ?>
              <?php if ((int)$model->phi !== (int)$sumDv): ?>
                <tr>
                  <td colspan="<?= $hasTamThuColumn ? 7 : 6 ?>" class="text-danger">
                    * Lưu ý: Tổng trên bảng (<?= $fmtVnd($sumDv) ?>) khác với Phí điều trị đã lưu (<?= $fmtVnd($model->phi) ?>).
                  </td>
                </tr>
              <?php endif; ?>
              <?php if ($hasTamThuColumn && (int)$model->tam_thu !== (int)$sumTamThu): ?>
                <tr>
                  <td colspan="7" class="text-danger">
                    * Lưu ý: Tổng tạm thu trên bảng (<?= $fmtVnd($sumTamThu) ?>) khác với Tạm thu đã lưu (<?= $fmtVnd($model->tam_thu) ?>).
                  </td>
                </tr>
              <?php endif; ?>
            </tfoot>
          </table>
        </div>
        <div class="subtle" style="margin-top:6px">* Dòng “Khác” là nội dung thêm ad-hoc (không thuộc danh mục dịch vụ).</div>
      </div>
    </div>
    <?php endif; ?>

    <!-- ẢNH ĐÍNH KÈM -->
    <div class="card">
      <div class="dt-header" style="background:#fff">
        <div class="header-left">
          <div class="dt-title" style="margin:0">Ảnh<span class="dt-meta">/ <?= count($imgs) ?> ảnh</span></div>
        </div>
        <div class="btns">
          <?= Html::a('Thêm ảnh', ['update', 'id'=>$model->id], ['class'=>'btn btn-default']) ?>
        </div>
      </div>
      <div class="card-body">
        <?php if (!empty($imgs)): ?>
          <div class="gallery">
            <?php foreach ($imgs as $i => $img): ?>
              <div class="thumb">
                <img src="<?= Yii::$app->request->baseUrl . $img->file_path ?>" alt="img" onclick="_openImg(<?= $i ?>)">
                <?= Html::a('Xoá', ['delete-image','id'=>$img->id], [
                  'class'=>'del',
                  'data' => ['confirm'=>'Xoá ảnh này?', 'method'=>'post']
                ]) ?>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="subtle" style="margin-top:6px">* Nhấp vào ảnh để phóng to.</div>
        <?php else: ?>
          <div class="subtle">Chưa có ảnh đính kèm.</div>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!empty($imgUrls)): ?>
      <script id="lbData" type="application/json"><?= json_encode($imgUrls) ?></script>
      <div class="lb" id="lb">
        <div class="close" id="lbClose">✕</div>
        <div class="nav prev" id="lbPrev">‹</div>
        <img id="lbImg" src="" alt="">
        <div class="nav next" id="lbNext">›</div>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>
