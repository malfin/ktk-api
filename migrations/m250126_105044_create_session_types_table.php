<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%session_types}}`.
 */
class m250126_105044_create_session_types_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%session_types}}', [
            'id' => $this->primaryKey(),
            'name' => "ENUM('Курсы', 'Мастер-класс', 'Индвидуальное занятие') NOT NULL UNIQUE",
        ]);
        // Вставка данных по умолчанию
        $this->batchInsert('session_types', ['name'], [
            ['Курсы'],
            ['Мастер-класс'],
            ['Индвидуальное занятие'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%session_types}}');
    }
}
