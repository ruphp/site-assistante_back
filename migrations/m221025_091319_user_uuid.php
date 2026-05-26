<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m221025_091319_user_uuid
 */
class m221025_091319_user_uuid extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user_uuid', [
        'id' => Schema::TYPE_PK,
        'uuid' => $this->getDb()->getSchema()->createColumnSchemaBuilder('uuid'). ' NOT NULL',
        'public_key' => Schema::TYPE_INTEGER . ' NOT NULL',
    ]);
        $this->createIndex(
            'user_uuid_pk',
            'user_uuid',
            ['uuid', 'public_key'],
            true
        );

        $this->addForeignKey(
            'fk_user_uuid_pk',
            'user_uuid',
            'public_key',
            'users',
            'id',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk_user_uuid_pk',
            'user_uuid',
        );
        $this->dropIndex(
            'user_uuid_pk',
            'user_uuid',
        );
        $this->dropTable('user_uuid');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221025_091319_user_uuid cannot be reverted.\n";

        return false;
    }
    */
}
