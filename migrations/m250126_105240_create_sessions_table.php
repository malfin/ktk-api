<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sessions}}`.
 */
class m250126_105240_create_sessions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sessions}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'type_id' => $this->integer()->notNull(),
            'start_date' => $this->datetime(),
            'end_date' => $this->datetime(),
            'max_participants' => $this->integer()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
        $this->addForeignKey('fk-sessions-type_id', 'sessions', 'type_id', 'session_types', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-sessions-type_id', 'sessions');
        $this->dropTable('{{%sessions}}');
    }
}
