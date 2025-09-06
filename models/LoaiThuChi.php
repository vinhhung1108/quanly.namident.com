<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "loai_thu_chi".
 *
 * @property int $id
 * @property string $loai_thu_chi
 * @property string $ghi_chu
 */
class LoaiThuChi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loai_thu_chi';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['loai_thu_chi'], 'required'],
            [['loai_thu_chi'], 'string'],
            [['ghi_chu'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'loai_thu_chi' => 'Loại thu chi',
            'ghi_chu' => 'Ghi chú',
        ];
    }

    /**
     * {@inheritdoc}
     * @return LoaiThuChiQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LoaiThuChiQuery(get_called_class());
    }
}
