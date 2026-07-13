<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

class m260713_000001_support_operator_projects extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('support_operator_projects', [
            'id' => Schema::TYPE_PK,
            'owner_public_key' => Schema::TYPE_INTEGER . ' NOT NULL',
            'operator_user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'project_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'created_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT NOW()',
        ]);

        $this->createIndex(
            'idx_support_operator_projects_owner_operator',
            'support_operator_projects',
            ['owner_public_key', 'operator_user_id'],
        );
        $this->createIndex(
            'idx_support_operator_projects_project',
            'support_operator_projects',
            'project_id',
        );
        $this->createIndex(
            'ux_support_operator_projects_operator_project',
            'support_operator_projects',
            ['operator_user_id', 'project_id'],
            true,
        );

        $this->addForeignKey(
            'fk_support_operator_projects_owner',
            'support_operator_projects',
            'owner_public_key',
            'users',
            'id',
            'CASCADE',
        );
        $this->addForeignKey(
            'fk_support_operator_projects_operator',
            'support_operator_projects',
            'operator_user_id',
            'users',
            'id',
            'CASCADE',
        );
        $this->addForeignKey(
            'fk_support_operator_projects_project',
            'support_operator_projects',
            'project_id',
            'support_projects',
            'id',
            'CASCADE',
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_support_operator_projects_project', 'support_operator_projects');
        $this->dropForeignKey('fk_support_operator_projects_operator', 'support_operator_projects');
        $this->dropForeignKey('fk_support_operator_projects_owner', 'support_operator_projects');
        $this->dropIndex('ux_support_operator_projects_operator_project', 'support_operator_projects');
        $this->dropIndex('idx_support_operator_projects_project', 'support_operator_projects');
        $this->dropIndex('idx_support_operator_projects_owner_operator', 'support_operator_projects');
        $this->dropTable('support_operator_projects');
    }
}
