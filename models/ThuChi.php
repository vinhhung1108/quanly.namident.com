<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "thu_chi".
 *
 * @property int $id
 * @property string $thu_chi
 * @property string $ngay_thu
 * @property int $so_tien
 * @property string $so_tien_t
 * @property string $noi_dung
 */
class ThuChi extends \yii\db\ActiveRecord
{
	//public $so_tien_t;
	public $upload;
	public $textname;
	public $loaithuchi=[];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'thu_chi';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['thu_chi'], 'required','message'=>'Chọn Thu hoặc Chi'],
            [['ngay_thu'], 'safe'],
            [['so_tien','nguoi_thu_chi_id'], 'integer'],
            [['noi_dung','loai'], 'string'],
            [['thu_chi', 'so_tien_t'], 'string', 'max' => 200],
			[['upload'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, heic, jpeg, gif', 'maxFiles' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'thu_chi' => 'Thu/Chi',
            'ngay_thu' => 'Ngày Thu/Chi',
            'so_tien' => 'Số Tiền',
            'so_tien_t' => 'Số Tiền',
            'noi_dung' => 'Nội dung',
			'loai' => 'Loại Thu/Chi',
      'nguoi_thu_chi_id'=>'Người thu/chi',
			'hinh_anh' => 'Hình ảnh',
			'upload' => 'Thêm hình ảnh',
      'nguoi_thu_chi_id' => 'Người thu/chi'
        ];
    }

    /**
     * {@inheritdoc}
     * @return ThuChiQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ThuChiQuery(get_called_class());
    }
	//xử lý hình ảnh
	public function getimageurl()
	{
		$path=[];		
		$textname = explode(":",$this->hinh_anh,20);
		foreach ($textname as $url){
			$path[] = $url;
		}
		return $path;
	}/*end xử lý hình ảnh*/

    public function getLoaiThuChi()
    {
        return $this->hasOne(LoaiThuChi::className(), ['id' => 'loai']);
    }

    public static function getTotal($provider, $fieldName)
    {
        $total = 0;

        foreach ($provider as $item) {
            $total += $item[$fieldName];
        }

        return Yii::$app->formatter->asInteger($total);
    }

    public function getNguoiThuChi()
    {
        return $this->hasOne(NguoiThuChi::class, ['id' => 'nguoi_thu_chi_id']);
    }
    
    public function getNguoiThuChiName()
    {
        return $this->nguoiThuChi ? $this->nguoiThuChi->ho_ten : '';
    }

    public function beforeAction($action)
    {
        if ($action->id === 'create' && Yii::$app->request->isGet) {
            $res = Yii::$app->response;
            $res->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $res->headers->add('Cache-Control', 'post-check=0, pre-check=0');
            $res->headers->set('Pragma', 'no-cache');
            $res->headers->set('Expires', '0');
        }
        return parent::beforeAction($action);
    }
}
