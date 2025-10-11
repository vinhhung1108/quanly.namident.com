<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $this yii\web\View */
/** @var $kh app\models\MyKhachHang */
/** @var $rows app\models\MyDieuTri[]|null */

$kh   = $kh ?? $model ?? null;
$rows = $rows ?? [];

$baseUrl = Yii::$app->request->baseUrl;

// Gom nhóm ảnh theo điều trị
$groups = [];
$total  = 0;

foreach ($rows as $dt) {
    $imgs = $dt->images ?? [];
    if (!$imgs) continue;

    $dateTx = $dt->ngay_dieu_tri
        ? Yii::$app->formatter->asDate($dt->ngay_dieu_tri, 'php:d/m/Y')
        : '—';

    $groups[] = [
        'id'    => (int)$dt->id,
        'title' => "Điều trị #{$dt->id} · {$dateTx}" . ($dt->gio ? " · {$dt->gio}" : ''),
        'imgs'  => $imgs,
    ];
    $total += count($imgs);
}

$this->registerCss(<<<CSS
.kh-allimg .subtle{color:#6b7280;font-size:13px}
.kh-allimg .hdr{display:flex;justify-content:space-between;align-items:center;gap:8px;margin-bottom:8px}
.kh-allimg .btns{display:flex;gap:6px;flex-wrap:wrap}

/* Section nhóm điều trị */
.kh-allimg .section{margin-bottom:18px}
.kh-allimg .section-hd{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px}
.kh-allimg .section-ttl{font-weight:600}

/* Gallery */
.kh-allimg .gallery{
  display:grid;
  grid-template-columns:repeat(auto-fill, 200px);
  gap:12px;
  justify-content:start;
}
.kh-allimg .thumb{
  position:relative; width:200px; height:200px;
  border:1px solid #e5e7eb; border-radius:10px; overflow:hidden; background:#fafafa;
  display:flex; align-items:center; justify-content:center;
}
.kh-allimg .thumb img{width:100%;height:100%;object-fit:cover; cursor:zoom-in;}
.kh-allimg .thumb .del{
  position:absolute; top:6px; right:6px;
  background:#ef4444; border:none; color:#fff; border-radius:8px; padding:4px 6px; font-size:12px; opacity:.9;
  text-decoration:none
}
.kh-allimg .thumb .del:hover{opacity:1}

/* Lightbox */
.kh-lb{position:fixed; inset:0; background:rgba(0,0,0,.9); display:none; align-items:center; justify-content:center; z-index:99999}
.kh-lb img{max-width:92vw; max-height:92vh; border-radius:8px; box-shadow:0 10px 40px rgba(0,0,0,.5)}
.kh-lb .close, .kh-lb .nav{position:absolute; color:#fff; cursor:pointer; user-select:none}
.kh-lb .close{top:16px; right:20px; font-size:28px}
.kh-lb .nav{top:50%; transform:translateY(-50%); font-size:32px; padding:12px 14px; opacity:.9}
.kh-lb .prev{left:10px}
.kh-lb .next{right:10px}
.kh-lb .nav:hover, .kh-lb .close:hover{opacity:1}

/* Mở rộng kích thước modal */
@media (min-width: 992px){
  .modal-dialog.modal-xl{ width:95vw; max-width:1400px; }
}
CSS);

$js = <<<JS
(function(){
  var \$wrap = \$('#kh-allimg-wrap');
  if (!\$wrap.length) return;

  // Phóng to modal dialog: hỗ trợ cả BS3/BS5
  (function enlargeModal(){
    var \$dlg = \$wrap.closest('.modal-dialog');
    if (!\$dlg.length) return;
    \$dlg.addClass('modal-xl');
    try { \$dlg.css({ width:'95vw', 'max-width':'1400px' }); } catch(e){}
    \$wrap.closest('.modal-content').css('border-radius','12px');
  })();

  // Xoá ảnh (AJAX) rồi reload nội dung modal
  \$wrap.off('click.khDel','.kh-del-img').on('click.khDel','.kh-del-img', function(e){
    e.preventDefault();
    if (!confirm('Xoá ảnh này?')) return;
    var url = \$(this).data('url') || \$(this).attr('href');
    if (!url) return;

    // CSRF
    var data = {};
    try {
      var p = yii.getCsrfParam(), t = yii.getCsrfToken();
      if (p && t) data[p] = t;
    } catch(e){}

    var reloadUrl = \$wrap.data('selfUrl') || window.location.href;
    var \$body = \$wrap.find('.modal-body-inner');
    if (\$body.length){ \$body.css('opacity', .5); }

    $.post(url, data).always(function(){
      $.get(reloadUrl).done(function(html){
        var \$new = $('<div>').html(html);
        var \$newWrap = \$new.find('#kh-allimg-wrap');
        if (\$newWrap.length){
          \$wrap.replaceWith(\$newWrap);
        } else if (\$body.length){
          \$body.html(html).css('opacity', 1);
        }
      }).fail(function(){
        alert('Không tải lại được nội dung sau khi xoá. Hãy đóng và mở lại modal.');
      });
    });
  });

  // Lightbox: tập hợp toàn bộ ảnh
  var list = [];
  \$wrap.find('.thumb img').each(function(){ list.push(\$(this).attr('src')); });

  // Tạo lightbox 1 lần
  if (!window.__khLbInited){
    window.__khLbInited = true;
    var lb = $(
      '<div class="kh-lb" id="khLb">' +
        '<div class="close" id="khLbClose">✕</div>' +
        '<div class="nav prev" id="khLbPrev">‹</div>' +
        '<img id="khLbImg" src="" alt="">' +
        '<div class="nav next" id="khLbNext">›</div>' +
      '</div>'
    ).appendTo(document.body);

    var idx = 0, \$img = \$('#khLbImg');

    function show(i){
      if(!list.length) return;
      if(i < 0) i = list.length - 1;
      if(i >= list.length) i = 0;
      idx = i;
      \$img.attr('src', list[idx]);
      lb.css('display','flex');
    }
    function hide(){ lb.hide(); }

    window.__khLbShow = show;

    \$('#khLbClose').on('click', hide);
    \$('#khLbPrev').on('click', function(){ show(idx - 1); });
    \$('#khLbNext').on('click', function(){ show(idx + 1); });
    lb.on('click', function(e){ if (e.target === this) hide(); });

    // Keyboard
    $(document).on('keydown.khLb', function(e){
      if (!lb.is(':visible')) return;
      if (e.key === 'Escape') hide();
      else if (e.key === 'ArrowLeft') \$('#khLbPrev').click();
      else if (e.key === 'ArrowRight') \$('#khLbNext').click();
    });
  }

  // Gán click mở lightbox
  \$wrap.off('click.khOpen','.thumb img').on('click.khOpen','.thumb img', function(){
    var src = \$(this).attr('src');
    var i = list.indexOf(src);
    if (i < 0) { list.push(src); i = list.length - 1; }
    if (typeof window.__khLbShow === 'function') window.__khLbShow(i);
  });

  // Đặt tiêu đề ra modal header
  try{
    var ttl = \$wrap.find('.kh-title-hidden').text().trim();
    if (ttl) {
      var \$hdr = \$wrap.closest('.modal-content').find('.modal-header .modal-title');
      if (\$hdr.length) \$hdr.text(ttl);
    }
  }catch(e){}
})();
JS;
$this->registerJs($js);
?>

<div id="kh-allimg-wrap" class="kh-allimg" data-self-url="<?= Html::encode(Url::to(['my-khach-hang/all-images', 'id' => (int)$kh->id])) ?>">
  <!-- <div class="kh-title-hidden">
    Tất cả ảnh của KH: <?= Html::encode($kh->ho_ten) ?> (<?= (int)$total ?> ảnh)
  </div> -->

  <div class="hdr">
    <div class="subtle">
      KH: <b><?= Html::encode($kh->ho_ten) ?></b>
      <?= $kh->sdt ? ' · ' . Html::encode($kh->sdt) : '' ?>
      · Ảnh: <b><?= (int)$total ?></b>
    </div>
  </div>

  <?php if (!$groups): ?>
    <div class="subtle">Khách hàng này chưa có ảnh đính kèm ở các lần điều trị.</div>
  <?php else: ?>
    <?php foreach ($groups as $g): ?>
      <div class="section">
        <div class="section-hd">
          <div class="section-ttl"><?= Html::encode($g['title']) ?></div>
          <div class="subtle"><?= count($g['imgs']) ?> ảnh</div>
        </div>
        <div class="gallery">
          <?php foreach ($g['imgs'] as $img): ?>
            <div class="thumb">
              <img src="<?= $baseUrl . $img->file_path ?>" alt="img">
              <?= Html::a('Xoá',
                    ['my-dieu-tri/delete-image','id'=>$img->id],
                    ['class'=>'del kh-del-img','data-url'=>Url::to(['my-dieu-tri/delete-image','id'=>$img->id])]) ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>

    <div class="subtle" style="margin-top:6px">
      * Bấm vào ảnh để phóng to; dùng phím ←/→ để chuyển, ESC để đóng.
    </div>
  <?php endif; ?>
</div>
