<?php

use yii\db\Migration;

class m251020_150000_create_bac_si_hoa_hong_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('bac_si_hoa_hong', [
            'id'         => $this->primaryKey(),
            'bs_id'      => $this->integer()->notNull(),
            'dv_id'      => $this->integer()->notNull(),
            'ty_le'      => $this->decimal(5, 2)->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx_bac_si_hoa_hong_bs_id', 'bac_si_hoa_hong', 'bs_id');
        $this->createIndex('idx_bac_si_hoa_hong_dv_id', 'bac_si_hoa_hong', 'dv_id');
        $this->createIndex('ux_bac_si_hoa_hong_bs_dv', 'bac_si_hoa_hong', ['bs_id', 'dv_id'], true);

        $this->addForeignKey(
            'fk_bac_si_hoa_hong_bs',
            'bac_si_hoa_hong',
            'bs_id',
            'bac_si',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_bac_si_hoa_hong_dv',
            'bac_si_hoa_hong',
            'dv_id',
            'dich_vu',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_bac_si_hoa_hong_dv', 'bac_si_hoa_hong');
        $this->dropForeignKey('fk_bac_si_hoa_hong_bs', 'bac_si_hoa_hong');
        $this->dropIndex('ux_bac_si_hoa_hong_bs_dv', 'bac_si_hoa_hong');
        $this->dropIndex('idx_bac_si_hoa_hong_dv_id', 'bac_si_hoa_hong');
        $this->dropIndex('idx_bac_si_hoa_hong_bs_id', 'bac_si_hoa_hong');
        $this->dropTable('bac_si_hoa_hong');
    }
}
