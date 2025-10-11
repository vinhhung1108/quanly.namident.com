<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class BacSi extends ActiveRecord
{
    public static function tableName()
    {
        return 'bac_si';
    }

    public function rules()
    {
        return [
            [['ho_ten'], 'required'],
            [['nam_sinh'], 'safe'],
            [['ho_ten'], 'string', 'max' => 200],

            // NEW:
            [['luong_co_dinh'], 'integer'],
            [['ty_le_hoa_hong'], 'number'],
            [['luong_co_dinh'], 'default', 'value' => 0],
            [['ty_le_hoa_hong'], 'default', 'value' => 0],
            ['ty_le_hoa_hong', 'compare', 'compareValue' => 0, 'operator' => '>='],
            ['ty_le_hoa_hong', 'compare', 'compareValue' => 100, 'operator' => '<=', 'message' => 'Tỷ lệ tối đa 100%'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'ho_ten'          => 'Họ Tên',
            'nam_sinh'        => 'Ngày Sinh',

            // NEW:
            'luong_co_dinh'   => 'Lương cố định (đ)',
            'ty_le_hoa_hong'  => 'Tỷ lệ hoa hồng (%)',
        ];
    }

    public static function find()
    {
        return new BacSiQuery(get_called_class());
    }

    /** Quan hệ: hoa hồng theo DV (indexBy dv_id để tra nhanh) */
    public function getHoaHongDvs()
    {
        return $this->hasMany(BacSiHoaHong::class, ['bs_id' => 'id'])->indexBy('dv_id');
    }

    /** % hoa hồng áp dụng cho 1 DV: ưu tiên cấu hình riêng, fallback ty_le_hoa_hong mặc định của BS */
    public function getTyLeHoaHongForService(?int $dvId): float
    {
        if (!$dvId) return (float)$this->ty_le_hoa_hong;

        static $cache = []; // cache theo BS để tránh query lặp
        $k = (int)$this->id;
        if (!isset($cache[$k])) {
            $cache[$k] = \yii\helpers\ArrayHelper::map(
                $this->hoaHongDvs, 'dv_id', 'ty_le'
            );
        }
        return isset($cache[$k][$dvId]) ? (float)$cache[$k][$dvId] : (float)$this->ty_le_hoa_hong;
    }

    /**
     * Tính lương theo TẠM THU từng dịch vụ trong khoảng ngày.
     *
     * - Sử dụng trực tiếp ct.tam_thu (đã có cột trong dieu_tri_dich_vu).
     * - Gán công: ưu tiên ct.bs_id = bác sĩ; nếu ct.bs_id NULL và $fallbackTheoDieuTri = true thì dùng dt.bs_id;
     *   nếu $fallbackTheoTen = true, tiếp tục nhận record dt.bs (text) = tên bác sĩ khi cả ct.bs_id & dt.bs_id rỗng.
     *
     * @param string $from YYYY-MM-DD
     * @param string $to   YYYY-MM-DD (bao gồm ngày này)
     * @param bool $fallbackTheoDieuTri
     * @param bool $fallbackTheoTen
     * @return array {
     *   tong_tam_thu, luong_co_dinh, luong_kinh_doanh, tong_luong, items:[{...}]
     * }
     */
    public function tinhLuongTheoDichVu(string $from, string $to, bool $fallbackTheoDieuTri = true, bool $fallbackTheoTen = false): array
    {
        $db = Yii::$app->db;

        // Điều kiện nhận công
        $conds  = ["ct.bs_id = :bs"];
        $params = [':bs' => (int)$this->id, ':f' => $from, ':t' => $to];

        if ($fallbackTheoDieuTri) {
            $conds[] = "(ct.bs_id IS NULL AND dt.bs_id = :bs)";
        }
        if ($fallbackTheoTen && $this->ho_ten) {
            $conds[] = "(ct.bs_id IS NULL AND (dt.bs_id IS NULL OR dt.bs_id = 0) AND dt.bs = :bs_ten)";
            $params[':bs_ten'] = (string)$this->ho_ten;
        }

        // Lấy các dòng DV thuộc điều trị trong kỳ
        $sql = "
            SELECT
                ct.id              AS ct_id,
                ct.dieu_tri_id     AS dieu_tri_id,
                ct.dich_vu_id      AS dich_vu_id,
                ct.ten_dv          AS ten_dv,
                ct.so_luong        AS so_luong,
                ct.don_gia         AS don_gia,
                ct.thanh_tien      AS thanh_tien,
                ct.tam_thu         AS tam_thu_dv,
                dt.bs_id           AS dt_bs_id,
                dt.id              AS dt_id,
                dt.ngay_dieu_tri   AS ngay_dieu_tri
            FROM dieu_tri_dich_vu ct
            JOIN dieu_tri dt ON dt.id = ct.dieu_tri_id
            WHERE (" . implode(' OR ', $conds) . ")
              AND dt.ngay_dieu_tri BETWEEN :f AND :t
            ORDER BY dt.ngay_dieu_tri ASC, ct.id ASC
        ";

        $rows = $db->createCommand($sql, $params)->queryAll();

        $items        = [];
        $tongTamThu   = 0;
        $tongHoaHong  = 0;

        foreach ($rows as $r) {
            $dvId      = $r['dich_vu_id'] !== null ? (int)$r['dich_vu_id'] : null;
            $tamThuDv  = (int)($r['tam_thu_dv'] ?? 0);
            $tyLe      = $this->getTyLeHoaHongForService($dvId);
            $hoaHong   = (int)round($tamThuDv * ($tyLe / 100.0));

            $tongTamThu  += $tamThuDv;
            $tongHoaHong += $hoaHong;

            $items[] = [
                'ct_id'        => (int)$r['ct_id'],
                'dieu_tri_id'  => (int)$r['dieu_tri_id'],
                'dv_id'        => $dvId,
                'ten_dv'       => (string)$r['ten_dv'],
                'so_luong'     => (int)$r['so_luong'],
                'don_gia'      => (int)$r['don_gia'],
                'thanh_tien'   => (int)$r['thanh_tien'],
                'tam_thu_dv'   => $tamThuDv,
                'ty_le'        => (float)$tyLe,
                'hoa_hong'     => $hoaHong,
            ];
        }

        $luongCoDinh = (int)$this->luong_co_dinh;

        return [
            'tong_tam_thu'     => $tongTamThu,
            'luong_co_dinh'    => $luongCoDinh,
            'luong_kinh_doanh' => $tongHoaHong,
            'tong_luong'       => $luongCoDinh + $tongHoaHong,
            'items'            => $items,
        ];
    }

    /**
     * API cũ (giữ tương thích): trả tổng theo DV & tổng lương
     * Ở đây gán tong_tam_thu = tong_phi_dich_vu để tương thích chữ ký cũ.
     */
    public function tinhLuong(string $from, string $to, bool $fallbackTheoTen = false): array
    {
        $calc = $this->tinhLuongTheoDichVu($from, $to, true, $fallbackTheoTen);

        return [
            'tong_tam_thu'      => (int)($calc['tong_tam_thu'] ?? 0),
            'tong_phi_dich_vu'  => (int)($calc['tong_tam_thu'] ?? 0), // giữ key cũ
            'luong_co_dinh'     => (int)($calc['luong_co_dinh'] ?? 0),
            'luong_kinh_doanh'  => (int)($calc['luong_kinh_doanh'] ?? 0),
            'tong_luong'        => (int)($calc['tong_luong'] ?? 0),
        ];
    }

    /** Bảng lương tất cả BS */
    public static function bangLuongAll(string $from, string $to, bool $fallbackTheoTen = false): array
    {
        $rows = [];
        foreach (self::find()->orderBy(['ho_ten' => SORT_ASC])->all() as $bs) {
            /** @var self $bs */
            $rows[] = [
                'bs' => $bs,
                'breakdown' => $bs->tinhLuong($from, $to, $fallbackTheoTen),
            ];
        }
        return $rows;
    }

    /** Chế độ tóm tắt (không trả items) */
    public function tinhLuongTheoChiTiet(string $from, string $to): array
    {
        $calc = $this->tinhLuongTheoDichVu($from, $to, true, false);
        unset($calc['items']);
        return $calc;
    }
}
