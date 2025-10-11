<?php

use yii\db\Migration;

class m251010_090000_add_tam_thu_to_dieu_tri_dich_vu extends Migration
{
    public function safeUp()
    {
        $this->addColumn(
            '{{%dieu_tri_dich_vu}}',
            'tam_thu',
            $this->integer()->notNull()->defaultValue(0)
        );
    }

    public function safeDown()
    {
        $this->dropColumn('{{%dieu_tri_dich_vu}}', 'tam_thu');
    }
}
