<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%files}}`.
 */
class m250126_105415_create_files_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%files}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'session_id' => $this->integer()->notNull(),
            'file_type' => "ENUM('assignment', 'submission') NOT NULL",
            'file_path' => $this->string(255)->notNull(),
            'uploaded_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'status' => "ENUM('pending', 'reviewed') DEFAULT 'pending'",
        ]);
        $this->addForeignKey('fk-files-user_id', 'files', 'user_id', 'users', 'id', 'CASCADE');
        $this->addForeignKey('fk-files-session_id', 'files', 'session_id', 'sessions', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-files-user_id', 'files');
        $this->dropForeignKey('fk-files-session_id', 'files');
        $this->dropTable('{{%files}}');
    }
}
