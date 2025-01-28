<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%logs}}`.
 */
class m250126_105448_create_logs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%logs}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'action' => $this->string(255)->notNull(),
            'details' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'ip_address' => $this->string(50),
        ]);
        $this->addForeignKey('fk-logs-user_id', 'logs', 'user_id', 'users', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-logs-user_id', 'logs');
        $this->dropTable('{{%logs}}');
    }
}
