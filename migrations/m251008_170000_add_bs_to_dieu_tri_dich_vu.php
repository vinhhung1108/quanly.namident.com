<?php
use yii\db\Migration;

class m251008_170000_add_bs_to_dieu_tri_dich_vu extends Migration
{
    public function safeUp()
    {
        $table = '{{%dieu_tri_dich_vu}}';
        $this->addColumn($table, 'bs_id', $this->integer()->null()->after('dich_vu_id'));
        $this->createIndex('idx_dt_dv_bs', $table, 'bs_id');
        $this->addForeignKey(
            'fk_dt_dv_bs',
            $table, 'bs_id',
            '{{%bac_si}}', 'id',
            'SET NULL', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $table = '{{%dieu_tri_dich_vu}}';
        $this->dropForeignKey('fk_dt_dv_bs', $table);
        $this->dropIndex('idx_dt_dv_bs', $table);
        $this->dropColumn($table, 'bs_id');
    }
}
