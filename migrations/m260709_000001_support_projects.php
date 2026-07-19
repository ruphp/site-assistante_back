<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

class m260709_000001_support_projects extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('support_projects', [
            'id' => Schema::TYPE_PK,
            'owner_public_key' => Schema::TYPE_INTEGER . ' NOT NULL',
            'public_key' => Schema::TYPE_INTEGER . ' NOT NULL',
            'name' => Schema::TYPE_STRING . " NOT NULL DEFAULT 'Основной сайт'",
            'domain' => Schema::TYPE_STRING . ' DEFAULT NULL',
            'enabled' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1',
            'is_default' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'created_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT NOW()',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT NOW()',
        ]);

        $this->createIndex('idx_support_projects_owner', 'support_projects', 'owner_public_key');
        $this->createIndex('idx_support_projects_public_key', 'support_projects', 'public_key');

        $this->addForeignKey(
            'fk_support_projects_owner_public_key',
            'support_projects',
            'owner_public_key',
            'users',
            'id',
            'CASCADE',
        );

        $this->addForeignKey(
            'fk_support_projects_public_key',
            'support_projects',
            'public_key',
            'users',
            'id',
            'CASCADE',
        );

        $this->addColumn('support_conversations', 'visitor_phone', Schema::TYPE_STRING . ' DEFAULT NULL');

        $this->execute(
            "INSERT INTO support_projects (owner_public_key, public_key, name, domain, enabled, is_default)
                SELECT users.public_key,
                       users.public_key,
                       COALESCE(NULLIF(users.firm, ''), users.name, 'Основной сайт'),
                       params.domain,
                       1,
                       1
                FROM users
                LEFT JOIN params ON params.public_key = users.public_key
                WHERE users.public_key IS NOT NULL
                  AND users.id = users.public_key
                  AND NOT EXISTS (
                      SELECT 1
                      FROM support_projects existing_project
                      WHERE existing_project.owner_public_key = users.public_key
                        AND existing_project.is_default = 1
                  )
                GROUP BY users.public_key, users.firm, users.name, params.domain"
        );
    }

    public function safeDown(): void
    {
        $this->dropColumn('support_conversations', 'visitor_phone');

        $this->dropForeignKey('fk_support_projects_public_key', 'support_projects');
        $this->dropForeignKey('fk_support_projects_owner_public_key', 'support_projects');
        $this->dropIndex('idx_support_projects_public_key', 'support_projects');
        $this->dropIndex('idx_support_projects_owner', 'support_projects');
        $this->dropTable('support_projects');
    }
}
