<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * @property int $id
 * @property int $dieu_tri_id
 * @property string $file_name
 * @property string $file_path  // lưu tương đối, bắt đầu bằng /uploads/...
 * @property int|null $file_size
 * @property string|null $mime_type
 */
class DieuTriImage extends ActiveRecord
{
    /** @var UploadedFile[] */
    public $files;

    public static function tableName() { return 'dieu_tri_image'; }

    public function behaviors()
    {
        return [TimestampBehavior::class, BlameableBehavior::class];
    }

    public function rules()
    {
        return [
            [['dieu_tri_id'], 'required'],
            [['dieu_tri_id', 'file_size'], 'integer'],
            [['file_name'], 'string', 'max' => 255],
            [['file_path'], 'string', 'max' => 500],
            [['mime_type'], 'string', 'max' => 100],
            [['files'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, heic, gif, webp', 'maxFiles' => 10, 'checkExtensionByMimeType' => true],
        ];
    }

    public function getDieuTri()
    {
        return $this->hasOne(DieuTri::class, ['id' => 'dieu_tri_id']);
    }

    /**
     * Lưu nhiều file cho 1 lần điều trị
     * @param DieuTri $dieuTri
     * @return bool
     */
    public function uploadAndSave(DieuTri $dieuTri): bool
    {
        $this->files = UploadedFile::getInstances($this, 'files');
        if (empty($this->files)) return true; // không có file thì coi như ok

        $khId = $dieuTri->id_kh;
        $dtId = $dieuTri->id;

        // đường dẫn vật lý và tương đối
        $baseRel = "/uploads/khach-hang/{$khId}/dieu-tri/{$dtId}";
        $baseAbs = Yii::getAlias('@webroot') . $baseRel;

        if (!is_dir($baseAbs)) {
            @mkdir($baseAbs, 0775, true);
        }

        foreach ($this->files as $file) {
            $safeName = uniqid() . '_' . preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $file->name);
            $destAbs = $baseAbs . '/' . $safeName;
            if ($file->saveAs($destAbs)) {
                $img = new self();
                $img->dieu_tri_id = $dtId;
                $img->file_name = $safeName;
                $img->file_path = $baseRel . '/' . $safeName; // LƯU TƯƠNG ĐỐI -> đúng yêu cầu của bạn
                $img->file_size = $file->size;
                $img->mime_type = $file->type;
                if (!$img->save(false)) {
                    // nếu cần, có thể xóa file vừa lưu
                    @unlink($destAbs);
                    return false;
                }
            } else {
                return false;
            }
        }
        return true;
    }
}
