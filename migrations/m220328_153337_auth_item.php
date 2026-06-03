<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m220328_153337_auth_item
 */
class m220328_153337_auth_item extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropPrimaryKey('auth_assignment_pkey','auth_assignment');
        $this->alterColumn('auth_assignment', 'user_id', 'int USING user_id::integer');
        $this->addPrimaryKey('auth_assignment_pkey', 'auth_assignment', ["item_name","user_id"]);
        $this->batchInsert('auth_item', ["name","type","description","data"],
           [
               ['admin',1,'Администратор',NULL],
               ['manager',1,'Менеджер',NULL],
               ['accesses_modules',2,'Доступы к модулям',NULL],
           ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {


        $this->truncateTable('auth_item');
    }


}
