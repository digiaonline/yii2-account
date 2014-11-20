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

class m141113_121701_create_account extends Migration
{
    public function up()
    {
        $this->createTable(
            'account',
            array(
                'id' => Schema::TYPE_PK,
                'username' => Schema::TYPE_STRING . ' NOT NULL',
                'password' => Schema::TYPE_STRING . ' NOT NULL',
                'authKey' => Schema::TYPE_STRING . ' NOT NULL',
                'email' => Schema::TYPE_STRING . ' NOT NULL',
                'lastLoginAt' => Schema::TYPE_DATETIME . ' NULL DEFAULT NULL',
                'createdAt' => Schema::TYPE_DATETIME . ' NULL DEFAULT NULL',
                'status' => Schema::TYPE_INTEGER . " NOT NULL DEFAULT '0'",
                'UNIQUE KEY username (username)',
                'UNIQUE KEY email (email)',
            )
        );
    }

    public function down()
    {
        $this->dropTable('account');
    }
}
