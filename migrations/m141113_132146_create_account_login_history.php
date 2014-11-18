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

class m141113_132146_create_account_login_history extends Migration
{
    public function up()
    {
        $this->createTable(
            'account_login_history',
            array(
                'id' => 'pk',
                'accountId' => "int NOT NULL DEFAULT '0'",
                'success' => "boolean NOT NULL DEFAULT '0'",
                'numFailedAttempts' => "int NOT NULL DEFAULT '0'",
                'createdAt' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
            )
        );
    }

    public function down()
    {
        $this->dropTable('account_login_history');
    }
}
