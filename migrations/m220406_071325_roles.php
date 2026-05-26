<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m220406_071325_roles
 */
class m220406_071325_roles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('roles', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' DEFAULT NULL',
            'public_key' => Schema::TYPE_INTEGER . ' NOT NULL',
            'id_role_in_system' => Schema::TYPE_BIGINT . ' NOT NULL'
        ]);
        $this->createIndex(
            'idx_roles_rolsys',
            'roles',
            'id_role_in_system'
        );
        $this->createIndex(
            'idx_roles_pk',
            'roles',
            'public_key'
        );
        $this->createIndex(
            'idx_roles_pk_rolsys',
            'roles',
            ['public_key','id_role_in_system'],
            true
        );
        $this->addForeignKey(
            'fk_roles_pk',
            'roles',
            ['public_key', 'public_key'],
            'users',
            ['id', 'public_key'],
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk_roles_pk',
            'roles'
        );
        $this->dropIndex(
            'idx_roles_pk_rolsys',
            'roles'
        );
        $this->dropIndex(
            'idx_roles_pk',
            'roles',
        );
        $this->dropIndex(
            'idx_roles_rolsys',
            'roles',
        );
        $this->dropTable('roles');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220406_071325_roles cannot be reverted.\n";

        return false;
    }
    */
}
