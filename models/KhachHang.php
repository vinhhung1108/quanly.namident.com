<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "khach_hang".
 *
 * @property int $id
 * @property string $ho_ten
 * @property string $ma_ho_so
 * @property string $sdt
 * @property string $ngay_sinh
 * @property string $dieu_tri
 * @property string $ngay_dieu_tri
 * @property string $ngay_hen
 * @property int $tong_phi
 * @property int $tam_thu
 * @property int $con_lai
 * @property string $bs_dieu_tri
 * @property string $gioi_thieu
 * @property string $hinh_anh
 *
 * @property DieuTri[] $dieuTris
 */
class KhachHang extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
	 
	public $upload;
	public $textname;
	public $videos;
	public $texturl;
	
    public static function tableName()
    {
        return 'khach_hang';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
       return [
            [['ho_ten'], 'required', 'message'=>'Thiếu nội dung phần này!'],      
			[['ngay_hen', 'ngay_sinh', 'ngay_dieu_tri'], 'safe'],
            [['dieu_tri', 'videos', 'video', 'gioi_tinh','dia_chi','chan_doan','tien_su_benh','ghi_chu','nghe_nghiep'], 'string'],
            [['tong_phi', 'tam_thu', 'con_lai', 'tam_thu_last'], 'integer','message'=>'Không đúng định dạng'],
            [['ho_ten', 'bs_dieu_tri','ma_the'], 'string', 'max' => 50],
			[['ma_the'],'unique'],
            [['gioi_thieu'], 'string', 'max' => 25],
            [['sdt'], 'string', 'max' => 15],
			[['upload'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, heic, jpeg, gif', 'maxFiles' => 6],
			[['gio', 'gio_hen', 'noi_dung_hen'],'safe'],
            [['nhom_kh'],'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Mã KH',
            'ho_ten' => 'Họ tên khách hàng',
            'sdt' => 'Số điện thoại',
            'ngay_sinh' => 'Ngày sinh',
            'dieu_tri' => 'Nội dung điều trị',
            'ngay_hen' => 'Ngày hẹn',
            'tong_phi' => 'Tổng phí',
            'tam_thu' => 'Tạm thu',
            'con_lai' => 'Còn lại',
            'bs_dieu_tri' => 'Bác sĩ điều trị',
            'gioi_thieu' => 'Giới thiệu',
			'upload' => 'Thêm hình ảnh',
			'hinh_anh'=>'Hình ảnh',
			'videos'=>'Link Youtube',
			'video'=>'Video',
			'gioi_tinh'=>'Giới tính',
			'ngay_dieu_tri'=>'Ngày điều trị',
			'dia_chi'=>'Địa chỉ',
			'chan_doan'=>'Chẩn đoán',
			'tien_su_benh'=>'Tiền sử bệnh',
			'ghi_chu'=>'Ghi chú',
			'nghe_nghiep'=>'Nghề nghiệp',
			'ma_the' =>'Mã BN',
            'nhom_kh' => 'Nhóm',
            'gio_hen'=>'Giờ hẹn',
            'noi_dung_hen'=>'Nội dung hẹn',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDieuTris()
    {
        return $this->hasMany(DieuTri::className(), ['id_kh' => 'id'])->orderBy(['ngay_dieu_tri'=> SORT_DESC]);
    }

    public function getNhomKhachHang()
    {
        return $this->hasOne(NhomKhachHang::className(), ['id' => 'nhom_kh']);
    }

    /**
     * {@inheritdoc}
     * @return KhachHangQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new KhachHangQuery(get_called_class());
    }
	
	public function getimageurl()
	{
		$path=[];
		$textname = explode(":",$this->hinh_anh,10);
		foreach ($textname as $url){
			$path[] = $url;
		}
		return $path;
	}
	public function getvideo()
	{	
		$path=[];
		$tempurl ='';
		$texturl = explode("__",$this->video,10);
		foreach ($texturl as $url){
			$tempurl = strstr($url, 'be/');
			$tempurl = strstr($tempurl, '/');		
			$path[] = $tempurl;
		}
		return $path;
	}	
	
}
