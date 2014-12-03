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

class m141113_131938_create_account_token extends Migration
{
    public function up()
    {
        $this->createTable(
            'account_token',
            [
                'id' => Schema::TYPE_PK,
                'accountId' => Schema::TYPE_INTEGER . ' NOT NULL',
                'type' => Schema::TYPE_STRING . ' NOT NULL',
                'token' => Schema::TYPE_STRING . ' NOT NULL',
                'createdAt' => Schema::TYPE_DATETIME . ' NULL DEFAULT NULL',
                'status' => Schema::TYPE_INTEGER . " NOT NULL DEFAULT '0'",
                'UNIQUE KEY accountId_type_token (accountId, type, token)',
            ]
        );
    }

    public function down()
    {
        $this->dropTable('account_token');
    }
}
