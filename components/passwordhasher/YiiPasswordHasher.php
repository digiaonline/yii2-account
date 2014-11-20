<?php
/*
 * This file is part of Account.
 *
 * (c) 2014 Nord Software
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nord\yii\account\components\passwordhasher;

use Yii;
use yii\base\Component;

class YiiPasswordHasher extends Component implements PasswordHasherInterface
{
    /**
     * @var int cost to use when hashing passwords.
     */
    public $cost = 13;

    /**
     * @inheritdoc
     */
    public function generatePasswordHash($password)
    {
        return Yii::$app->security->generatePasswordHash($password, $this->cost);
    }

    /**
     * @inheritdoc
     */
    public function validatePassword($password, $hash)
    {
        return Yii::$app->security->validatePassword($password, $hash);
    }
}