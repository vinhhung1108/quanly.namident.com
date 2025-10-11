<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "nhom_khach_hang".
 *
 * @property int $id
 * @property string $ten_nhom
 * @property string $ghi_chu
 */
class NhomKhachHang extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nhom_khach_hang';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ten_nhom'], 'required'],
            [['ten_nhom'], 'string'],
            [['ghi_chu'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ten_nhom' => 'Tên Nhóm',
            'ghi_chu' => 'Ghi Chú',
        ];
    }

    /**
     * {@inheritdoc}
     * @return NhomKhachHangQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NhomKhachHangQuery(get_called_class());
    }

    public function getKhachHangs()
    {
        return $this->hasMany(MyKhachHang::class, ['id' => 'kh_id'])
            ->viaTable('kh_nhom_map', ['nhom_id' => 'id']);
    }
}
