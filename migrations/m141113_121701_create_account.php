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

class m141113_121701_create_account extends Migration
{
    public function up()
    {
        $this->createTable(
            'account',
            array(
                'id' => 'pk',
                'salt' => 'string NOT NULL',
                'username' => 'string NOT NULL',
                'password' => 'string NOT NULL',
                'authKey' => 'string NOT NULL',
                'email' => 'string NOT NULL',
                'passwordStrategy' => 'string NOT NULL',
                'requireNewPassword' => "boolean NOT NULL DEFAULT '0'",
                'lastLoginAt' => 'datetime NULL DEFAULT NULL',
                'createdAt' => 'datetime NULL DEFAULT NULL',
                'status' => "integer NOT NULL DEFAULT '0'",
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
