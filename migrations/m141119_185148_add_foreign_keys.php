<?php

use yii\db\Schema;
use yii\db\Migration;

class m141119_185148_add_foreign_keys extends Migration
{
    public function up()
    {
        $this->addForeignKey('account_token_accountId_account_id', 'account_token', 'accountId', 'account', 'id');
        $this->addForeignKey('account_login_history_accountId_account_id', 'account_login_history', 'accountId',
            'account', 'id');
        $this->addForeignKey('account_password_history_accountId_account_id', 'account_password_history', 'accountId',
            'account', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('account_password_history_accountId_account_id', 'account_password_history');
        $this->dropForeignKey('account_login_history_accountId_account_id', 'account_login_history');
        $this->dropForeignKey('account_token_accountId_account_id', 'account_token');
    }
}
