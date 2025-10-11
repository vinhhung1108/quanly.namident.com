<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class DichVu extends ActiveRecord
{
    public static function tableName(): string { return 'dich_vu'; }

    public function behaviors(): array
    {
        return [ TimestampBehavior::class ];
    }

    public function rules(): array
    {
        return [
            [['ten'], 'required'],
            [['ten'], 'string', 'max' => 255],
            [['don_gia'], 'integer'],
            [['active'], 'boolean'],
            [['don_gia'], 'default', 'value' => 0],
            [['active'], 'default', 'value' => 1],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'         => 'ID',
            'ten'        => 'Tên dịch vụ',
            'don_gia'    => 'Đơn giá',
            'active'     => 'Kích hoạt',
            'created_at' => 'Tạo lúc',
            'updated_at' => 'Cập nhật',
        ];
    }

    /** Cho phép nhập “1,000,000” -> 1000000 */
    public function beforeValidate()
    {
        if ($this->don_gia !== null && !is_int($this->don_gia)) {
            $digits = preg_replace('/\D+/', '', (string)$this->don_gia);
            $this->don_gia = $digits === '' ? 0 : (int)$digits;
        }
        return parent::beforeValidate();
    }
}
