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

class m141113_131938_create_account_token extends Migration
{
    public function up()
    {
        $this->createTable(
            'account_token',
            array(
                'id' => 'pk',
                'accountId' => 'int NOT NULL',
                'type' => 'string NOT NULL',
                'token' => 'string NOT NULL',
                'createdAt' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
                'status' => "integer NOT NULL DEFAULT '0'",
                'UNIQUE KEY accountId_type_token (accountId, type, token)',
            )
        );
    }

    public function down()
    {
        $this->dropTable('account_token');
    }
}
