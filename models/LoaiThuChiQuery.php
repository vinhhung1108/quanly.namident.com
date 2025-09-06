<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[LoaiThuChi]].
 *
 * @see LoaiThuChi
 */
class LoaiThuChiQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return LoaiThuChi[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return LoaiThuChi|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
