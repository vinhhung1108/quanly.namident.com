<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * Bảng: dieu_tri_image
 * Lưu ảnh điều trị, resize “fit” và ÉP PNG/HEIC → JPEG để giảm dung lượng.
 *
 * Thuộc tính DB:
 * @property int         $id
 * @property int         $dieu_tri_id
 * @property string      $file_name
 * @property string      $file_path
 * @property int|null    $file_size
 * @property string|null $mime_type
 *
 * Thuộc tính runtime:
 * @property UploadedFile[] $files
 */
class MyDieuTriImage extends ActiveRecord
{
    /** Tuỳ chỉnh nhanh */
    private const MAX_W = 1280;          // cạnh ngang tối đa
    private const MAX_H = 1280;          // cạnh dọc tối đa
    private const JPEG_QUALITY = 88;     // 85–90 là đẹp & nhẹ
    private const WEBP_QUALITY = 85;

    /** @var UploadedFile[] */
    public $files;

    public static function tableName(): string
    {
        return 'dieu_tri_image';
    }

    public function behaviors(): array
    {
        return [TimestampBehavior::class, BlameableBehavior::class];
    }

    public function rules(): array
    {
        return [
            [['dieu_tri_id'], 'required'],
            [['dieu_tri_id', 'file_size'], 'integer'],
            [['file_name'], 'string', 'max' => 255],
            [['file_path'], 'string', 'max' => 500],
            [['mime_type'], 'string', 'max' => 100],
            [
                ['files'], 'file',
                'skipOnEmpty' => true,
                'extensions' => 'png, jpg, jpeg, heic, heif, gif, webp',
                'checkExtensionByMimeType' => true,
                'maxFiles' => 10,
                // Cho phép ảnh lớn (iPhone/iPad); chỉnh lại nếu cần.
                'maxSize' => 50 * 1024 * 1024, // 50MB
            ],
        ];
    }

    public function getMyDieuTri()
    {
        return $this->hasOne(MyDieuTri::class, ['id' => 'dieu_tri_id']);
    }

