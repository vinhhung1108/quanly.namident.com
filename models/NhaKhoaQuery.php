<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[NhaKhoa]].
 *
 * @see NhaKhoa
 */
class NhaKhoaQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return NhaKhoa[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return NhaKhoa|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
