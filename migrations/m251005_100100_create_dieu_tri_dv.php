<?php
use yii\db\Migration;

class m251005_100100_create_dieu_tri_dv extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%dieu_tri_dv}}', [
            'id'           => $this->primaryKey(),
            'dieu_tri_id'  => $this->integer()->notNull(),
            'dich_vu_id'   => $this->integer()->null(),         // null = ad-hoc
            'ten_dv'       => $this->string(255)->notNull(),     // tên hiển thị (copy từ danh mục hoặc nhập tay)
            'don_gia'      => $this->integer()->notNull()->defaultValue(0),
            'so_luong'     => $this->integer()->notNull()->defaultValue(1),
            'rang_so'      => $this->string(100)->null(),        // VD: "16,26" hoặc "11,12,13"
            'thanh_tien'   => $this->integer()->notNull()->defaultValue(0), // don_gia*so_luong (lưu để truy vấn nhanh)
            'created_at'   => $this->integer(),
            'updated_at'   => $this->integer(),
        ]);

        $this->createIndex('idx_dtdv_dieu_tri_id', '{{%dieu_tri_dv}}', 'dieu_tri_id');
        $this->addForeignKey('fk_dtdv_dieu_tri', '{{%dieu_tri_dv}}', 'dieu_tri_id', '{{%dieu_tri}}', 'id', 'CASCADE', 'CASCADE');
        // FK danh mục (cho phép null)
        $this->addForeignKey('fk_dtdv_dich_vu', '{{%dieu_tri_dv}}', 'dich_vu_id', '{{%dich_vu}}', 'id', 'SET NULL', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_dtdv_dich_vu', '{{%dieu_tri_dv}}');
        $this->dropForeignKey('fk_dtdv_dieu_tri', '{{%dieu_tri_dv}}');
        $this->dropTable('{{%dieu_tri_dv}}');
    }
}
