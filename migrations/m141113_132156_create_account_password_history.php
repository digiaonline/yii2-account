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

class m141113_132156_create_account_password_history extends Migration
{
    public function up()
    {
        $this->createTable(
            'account_password_history',
            array(
                'id' => 'pk',
                'accountId' => 'int NOT NULL',
                'salt' => 'string NOT NULL',
                'password' => 'string NOT NULL',
                'createdAt' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
            )
        );
    }

    public function down()
    {
        $this->dropTable('account_password_history');
    }
}