    /**
     * Lưu nhiều file cho 1 lần điều trị:
     * - Resize “fit” vào MAX_W x MAX_H nếu quá lớn
     * - PNG/HEIC/HEIF → JPEG (flatten nền trắng, strip metadata)
     * - JPEG/WEBP giữ định dạng (ghi lại để giảm size)
     */
    public function uploadAndSave(MyDieuTri $dieuTri): bool
    {
        // Cho phép xử lý batch ảnh lớn hơn (tuỳ chọn)
        @set_time_limit(180);
        // @ini_set('memory_limit', '512M');

        $this->files = UploadedFile::getInstances($this, 'files');
        if (empty($this->files)) return true; // không có file → coi như OK

        $khId    = $dieuTri->id_kh;
        $dtId    = $dieuTri->id;
        $baseRel = "/uploads/khach-hang/{$khId}/dieu-tri/{$dtId}";
        $baseAbs = Yii::getAlias('@webroot') . $baseRel;

        if (!is_dir($baseAbs)) {
            @mkdir($baseAbs, 0775, true);
        }

        foreach ($this->files as $file) {
            $t0       = microtime(true);
            $srcPath  = $file->tempName;                 // file tạm do PHP tạo (sẽ tự dọn sau request)
            $srcMime  = strtolower((string)$file->type); // image/png, image/heic, ...
            $isHeic   = in_array($srcMime, ['image/heic', 'image/heif', 'image/heif-sequence']);
            $isPng    = ($srcMime === 'image/png');
            $isWebp   = str_contains($srcMime, 'webp');

            $safeBase = uniqid('', true) . '_' . preg_replace('/[^A-Za-z0-9\.\-_]/', '_', pathinfo($file->name, PATHINFO_FILENAME));

            // Đích mong muốn: HEIC/PNG → JPG; WEBP giữ WebP; còn lại mặc định JPG
            $destExt  = ($isHeic || $isPng) ? 'jpg' : ($isWebp ? 'webp' : 'jpg');
            $destName = $safeBase . '.' . $destExt;
            $destAbs  = $baseAbs . '/' . $destName;

            $ok = false;

            // ========= NHÁNH 1: Imagick (nhanh, hỗ trợ HEIC nếu có libheif) =========
            if (extension_loaded('imagick')) {
                try {
                    $im = new \Imagick();
                    $im->readImage($srcPath);

                    // Auto-orient theo EXIF
                    if (method_exists($im, 'autoOrient')) {
                        $im->autoOrient();
                    }

                    // Resize “fit” theo MAX_W/H (giữ tỉ lệ, không upscale)
                    $im->thumbnailImage(self::MAX_W, self::MAX_H, true, true);

                    // Nếu lưu JPG: flatten lên nền trắng để loại bỏ alpha
                    if ($destExt === 'jpg') {
                        $w = $im->getImageWidth();
                        $h = $im->getImageHeight();
                        $bg = new \Imagick();
                        $bg->newImage($w, $h, new \ImagickPixel('white'));
                        $bg->setImageColorspace($im->getImageColorspace());
                        $bg->compositeImage($im, \Imagick::COMPOSITE_OVER, 0, 0);
                        $im->clear();
                        $im = $bg;
                    }

                    // Bỏ metadata
                    $im->stripImage();

                    // Thiết lập format + chất lượng
                    if ($destExt === 'jpg') {
                        $im->setImageFormat('jpeg');
                        $im->setImageCompression(\Imagick::COMPRESSION_JPEG);
                        $im->setImageCompressionQuality(self::JPEG_QUALITY);
                        $im->setInterlaceScheme(\Imagick::INTERLACE_JPEG); // progressive
                    } elseif ($destExt === 'webp') {
                        $im->setImageFormat('webp');
                        if (method_exists($im, 'setOption')) {
                            $im->setOption('webp:method', '6');
                        }
                        $im->setImageCompressionQuality(self::WEBP_QUALITY);
                    }

                    $ok = $im->writeImage($destAbs);
                    $im->destroy();
                } catch (\Throwable $e) {
                    Yii::warning('Imagick fail: ' . $e->getMessage(), __METHOD__);
                    $ok = false;
                }
            }

            // ========= NHÁNH 2: Fallback Imagine =========
            if (!$ok) {
                try {
                    $imagine = \yii\imagine\Image::getImagine();
                    $img     = $imagine->open($srcPath);

                    // Resize “fit” theo MAX_W/H
                    $size = $img->getSize();
                    if ($size->getWidth() > self::MAX_W || $size->getHeight() > self::MAX_H) {
                        $ratio = min(self::MAX_W / $size->getWidth(), self::MAX_H / $size->getHeight());
                        $img->resize(new \Imagine\Image\Box(
                            (int)floor($size->getWidth()  * $ratio),
                            (int)floor($size->getHeight() * $ratio)
                        ));
                    }

                    // PNG/HEIC → JPG: flatten nền trắng
                    if ($destExt === 'jpg') {
                        $palette = new \Imagine\Image\Palette\RGB();
                        $canvas  = $imagine->create($img->getSize(), $palette->color('#ffffff', 100));
                        $canvas->paste($img, new \Imagine\Image\Point(0, 0));
                        $img = $canvas;
                    }

                    $opts = [];
                    if ($destExt === 'jpg')   $opts['jpeg_quality'] = self::JPEG_QUALITY;
                    if ($destExt === 'webp')  $opts['webp_quality']  = self::WEBP_QUALITY;

                    $img->save($destAbs, $opts);
                    $ok = is_file($destAbs);
                } catch (\Throwable $e) {
                    Yii::error('Imagine fail: ' . $e->getMessage(), __METHOD__);
                    // Bất đắc dĩ: copy nguyên bản để không mất dữ liệu
                    @copy($srcPath, $destAbs);
                    $ok = is_file($destAbs);
                }
            }

            // ========= Log thời gian & kích thước =========
            try {
                $ms     = round((microtime(true) - $t0) * 1000);
                $before = @filesize($srcPath);
                $after  = @filesize($destAbs);
                Yii::info(sprintf(
                    'DTIMG: mime=%s | %sKB -> %sKB | %d ms | %s',
                    $srcMime,
                    $before ? round($before/1024) : '-',
                    $after  ? round($after/1024)  : '-',
                    $ms,
                    $destName
                ), __METHOD__);
            } catch (\Throwable $e) {
                // ignore log error
            }

            // ========= Lưu DB =========
            $row = new self();
            $row->dieu_tri_id = $dtId;
            $row->file_name   = $destName;
            $row->file_path   = $baseRel . '/' . $destName; // lưu tương đối
            $row->file_size   = @filesize($destAbs) ?: $file->size;
            $row->mime_type   = ($destExt === 'jpg')
                ? 'image/jpeg'
                : (($destExt === 'webp') ? 'image/webp' : $file->type);

            if (!$row->save(false)) {
                @unlink($destAbs);
                return false;
            }
        }

        return true;
    }
}
