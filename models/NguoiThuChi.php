<?php

namespace app\models;
use yii\behaviors\TimestampBehavior;

use Yii;

/**
 * This is the model class for table "nguoi_thu_chi".
 *
 * @property int $id
 * @property string $ho_ten
 * @property string $username
 * @property string $dien_thoai
 * @property string $ghi_chu
 * @property int $created_at
 * @property int $updated_at
 *
 * @property ThuChi[] $thuChis
 */
class NguoiThuChi extends \yii\db\ActiveRecord
{

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
        return 'nguoi_thu_chi';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ho_ten'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['ho_ten'], 'string', 'max' => 100],
            [['username'], 'string', 'max' => 64],
            [['dien_thoai'], 'string', 'max' => 20],
            [['ghi_chu'], 'string', 'max' => 255],
            [['username'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ho_ten' => 'Họ tên',
            'username' => 'Username',
            'dien_thoai' => 'Số điện thoại',
            'ghi_chu' => 'Ghi chú',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getThuChis()
    {
        return $this->hasMany(ThuChi::className(), ['nguoi_thu_chi_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return NguoiThuChiQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NguoiThuChiQuery(get_called_class());
    }
}
