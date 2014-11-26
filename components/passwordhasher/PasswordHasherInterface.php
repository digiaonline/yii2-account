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

interface PasswordHasherInterface
{
    /**
     * Hashes the given password.
     *
     * @param string $password password to hash.
     * @return string password hash.
     */
    public function generatePasswordHash($password);

    /**
     * Verifies the given password.
     *
     * @param string $password password to verify.
     * @param string $hash password hash.
     * @return boolean whether the passwords match.
     */
    public function validatePassword($password, $hash);
}