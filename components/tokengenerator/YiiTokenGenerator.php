<?php
/*
 * This file is part of Account.
 *
 * (c) 2014 Nord Software
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nord\yii\account\components\tokengenerator;

use Yii;
use yii\base\Component;

class YiiTokenGeneratorInterface extends Component implements TokenGeneratorInterface
{
    /**
     * @var int token length.
     */
    public $length = TokenGeneratorInterface::DEFAULT_TOKEN_LENGTH;

    /**
     * @inheritdoc
     */
    public function generate()
    {
        return Yii::$app->getSecurity()->generateRandomString($this->length);
    }
}