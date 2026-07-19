<?php

use yii\db\Migration;

class m260713_000002_backfill_support_operator_projects extends Migration
{
    public function safeUp(): void
    {
        $this->execute(
            "INSERT INTO support_operator_projects (owner_public_key, operator_user_id, project_id)
                SELECT users.public_key, users.id, support_projects.id
                FROM users
                INNER JOIN auth_assignment ON auth_assignment.user_id = users.id
                INNER JOIN support_projects ON support_projects.owner_public_key = users.public_key
                WHERE auth_assignment.item_name = 'manager'
                  AND users.status = 1
                  AND users.id <> users.public_key
                  AND support_projects.enabled = 1
                  AND NOT EXISTS (
                      SELECT 1
                      FROM support_operator_projects existing
                      WHERE existing.operator_user_id = users.id
                        AND existing.project_id = support_projects.id
                  )"
        );
    }

    public function safeDown(): void
    {
        $this->delete('support_operator_projects');
    }
}
