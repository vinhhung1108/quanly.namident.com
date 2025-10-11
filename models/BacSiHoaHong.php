<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class BacSiHoaHong extends ActiveRecord
{
    public static function tableName(): string { return 'bac_si_hoa_hong'; }

    public function behaviors(): array { return [ TimestampBehavior::class ]; }

    public function rules(): array
    {
        return [
            [['bs_id','dv_id'], 'required'],
            [['bs_id','dv_id'], 'integer'],
            [['ty_le'], 'number', 'min'=>0, 'max'=>100],
            [['ty_le'], 'default', 'value'=>0],
            [['bs_id','dv_id'], 'unique', 'targetAttribute'=>['bs_id','dv_id'],
              'message'=>'Bác sĩ đã có cấu hình hoa hồng cho dịch vụ này'],
            ['bs_id', 'exist', 'skipOnError'=>true,
                'targetClass'=>BacSi::class, 'targetAttribute'=>['bs_id'=>'id']],
            ['dv_id', 'exist', 'skipOnError'=>true,
                'targetClass'=>DichVu::class, 'targetAttribute'=>['dv_id'=>'id']],
        ];
    }

    public function attributeLabels(): array
    {
      return [
        'bs_id' => 'Bác sĩ',
        'dv_id' => 'Dịch vụ',
        'ty_le' => 'Tỷ lệ hoa hồng',
      ];
    }

    public function getBacSi(){ return $this->hasOne(BacSi::class, ['id'=>'bs_id']); }
    public function getDichVu(){ return $this->hasOne(DichVu::class, ['id'=>'dv_id']); }
}
