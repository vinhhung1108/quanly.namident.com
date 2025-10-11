<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * Model mới, dùng song song với KhachHang cũ.
 * Bảng: khach_hang
 *
 * Thuộc tính ảo:
 *  - $nhom_ids: mảng id nhóm KH (many-to-many qua kh_nhom_map)
 *  - $last_ngay_dieu_tri, $last_phi, $last_tam_thu: dùng để hiển thị/sort ở Search
 */
class MyKhachHang extends ActiveRecord
{
    /** ======= Thuộc tính ảo ======= */
    public $nhom_ids = [];         // nhiều-nhiều nhóm KH (form: select2 multiple)
    public $last_ngay_dieu_tri;    // để hiển thị/sort ở grid (không phải cột thật)
    public $last_tam_thu;          // "
    public $last_phi;              // "

    public static function tableName(): string
    {
        return 'khach_hang';
    }

    public function rules(): array
    {
        return [
            [['ho_ten'], 'required', 'message' => 'Thiếu họ tên'],

            // text
            [['ghi_chu', 'dia_chi', 'gioi_thieu', 'dieu_tri', 'video', 'gioi_tinh',
              'chan_doan', 'tien_su_benh', 'nghe_nghiep', 'noi_dung_hen'], 'string'],

            // số
            [['tong_phi', 'tam_thu', 'con_lai', 'tam_thu_last', 'nhom_kh'], 'integer'],

            // ngày/giờ
            [['ngay_sinh', 'ngay_hen', 'ngay_dieu_tri', 'gio', 'gio_hen'], 'safe'],

            // string length
            [['sdt'], 'string', 'max' => 15],
            [['ho_ten', 'bs_dieu_tri', 'ma_the'], 'string', 'max' => 50],
            [['gioi_thieu'], 'string', 'max' => 25],

            // unique
            [['ma_the'], 'unique', 'skipOnEmpty' => true],

            // default
            [['tong_phi','tam_thu','con_lai'], 'default', 'value' => 0],

            // nhóm KH (nhiều-nhiều) – nhận mảng số nguyên dương
            [['nhom_ids'], 'default', 'value' => []],
            [['nhom_ids'], 'each', 'rule' => ['integer']],

            // các field ảo dùng để sort/filter ở Search
            [['last_ngay_dieu_tri', 'last_phi', 'last_tam_thu'], 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'Mã KH',
            'ho_ten' => 'Họ tên',
            'sdt' => 'Số điện thoại',
            'ngay_sinh' => 'Ngày sinh',
            'gioi_tinh' => 'Giới tính',
            'dia_chi' => 'Địa chỉ',
            'ma_the' => 'Mã KH',
            'gioi_thieu' => 'Giới thiệu',
            'tong_phi' => 'Tổng phí',
            'tam_thu' => 'Tạm thu',
            'con_lai' => 'Còn lại',
            'ghi_chu' => 'Ghi chú',
            'ngay_hen' => 'Ngày hẹn',
            'gio_hen' => 'Giờ hẹn',
            'noi_dung_hen' => 'Nội dung hẹn',
            'chan_doan' => 'Chẩn đoán',
            'tien_su_benh' => 'Tiền sử bệnh',
            'nghe_nghiep' => 'Nghề nghiệp',
            'bs_dieu_tri' => 'Bác sĩ điều trị',
            'dieu_tri' => 'Đã điều trị',
            'video' => 'Video',
            'nhom_kh' => 'Nhóm KH (cũ - nếu có)',
            'tam_thu_last' => 'Tạm thu lần cuối',

            'nhom_ids' => 'Nhóm khách hàng',
            'last_ngay_dieu_tri' => 'Ngày điều trị gần nhất',
            'last_phi' => 'Phí điều trị gần nhất',
            'last_tam_thu' => 'Tạm thu gần nhất',
        ];
    }

    /** Quan hệ: các lần điều trị của KH (model mới) */
    public function getDieuTris()
    {
        return $this->hasMany(MyDieuTri::class, ['id_kh' => 'id'])
            ->orderBy(['ngay_dieu_tri'=>SORT_DESC, 'id'=>SORT_DESC]);
    }

    /** Quan hệ: các nhóm KH (qua bảng nối kh_nhom_map) */
    public function getNhoms()
    {
        return $this->hasMany(NhomKhachHang::class, ['id' => 'nhom_id'])
            ->viaTable('kh_nhom_map', ['kh_id' => 'id'])
            ->orderBy(['ten_nhom' => SORT_ASC]);
    }

    /** 
     * Chuẩn hoá trước validate:
     * - Đảm bảo $nhom_ids là mảng số nguyên dương (loại bỏ '', 0, null).
     */
    public function beforeValidate()
    {
        if (!parent::beforeValidate()) return false;

        if (is_string($this->nhom_ids)) {
            $this->nhom_ids = trim($this->nhom_ids) === '' ? [] : explode(',', $this->nhom_ids);
        }
        if (!is_array($this->nhom_ids)) {
            $this->nhom_ids = (array)$this->nhom_ids;
        }

        $this->nhom_ids = array_values(array_unique(array_filter(
            array_map('intval', $this->nhom_ids),
            fn ($v) => $v > 0
        )));

        return true;
    }

    /**
     * Sau khi find: nạp sẵn danh sách nhóm vào $nhom_ids để hiển thị trên form.
     */
    public function afterFind()
    {
        parent::afterFind();

        $this->nhom_ids = (new Query())
            ->select('nhom_id')
            ->from('kh_nhom_map')
            ->where(['kh_id' => $this->id])
            ->column();
    }

    /**
     * Đồng bộ bảng nối kh_nhom_map sau khi lưu.
     * Bảo đảm không chèn nhom_id = 0 (tránh lỗi FK).
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $db = Yii::$app->db;

        // nhóm hiện có trong DB
        $old = (new Query())
            ->select('nhom_id')
            ->from('kh_nhom_map')
            ->where(['kh_id' => $this->id])
            ->column();

        // nhóm mới từ form (đã chuẩn hoá ở beforeValidate)
        $new = $this->nhom_ids;

        $toAdd = array_values(array_diff($new, $old));
        $toDel = array_values(array_diff($old, $new));

        if (!empty($toDel)) {
            $db->createCommand()
               ->delete('kh_nhom_map', [
                   'kh_id' => $this->id,
                   'nhom_id' => $toDel,
               ])->execute();
        }

        if (!empty($toAdd)) {
            $rows = [];
            foreach ($toAdd as $nid) {
                $nid = (int)$nid;
                if ($nid > 0) {
                    $rows[] = [$this->id, $nid];
                }
            }
            if ($rows) {
                $db->createCommand()
                   ->batchInsert('kh_nhom_map', ['kh_id', 'nhom_id'], $rows)
                   ->execute();
            }
        }
    }

    /**
     * Ngăn xoá khi còn điều trị liên quan.
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete() === false) {
            return false;
        }

        $count = (int)$this->getDieuTris()->count();
        if ($count > 0) {
            Yii::$app->session->setFlash('warning',
                "Không thể xoá khách hàng vì còn {$count} lần điều trị. "
                . "Vui lòng xoá/di chuyển các điều trị trước."
            );
            return false; // NGĂN xoá, KHÔNG ném exception
        }

        return true;
    }
}
