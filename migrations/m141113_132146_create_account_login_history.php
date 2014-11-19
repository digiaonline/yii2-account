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
use yii\db\Schema;

class m141113_132146_create_account_login_history extends Migration
{
    public function up()
    {
        $this->createTable(
            'account_login_history',
            array(
                'id' => Schema::TYPE_PK,
                'accountId' => Schema::TYPE_INTEGER . " NOT NULL DEFAULT '0'",
                'success' => Schema::TYPE_BOOLEAN . " NOT NULL DEFAULT '0'",
                'numFailedAttempts' => Schema::TYPE_INTEGER . " NOT NULL DEFAULT '0'",
                'createdAt' => Schema::TYPE_DATETIME . ' NULL DEFAULT NULL',
            )
        );
    }

    public function down()
    {
        $this->dropTable('account_login_history');
    }
}
