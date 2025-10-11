<?php

use yii\db\Migration;

/**
 * Class m250927_122851_add_salary_columns_to_bac_si
 */
class m250927_122851_add_salary_columns_to_bac_si extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('bac_si', 'luong_co_dinh', $this->integer()->notNull()->defaultValue(0)->after('nam_sinh'));
        $this->addColumn('bac_si', 'ty_le_hoa_hong', $this->decimal(5,2)->notNull()->defaultValue(0)->after('luong_co_dinh'));
    }

    public function safeDown()
    {
        $this->dropColumn('bac_si', 'ty_le_hoa_hong');
        $this->dropColumn('bac_si', 'luong_co_dinh');
    }

    /*
    */
}
