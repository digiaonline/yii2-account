<?php

use yii\db\Schema;
use yii\db\Migration;

class m141119_185128_account_provider_create extends Migration
{
    public function up()
    {
        $this->createTable(
            'account_provider',
            [
                'id' => Schema::TYPE_PK,
                'accountId' => Schema::TYPE_INTEGER . ' NOT NULL',
                'name' => Schema::TYPE_STRING . ' NOT NULL',
                'clientId' => Schema::TYPE_STRING . ' NOT NULL',
                'data' => Schema::TYPE_TEXT,
                'UNIQUE KEY accountId_name_clientId (accountId, name, clientId)',
            ]
        );
    }

    public function down()
    {
        $this->dropTable('account_provider');
    }
}
