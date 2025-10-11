<?php
namespace app\models;

use yii\base\Model;

class AppointmentForm extends Model
{
    public $kh_id;
    public $ngay_hen;
    public $gio_hen;
    public $bs_dieu_tri;
    public $noi_dung_hen;

    public function rules()
    {
        return [
            [['kh_id'], 'required'],
            [['kh_id'], 'integer'],

            [['ngay_hen'], 'required', 'message' => 'Vui lòng chọn ngày hẹn'],
            [['ngay_hen', 'gio_hen'], 'safe'],

            [['bs_dieu_tri'], 'string', 'max' => 50],
            [['noi_dung_hen'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'ngay_hen'     => 'Ngày hẹn',
            'gio_hen'      => 'Giờ hẹn',
            'bs_dieu_tri'  => 'Bác sĩ',
            'noi_dung_hen' => 'Nội dung hẹn',
        ];
    }

    /** Nạp dữ liệu sẵn từ model KH (prefill) */
    public function loadFromKhachHang(MyKhachHang $kh): void
    {
        $this->kh_id        = $kh->id;
        $this->ngay_hen     = $kh->ngay_hen;
        $this->gio_hen      = $kh->gio_hen;
        $this->bs_dieu_tri  = $kh->bs_dieu_tri;
        $this->noi_dung_hen = $kh->noi_dung_hen;
    }

    /** Lưu dữ liệu hẹn vào bảng khach_hang */
    public function save(): bool
    {
        if (!$this->validate()) return false;

        $kh = MyKhachHang::findOne((int)$this->kh_id);
        if (!$kh) {
            $this->addError('kh_id', 'Không tìm thấy khách hàng.');
            return false;
        }

        $kh->ngay_hen     = $this->ngay_hen ?: null;
        $kh->gio_hen      = $this->gio_hen ?: null;
        $kh->bs_dieu_tri  = $this->bs_dieu_tri ?: null;
        $kh->noi_dung_hen = $this->noi_dung_hen ?: null;

        return $kh->save(false);
    }
}
