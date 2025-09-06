<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[DieuTri]].
 *
 * @see DieuTri
 */
class DieuTriQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return DieuTri[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return DieuTri|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
