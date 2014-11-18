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

use yii\base\Component;

class RandomLibTokenGenerator extends Component implements TokenGeneratorInterface
{
    /**
     * @var int token length.
     */
    public $length = TokenGeneratorInterface::DEFAULT_TOKEN_LENGTH;

    /**
     * @var int token strength.
     */
    public $strength = \SecurityLib\Strength::MEDIUM;

    /**
     * @var string characters to use when generating a token string.
     */
    public $chars = 'abcdefghijklmnopqrstuvxyz0123456789';

    /**
     * @var \RandomLib\Factory Random factory instance.
     */
    private $_factory;

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $generator = $this->getFactory()->getGenerator(new \SecurityLib\Strength($this->strength));
        return $generator->generateString($this->length, $this->chars);
    }

    /**
     * Getter for the RandomLib factory instance.
     *
     * @return \RandomLib\Factory
     */
    protected function getFactory()
    {
        if (!isset($this->_factory)) {
            $this->_factory = new \RandomLib\Factory();
        }
        return $this->_factory;
    }
}