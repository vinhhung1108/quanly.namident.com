<?php
namespace app\models;

use yii\db\ActiveRecord;

class MyDieuTriDv extends ActiveRecord
{
    private static ?bool $hasTamThuColumn = null;

    public static function tableName(): string { return 'dieu_tri_dich_vu'; }

    public static function hasTamThuColumn(): bool
    {
        if (self::$hasTamThuColumn === null) {
            $schema = \Yii::$app->db->schema->getTableSchema(self::tableName(), true);
            self::$hasTamThuColumn = $schema && $schema->getColumn('tam_thu') !== null;
        }
        return self::$hasTamThuColumn;
    }

    public function rules(): array
    {
        return [
            [['dieu_tri_id'], 'required'],
            [['dieu_tri_id','dich_vu_id','don_gia','so_luong','thanh_tien','bs_id','tam_thu'], 'integer'], // + bs_id
            [['ten_dv','rang_so'], 'string', 'max' => 255],
            [['don_gia','so_luong','thanh_tien','tam_thu'], 'default', 'value' => 0],
            [['so_luong'], 'default', 'value' => 1],
            // (khuyến nghị) validate tồn tại
            ['bs_id', 'exist', 'skipOnError' => true,
                'targetClass' => \app\models\BacSi::class, 'targetAttribute' => ['bs_id' => 'id']],
        ];
    }

    public function getDieuTri() { return $this->hasOne(MyDieuTri::class, ['id' => 'dieu_tri_id']); }
    public function getDichVu()  { return $this->hasOne(\app\models\DichVu::class, ['id' => 'dich_vu_id']); }
    public function getBacSi()   { return $this->hasOne(\app\models\BacSi::class, ['id' => 'bs_id']); } // NEW

    /**
     * Đồng bộ các dòng dịch vụ từ POST về DB cho một điều trị
     */
    public static function syncFromPost(int $dieuTriId, array $post): int
    {
        $ids   = $post['dv_id']       ?? [];
        $tens  = $post['dv_ten']      ?? [];
        $gias  = $post['dv_don_gia']  ?? [];
        $qtys  = $post['dv_qty']      ?? [];
        $rangs = $post['dv_rang']     ?? []; // mảng con theo index
        $bss   = $post['dv_bs']       ?? []; // NEW: bác sĩ từng dòng
        $tamThuArr = $post['dv_tam_thu'] ?? [];
        $hasTamThu = self::hasTamThuColumn();

        self::deleteAll(['dieu_tri_id' => $dieuTriId]);

        $total = 0;
        $totalTamThu = 0;
        $count = max(count($gias), count($ids), count($tens), count($bss), count($tamThuArr));

        for ($i = 0; $i < $count; $i++) {
            $dvId = $ids[$i]  ?? null;
            $ten  = trim((string)($tens[$i] ?? ''));
            $gia  = (int)preg_replace('/\D+/', '', (string)($gias[$i] ?? 0));
            $qty  = (int)($qtys[$i] ?? 1);
            if ($qty < 1) $qty = 1;

            // bỏ dòng trống
            $tamThuRaw = $tamThuArr[$i] ?? null;
            $tamThu = $hasTamThu ? (int)preg_replace('/\D+/', '', (string)($tamThuRaw ?? 0)) : 0;

            if (!$dvId && $ten === '' && $gia === 0 && $tamThu === 0) continue;

            if ($ten === '' && $dvId) {
                $dv = \app\models\DichVu::findOne((int)$dvId);
                $ten = $dv ? $dv->ten : '';
            }

            $rangList = '';
            if (isset($rangs[$i]) && is_array($rangs[$i]) && !empty($rangs[$i])) {
                $rangList = implode(',', array_map('strval', $rangs[$i]));
            }

            $bsId = isset($bss[$i]) && $bss[$i] !== '' ? (int)$bss[$i] : null; // NEW

            $row = new self();
            $row->dieu_tri_id = $dieuTriId;
            $row->dich_vu_id  = $dvId ?: null;
            $row->ten_dv      = $ten ?: null;
            $row->don_gia     = $gia;
            $row->so_luong    = $qty;
            $row->rang_so     = $rangList ?: null;
            $row->bs_id       = $bsId;                      // NEW
            $row->thanh_tien  = $gia * $qty;
            if ($hasTamThu) {
                $row->tam_thu = $tamThu;
            }
            $row->save(false);

            $total += $row->thanh_tien;
            if ($hasTamThu) {
                $totalTamThu += $row->tam_thu;
            }
        }

        if (($m = MyDieuTri::findOne($dieuTriId))) {
            $m->phi = $total;
            $attrs = ['phi'];
            if ($hasTamThu) {
                $m->tam_thu = $totalTamThu;
                $attrs[] = 'tam_thu';
            }
            $m->save(false, $attrs);
        }

        return $total;
    }
}
