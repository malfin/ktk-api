<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m250126_105106_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(100)->notNull(),
            'last_name' => $this->string(100)->notNull(),
            'patronymic' => $this->string(100)->null(),
            'email' => $this->string(150)->notNull()->unique(),
            'password_hash' => $this->string(255)->notNull(),
            'role_id' => $this->integer()->notNull(),
            'birth_date' => $this->date(),
            'access_token' => $this->string(255),
            'status' => $this->boolean()->defaultValue(true),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey('fk-users-role_id', 'users', 'role_id', 'roles', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-users-role_id', 'users');
        $this->dropTable('{{%users}}');
    }
}
