<?php
use yii\db\Migration;

class m251005_100000_create_dich_vu extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%dich_vu}}', [
            'id'       => $this->primaryKey(),
            'ten'      => $this->string(255)->notNull(),
            'don_gia'  => $this->integer()->notNull()->defaultValue(0),
            'active'   => $this->boolean()->notNull()->defaultValue(1),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
        $this->createIndex('idx_dich_vu_active', '{{%dich_vu}}', 'active');

        // seed nhẹ (tùy bạn)
        $this->batchInsert('{{%dich_vu}}', ['ten','don_gia','active'], [
            ['Trám răng',         300000, 1],
            ['Nhổ răng',          500000, 1],
            ['Cạo vôi răng',      400000, 1],
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%dich_vu}}');
    }
}
