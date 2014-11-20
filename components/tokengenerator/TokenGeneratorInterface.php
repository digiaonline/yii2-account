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

interface TokenGeneratorInterface
{
    /**
     * Generates a new random token.
     *
     * @return string the token
     */
    public function generate();
}