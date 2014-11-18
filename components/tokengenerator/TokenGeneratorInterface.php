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
    const DEFAULT_TOKEN_LENGTH = 32;

    /**
     * Generates a new random token.
     *
     * @return string the token
     */
    public function generate();
}