<?php

namespace app\models;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "nha_khoa".
 *
 * @property int $id
 * @property string $ten_nha_khoa
 * @property string $dia_chi
 * @property string $so_dien_thoai
 * @property string $ma_so_thue
 * @property int $created_at
 * @property int $updated_at
 */
class NhaKhoa extends \yii\db\ActiveRecord
{

  public $logoFile;

  public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nha_khoa';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ten_nha_khoa', 'dia_chi', 'so_dien_thoai'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['ten_nha_khoa', 'so_dien_thoai'], 'string', 'max' => 100],
            [['dia_chi'], 'string', 'max' => 255],
            [['ma_so_thue'], 'string', 'max' => 50],
            [['logo'], 'string', 'max' => 255],

            // file upload: chỉ chấp nhận raster để tránh rủi ro với SVG
            [['logoFile'], 'file',
                'extensions' => ['png','jpg','jpeg','webp','gif'],
                'checkExtensionByMimeType' => true,
                'maxSize' => 5 * 1024 * 1024, // 5MB
                'skipOnEmpty' => true
            ],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ten_nha_khoa' => 'Tên nha khoa',
            'dia_chi' => 'Địa chỉ',
            'so_dien_thoai' => 'Số điện thoại',
            'ma_so_thue' => 'Mã số thuế',
            'logo'=> 'Logo',
            'created_at' => 'Ngày tạo',
            'updated_at' => 'Ngày cập nhật',
        ];
    }

    /**
     * {@inheritdoc}
     * @return NhaKhoaQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NhaKhoaQuery(get_called_class());
    }

    public function getLogoUrl(): ?string
    {
        return $this->logo ? (\Yii::$app->request->baseUrl . $this->logo) : null;
    }
    
}
