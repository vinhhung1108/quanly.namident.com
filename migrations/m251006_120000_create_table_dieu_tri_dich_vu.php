<?php
use yii\db\Migration;

class m251006_120000_create_table_dieu_tri_dich_vu extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%dieu_tri_dich_vu}}', [
            'id'           => $this->primaryKey(),
            'dieu_tri_id'  => $this->integer()->notNull(),
            'dich_vu_id'   => $this->integer()->null(),             // null nếu là “nội dung khác”
            'ten_dv'       => $this->string(255)->null(),           // tên hiển thị (đổ từ dropdown hoặc ad-hoc)
            'don_gia'      => $this->integer()->notNull()->defaultValue(0),
            'so_luong'     => $this->integer()->notNull()->defaultValue(1),
            'rang_so'      => $this->string(255)->null(),           // CSV: "11,12,13"
            'thanh_tien'   => $this->integer()->notNull()->defaultValue(0),
            'created_at'   => $this->integer()->null(),
            'updated_at'   => $this->integer()->null(),
        ]);

        $this->createIndex('idx_dv_dieu_tri_id', '{{%dieu_tri_dich_vu}}', 'dieu_tri_id');
        // (tuỳ chọn) FK
        // $this->addForeignKey('fk_dv_dieu_tri', '{{%dieu_tri_dich_vu}}', 'dieu_tri_id', '{{%dieu_tri}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        // $this->dropForeignKey('fk_dv_dieu_tri', '{{%dieu_tri_dich_vu}}');
        $this->dropTable('{{%dieu_tri_dich_vu}}');
    }
}
