<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bac_si".
 *
 * @property int $id
 * @property string $ho_ten
 * @property string $nam_sinh
 */
class BacSi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bac_si';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ho_ten'], 'required'],
            [['nam_sinh'], 'safe'],
            [['ho_ten'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ho_ten' => 'Họ Tên',
            'nam_sinh' => 'Ngày Sinh',
        ];
    }

    /**
     * {@inheritdoc}
     * @return BacSiQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BacSiQuery(get_called_class());
    }
}
