<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[KhachHangSearch]].
 *
 * @see KhachHangSearch
 */
class KhachHangSearchQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return KhachHangSearch[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return KhachHangSearch|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
