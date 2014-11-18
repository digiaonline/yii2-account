<?php
/*
 * This file is part of Account.
 *
 * (c) 2014 Nord Software
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use yii\db\Migration;

class m141113_132304_add_foreign_keys extends Migration
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
