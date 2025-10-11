<?php

namespace app\models;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

use Yii;

/**
 * This is the model class for table "dieu_tri".
 *
 * @property int $id
 * @property int $lan_dieu_tri
 * @property string $ngay_dieu_tri
 * @property string $noi_dung
 * @property int $phi
 * @property int $tam_thu
 * @property int $id_kh
 *
 * @property KhachHang $kh
 */
class DieuTri extends \yii\db\ActiveRecord
{
	public $phi_t;
	public $tam_thu_t;
    
    /**
     * {@inheritdoc}
     */

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    public static function tableName()
    {
        return 'dieu_tri';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
           /* [['noi_dung'], 'required', 'message'=>'Nội dung bắt buộc!'],*/
            [['lan_dieu_tri'], 'integer', 'message'=>'sai'],
			[['phi_t', 'tam_thu_t','hinh_thuc_thanh_toan'], 'safe'],
			[['phi', 'tam_thu'], 'integer'],
            [['ngay_dieu_tri'], 'safe'],
            [['ngay_dieu_tri'],'default','value' => date("Y-m-d")],
			[['ngay_dieu_tri'], 'required'],
            [['noi_dung','bs'], 'string'],
            [['id_kh'], 'exist', 'skipOnError' => true, 'targetClass' => KhachHang::className(), 'targetAttribute' => ['id_kh' => 'id']],
			[['gio'],'safe'],
			
        ];
    }

    /**
     * {@inheritdoc}
     */
    private function demojibake(string $s): string
{
    // dấu hiệu mojibake phổ biến (UTF-8 bị đọc Latin-1 rồi re-encode)
    if (strpos($s, 'Ã') !== false || strpos($s, 'Â') !== false) {
        // thử “giải cứu” theo ISO-8859-1 trước
        $fixed = mb_convert_encoding($s, 'UTF-8', 'ISO-8859-1');
        // fallback nếu nguồn là Windows-1252
        if (strpos($fixed, 'Ã') !== false || strpos($fixed, 'Â') !== false) {
            $fixed = mb_convert_encoding($s, 'UTF-8', 'Windows-1252');
        }
        return $fixed;
    }
    return $s;
}
    public function attributeLabels()
{
    $labels = [
        'id' => 'ID',
        'lan_dieu_tri' => 'Lần điều trị',
        'ngay_dieu_tri' => 'Ngày điều trị',
        'noi_dung' => 'Nội dung',
        'phi' => 'Phí Điều Trị',
        'tam_thu' => 'Tạm Thu',
        'id_kh' => 'Mã khách hàng',
        'bs' => 'Bác sĩ',
        'phi_t' => 'Phí điều trị',
        'tam_thu_t' => 'Tạm thu',
        'hinh_thuc_thanh_toan' => 'Hình thức TT',
        'gio' => 'Giờ',
    ];
    foreach ($labels as $k => $v) {
        $labels[$k] = $this->demojibake($v);
    }
    return $labels;
}

    /**
     * @return \yii\db\ActiveQuery
     */
 
	
	public function ten_kh()
	{
		$khach_hang = KhachHang::findOne($this->id_kh);
		return $khach_hang->ho_ten;
	}
	public function con_lai()
	{
		$khach_hang = KhachHang::findOne($this->id_kh);
		return $khach_hang->con_lai;
	}
    /**
     * {@inheritdoc}
     * @return KhachHangQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new KhachHangQuery(get_called_class());
    }
	
	/* Dieu Tri     */
    public static function createMultiple($modelClass, $multipleModels = [])
    {
        $model    = new $modelClass;
		$model->gio = time();
        $formName = $model->formName();
        $post     = Yii::$app->request->post($formName);
        $models   = [];

        if (! empty($multipleModels)) {
            $keys = array_keys(ArrayHelper::map($multipleModels, 'id', 'id'));
            $multipleModels = array_combine($keys, $multipleModels);
        }

        if ($post && is_array($post)) {
            foreach ($post as $i => $item) {
                if (isset($item['id']) && !empty($item['id']) && isset($multipleModels[$item['id']])) {
                    $models[] = $multipleModels[$item['id']];
                } else {
                    $models[] = new $modelClass;
                }
            }
        }

        unset($model, $formName, $post);

        return $models;
    }

   public function getImages()
    {
        return $this->hasMany(\app\models\DieuTriImage::class, ['dieu_tri_id' => 'id'])->orderBy(['id'=>SORT_DESC]);
    }
    public function getKhach_hang()
    {
        return $this->hasOne(\app\models\KhachHang::class, ['id' => 'id_kh']);
    }

}
