<?php

use yii\db\Migration;

/**
 * Class m181002_205551_create_static_files
 */
class m181002_205551_create_static_files extends Migration {

    public function up() {
        $this->createTable('static_files', [
            'id'            => $this->primaryKey(),
            'collection_id' => $this->string()->notNull(),
            'file_id' => $this->string()->notNull(),
            'coords'     => $this->json(),
            'url'     => $this->string()->notNull(),
            'user_id'       => $this->integer()->notNull(),
            'create_date'   => $this->dateTime()->defaultValue(new \yii\db\Expression('NOW()')),
        ]);
    }

    public function down() {
        $this->dropTable('static_files');
    }
}
