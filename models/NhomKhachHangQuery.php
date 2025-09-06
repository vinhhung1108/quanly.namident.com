<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[NhomKhachHang]].
 *
 * @see NhomKhachHang
 */
class NhomKhachHangQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return NhomKhachHang[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return NhomKhachHang|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
