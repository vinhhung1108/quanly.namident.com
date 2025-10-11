<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

class MyDieuTri extends ActiveRecord
{
    public $phi_t;
    public $tam_thu_t;

    public static function tableName(): string { return 'dieu_tri'; }

    public function behaviors(): array
    {
        // BỎ behaviors nếu bảng không có các cột này
        return [
            TimestampBehavior::class,   // cần cột created_at, updated_at (int)
            BlameableBehavior::class,   // cần cột created_by, updated_by (int)
        ];
    }

    public function rules(): array
    {
        return [
            [['id_kh'], 'required'],
            [['id_kh', 'lan_dieu_tri', 'phi', 'tam_thu', 'bs_id'], 'integer'],
            [['ngay_dieu_tri', 'gio', 'phi_t', 'tam_thu_t', 'hinh_thuc_thanh_toan'], 'safe'],
            [['ngay_dieu_tri'], 'required'],
            [['noi_dung', 'bs'], 'string'],

            // Mặc định 0 khi để trống
            [['phi', 'tam_thu'], 'default', 'value' => 0],

            // Mặc định ngày điều trị = hôm nay nếu bỏ trống
            ['ngay_dieu_tri', 'default', 'value' => function(){ return date('Y-m-d'); }],

            // Trim text
            [['noi_dung', 'bs', 'hinh_thuc_thanh_toan'], 'filter', 'filter' => 'trim'],

            // Quan hệ tồn tại (bật nếu chắc chắn có các bảng/khóa ngoại)
            ['id_kh', 'exist', 'skipOnError' => true,
                'targetClass' => MyKhachHang::class, 'targetAttribute' => ['id_kh' => 'id']],
            ['bs_id', 'exist', 'skipOnError' => true,
                'targetClass' => \app\models\BacSi::class, 'targetAttribute' => ['bs_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'id_kh' => 'Mã KH',
            'lan_dieu_tri' => 'Lần điều trị',
            'ngay_dieu_tri' => 'Ngày điều trị',
            'gio' => 'Giờ',
            'noi_dung' => 'Nội dung',
            'bs' => 'Bác sĩ',
            'bs_id' => 'Bác sĩ',
            'phi' => 'Phí điều trị',
            'tam_thu' => 'Tạm thu',
            'hinh_thuc_thanh_toan' => 'Hình thức TT',
        ];
    }

    /** Lọc chuỗi có dấu phẩy/chấm… về số nguyên trước khi validate */
    public function beforeValidate()
    {
        $this->phi     = $this->toInt($this->phi);
        $this->tam_thu = $this->toInt($this->tam_thu);
        return parent::beforeValidate();
    }

    private function toInt($v): int
    {
        if ($v === null || $v === '') return 0;
        if (is_int($v)) return $v;
        if (is_numeric($v)) return (int)$v;
        if (is_string($v)) {
            $neg = str_starts_with($v, '-') ? -1 : 1;
            $digits = preg_replace('/\D+/', '', $v);
            return $digits === '' ? 0 : $neg * (int)$digits;
        }
        return (int)$v;
    }

    // ================== Quan hệ ==================
    public function getKhachHang()
    {
        return $this->hasOne(MyKhachHang::class, ['id' => 'id_kh']);
    }

    public function getImages()
    {
        return $this->hasMany(MyDieuTriImage::class, ['dieu_tri_id' => 'id'])->orderBy(['id'=>SORT_DESC]);
    }

    public function getBacSi()
    {
        return $this->hasOne(\app\models\BacSi::class, ['id' => 'bs_id']);
    }

    // ================== Xoá ảnh kèm theo khi xoá điều trị ==================
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        // Xoá toàn bộ ảnh (file + bản ghi)
        $this->deleteAllImages();

        return true;
    }

    protected function deleteAllImages(): void
    {
        // Lấy theo batches để an toàn nếu có nhiều ảnh
        /** @var MyDieuTriImage[] $imgs */
        $imgs = $this->getImages()->all();
        foreach ($imgs as $img) {
            $abs = Yii::getAlias('@webroot') . $img->file_path;
            if (is_file($abs)) {
                @unlink($abs);
            }
            // Xoá bản ghi ảnh
            $img->delete(false);
        }
    }

    // ================== Recalc totals sau khi save/delete ==================
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        self::recalcCustomerTotals((int)$this->id_kh);
    }

    public function afterDelete()
    {
        parent::afterDelete();
        self::recalcCustomerTotals((int)$this->id_kh);
    }

    private static function recalcCustomerTotals(int $khId): void
    {
        $db = Yii::$app->db;
        $tong_phi = (int)$db->createCommand(
            "SELECT COALESCE(SUM(phi),0) FROM dieu_tri WHERE id_kh=:id",
            [':id' => $khId]
        )->queryScalar();

        $tam_thu = (int)$db->createCommand(
            "SELECT COALESCE(SUM(tam_thu),0) FROM dieu_tri WHERE id_kh=:id",
            [':id' => $khId]
        )->queryScalar();

        if (($kh = MyKhachHang::findOne($khId))) {
            $kh->tong_phi = $tong_phi;
            $kh->tam_thu  = $tam_thu;
            $kh->con_lai  = $tong_phi - $tam_thu;
            $kh->save(false);
        }
    }

    public function getDvItems()
    {
        return $this->hasMany(\app\models\MyDieuTriDv::class, ['dieu_tri_id' => 'id'])
            ->orderBy(['id' => SORT_ASC]);
    }

    /** Tổng phí từ các dòng dịch vụ */
    public function calcTotalPhiFromItems(): int
    {
        $sum = (int)\Yii::$app->db->createCommand(
            "SELECT COALESCE(SUM(thanh_tien),0) FROM dieu_tri_dich_vu WHERE dieu_tri_id=:id",
            [':id'=>$this->id]
        )->queryScalar();
        return $sum;
    }
}
