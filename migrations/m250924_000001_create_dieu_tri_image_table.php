<?php

use yii\db\Migration;

class m250924_000001_create_dieu_tri_image_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%dieu_tri_image}}', [
            'id' => $this->primaryKey(),
            'dieu_tri_id' => $this->integer()->notNull(),
            'file_name' => $this->string(255)->notNull(),
            'file_path' => $this->string(500)->notNull(), // /uploads/khach-hang/{kh}/{dt}/{file}
            'file_size' => $this->integer()->null(),
            'mime_type' => $this->string(100)->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx_dieu_tri_image_dieu_tri_id', '{{%dieu_tri_image}}', 'dieu_tri_id');
        $this->addForeignKey(
            'fk_dieu_tri_image_dieu_tri',
            '{{%dieu_tri_image}}',
            'dieu_tri_id',
            '{{%dieu_tri}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_dieu_tri_image_dieu_tri', '{{%dieu_tri_image}}');
        $this->dropTable('{{%dieu_tri_image}}');
    }
}
