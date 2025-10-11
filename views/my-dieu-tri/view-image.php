<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\MyDieuTri */

$kh   = $model->khachHang ?? null;
$imgs = $model->images ?? [];

$baseUrl   = Yii::$app->request->baseUrl;
$selfUrl   = Url::to(['my-dieu-tri/view-image', 'id' => (int)$model->id]); // để JS reload modal
$updateUrl = Url::to(['my-dieu-tri/update', 'id' => (int)$model->id]);

// CSS
$this->registerCss(<<<CSS
/* Ẩn tiêu đề nội bộ – trang ngoài sẽ đọc và set vào header modal */
.dtv-title{display:none}

/* Khối trong modal */
.dt-modal .subtle{color:#6b7280;font-size:13px}
.dt-modal .hdr{display:flex;justify-content:space-between;align-items:center;gap:8px;margin-bottom:10px}
.dt-modal .btns{display:flex;gap:6px;flex-wrap:wrap}

/* Gallery lớn hơn */
.dt-modal .gallery{
  display:grid;
  grid-template-columns:repeat(auto-fill, 220px);
  gap:12px;
  justify-content:start;
}
.dt-modal .thumb{
  position:relative;
  width:220px; height:220px;
  border:1px solid #e5e7eb; border-radius:10px; overflow:hidden; background:#fafafa;
  display:flex; align-items:center; justify-content:center;
}
.dt-modal .thumb img{width:100%;height:100%;object-fit:cover; cursor:zoom-in;}
.dt-modal .thumb .del{
  position:absolute; top:6px; right:6px;
  background:#ef4444; border:none; color:#fff; border-radius:8px; padding:4px 6px; font-size:12px; opacity:.9;
  text-decoration:none
}
.dt-modal .thumb .del:hover{opacity:1}

/* Lightbox overlay */
.lb{position:fixed; inset:0; background:rgba(0,0,0,.9); display:none; align-items:center; justify-content:center; z-index:99999}
.lb img{max-width:92vw; max-height:92vh; border-radius:8px; box-shadow:0 10px 40px rgba(0,0,0,.5)}
.lb .close, .lb .nav{position:absolute; color:#fff; cursor:pointer; user-select:none}
.lb .close{top:16px; right:20px; font-size:28px}
.lb .nav{top:50%; transform:translateY(-50%); font-size:32px; padding:12px 14px; opacity:.9}
.lb .prev{left:10px}
.lb .next{right:10px}
.lb .nav:hover, .lb .close:hover{opacity:1}

/* Kích thước modal to hơn (áp dụng cho cả BS3/BS5) */
@media (min-width: 992px){
  .modal-dialog.modal-xl{ width:95vw; max-width:1400px; }
}
CSS);

// JS
$js = <<<JS
(function(){
  var \$wrap = \$('#dt-modal-wrap');
  if (!\$wrap.length) return;

  // ===== Phóng to modal dialog: thêm class & style trực tiếp cho cả BS3/BS5 =====
  (function enlargeModal(){
    var \$dlg = \$wrap.closest('.modal-dialog');
    if (!\$dlg.length) return;
    // BS5: có sẵn modal-xl
    \$dlg.addClass('modal-xl');
    // BS3 fallback: set inline width
    try {
      \$dlg.css({ width: '95vw', 'max-width': '1400px' });
    } catch(e){}
    // Tăng padding cho modal-body nếu cần
    \$wrap.closest('.modal-content').css('border-radius','12px');
  })();

  // ===== Xoá ảnh (AJAX) + reload nội dung modal =====
  \$wrap.off('click.dtDel','.dt-del-img').on('click.dtDel','.dt-del-img', function(e){
    e.preventDefault();
    var \$a = \$(this);
    var url = \$a.data('url') || \$a.attr('href');
    if (!url) return;
    if (!confirm('Xoá ảnh này?')) return;

    // CSRF
    var data = {};
    try {
      var param = yii && yii.getCsrfParam ? yii.getCsrfParam() : null;
      var token = yii && yii.getCsrfToken ? yii.getCsrfToken() : null;
      if (param && token) { data[param] = token; }
    } catch(e){}

    $.post(url, data).always(function(){
      var reloadUrl = \$wrap.data('selfUrl') || window.location.href;
      var \$body = \$wrap.find('.modal-body-inner');
      if (\$body.length){ \$body.css('opacity', .5); }
      $.get(reloadUrl).done(function(html){
        var \$new = $('<div>').html(html);
        var \$newWrap = \$new.find('#dt-modal-wrap');
        if (\$newWrap.length){
          \$wrap.replaceWith(\$newWrap);
        } else if (\$body.length) {
          \$body.html(html).css('opacity', 1);
        }
      }).fail(function(){
        alert('Không tải lại được nội dung sau khi xoá. Hãy đóng và mở lại modal.');
      });
    });
  });

  // ===== Lightbox xem ảnh lớn =====
  var list = [];
  \$wrap.find('.thumb img').each(function(){ list.push(\$(this).attr('src')); });

  // Tạo lightbox 1 lần
  if (!window.__dtLightboxInited){
    window.__dtLightboxInited = true;
    var lb = $(
      '<div class="lb" id="dtLb">' +
        '<div class="close" id="dtLbClose">✕</div>' +
        '<div class="nav prev" id="dtLbPrev">‹</div>' +
        '<img id="dtLbImg" src="" alt="">' +
        '<div class="nav next" id="dtLbNext">›</div>' +
      '</div>'
    ).appendTo(document.body);

    var idx = 0, \$img = \$('#dtLbImg');

    function show(i){
      if(!list.length) return;
      if(i < 0) i = list.length - 1;
      if(i >= list.length) i = 0;
      idx = i;
      \$img.attr('src', list[idx]);
      lb.css('display','flex');
    }
    function hide(){ lb.hide(); }

    window.__dtLbShow = show; // cho lần render khác có thể dùng lại

    \$('#dtLbClose').on('click', hide);
    \$('#dtLbPrev').on('click', function(){ show(idx - 1); });
    \$('#dtLbNext').on('click', function(){ show(idx + 1); });
    lb.on('click', function(e){ if (e.target === this) hide(); });

    // Phím tắt
    $(document).on('keydown.dtLb', function(e){
      if (!lb.is(':visible')) return;
      if (e.key === 'Escape') hide();
      else if (e.key === 'ArrowLeft') \$('#dtLbPrev').click();
      else if (e.key === 'ArrowRight') \$('#dtLbNext').click();
    });
  }

  // Gán click mở lightbox cho từng ảnh (sau mỗi lần reload nội dung modal)
  \$wrap.off('click.dtOpen','.thumb img').on('click.dtOpen','.thumb img', function(){
    var src = \$(this).attr('src');
    var i = list.indexOf(src);
    if (i < 0) { list.push(src); i = list.length - 1; }
    if (typeof window.__dtLbShow === 'function') window.__dtLbShow(i);
  });

  // Đặt tiêu đề ra header modal phía ngoài (nếu cần)
  try{
    var title = \$wrap.find('.dtv-title').text().trim();
    if (title) {
      // Tìm phần header gần nhất và thay text (tương thích BS3/BS5)
      var \$hdr = \$wrap.closest('.modal-content').find('.modal-header .modal-title');
      if (\$hdr.length) \$hdr.text(title);
    }
  }catch(e){}
})();
JS;
$this->registerJs($js);
?>

<div id="dt-modal-wrap" class="dt-modal" data-self-url="<?= Html::encode($selfUrl) ?>">
  <!-- Tiêu đề để trang ngoài đọc & đặt cho header modal -->
  <div class="dtv-title">
    Điều trị #<?= (int)$model->id ?><?= $kh ? (' - ' . Html::encode($kh->ho_ten)) : '' ?>
  </div>

  <div class="hdr">
    <div class="subtle">
      <b>Ngày:</b> <?= $model->ngay_dieu_tri ? Yii::$app->formatter->asDate($model->ngay_dieu_tri, 'php:d/m/Y') : '—' ?>
      · <b>Giờ:</b> <?= $model->gio ?: '—' ?>
      <?php if ($model->hinh_thuc_thanh_toan): ?>
        · <b>HT:</b> <?= Html::encode($model->hinh_thuc_thanh_toan) ?>
      <?php endif; ?>
    </div>
    <div class="btns">
      <?= Html::a('Thêm ảnh', $updateUrl, [
        'class' => 'btn btn-default btn-sm',
        'target'=> '_blank',
        'data-pjax' => '0',
        'title' => 'Mở trang cập nhật để thêm ảnh'
      ]) ?>
    </div>
  </div>

  <?php if (!empty($imgs)): ?>
    <div class="gallery">
      <?php foreach ($imgs as $img): ?>
        <div class="thumb">
          <img src="<?= $baseUrl . $img->file_path ?>" alt="img">
          <?= Html::a('Xoá', ['delete-image','id'=>$img->id], [
            'class'=>'del dt-del-img',
            'data-url' => Url::to(['delete-image','id'=>$img->id]),
          ]) ?>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="subtle" style="margin-top:6px">
      * Bấm vào ảnh để phóng to; dùng phím ←/→ để chuyển, ESC để đóng. Xoá ảnh xong modal sẽ tự tải lại.
    </div>
  <?php else: ?>
    <div class="subtle">Chưa có ảnh đính kèm.</div>
  <?php endif; ?>
</div>
