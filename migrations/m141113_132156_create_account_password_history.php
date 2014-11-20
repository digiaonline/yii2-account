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

class m141113_132156_create_account_password_history extends Migration
{
    public function up()
    {
        $this->createTable(
            'account_password_history',
            array(
                'id' => Schema::TYPE_PK,
                'accountId' => Schema::TYPE_INTEGER . ' NOT NULL',
                'password' => Schema::TYPE_STRING . ' NOT NULL',
                'createdAt' => Schema::TYPE_DATETIME . ' NULL DEFAULT NULL',
            )
        );
    }

    public function down()
    {
        $this->dropTable('account_password_history');
    }
}
