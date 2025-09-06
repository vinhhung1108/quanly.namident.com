<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[ThuChi]].
 *
 * @see ThuChi
 */
class ThuChiQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ThuChi[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ThuChi|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
