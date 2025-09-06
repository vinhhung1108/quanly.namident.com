<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[BacSi]].
 *
 * @see BacSi
 */
class BacSiQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return BacSi[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return BacSi|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
