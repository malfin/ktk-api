<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_sessions}}`.
 */
class m250126_105309_create_user_sessions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_sessions}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'session_id' => $this->integer()->notNull(),
            'status' => "ENUM('pending', 'approved', 'canceled') DEFAULT 'pending'",
            'registered_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->addForeignKey('fk-user_sessions-user_id', 'user_sessions', 'user_id', 'users', 'id', 'CASCADE');
        $this->addForeignKey('fk-user_sessions-session_id', 'user_sessions', 'session_id', 'sessions', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-user_sessions-user_id', 'user_sessions');
        $this->dropForeignKey('fk-user_sessions-session_id', 'user_sessions');
        $this->dropTable('{{%user_sessions}}');
    }
}
