<?php
/*
 * This file is part of Account.
 *
 * (c) 2014 Nord Software
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nord\yii\account\validators;

use nord\yii\account\Module;
use yii\validators\Validator;

class PasswordStrengthValidator extends Validator
{
    /**
     * @var int minimum password length.
     */
    public $minLength = 6;
    /**
     * @var int minimum amount of upper case characters required.
     */
    public $minUpperCaseLetters = 0;
    /**
     * @var int minimum amount of lower case characters required.
     */
    public $minLowerCaseLetters = 0;
    /**
     * @var int minimum amount of non alpha numeric characters required.
     */
    public $minDigits = 0;
    /**
     * @var int minimum amount of special characters required.
     */
    public $minSpecialChars = 0;
    /**
     * @var string special characters.
     */
    public $specialChars = [" ", "'", "~", "!", "@", "#", "£", "$", "%", "^", "&", "\*", "(", ")", "_", "-", "\+", "=", "[", "]", "\\", "\|", "{", "}", ";", ":", "\"", "\.", ",", "\/", "<", ">", "\?", "`"];

    /**
     * @inheritdoc
     */
    public function validateAttribute($object, $attribute)
    {
        $password = $object->$attribute;
        $length = mb_strlen($password);
        if ($this->minLength && $length < $this->minLength) {
            $this->addError($object, $attribute, Module::t(
                'errors',
                "{attribute} is too short, minimum is {n} {n, plural, =1{character} other{characters}}.",
                ['n' => $this->minLength]
            ));
            return false;
        }
        if ($this->minDigits) {
            $digits = '';
            if (preg_match_all("/[\d+]/u", $password, $matches)) {
                $digits = implode('', $matches[0]);
            }
            if (mb_strlen($digits) < $this->minDigits) {
                $this->addError($object, $attribute, Module::t(
                    'errors',
                    "{attribute} should contain at least {n} {n, plural, =1{digit} other{digits}}.",
                    ['n' => $this->minLength]
                ));
                return false;
            }
        }
        if ($this->minUpperCaseLetters) {
            $numUpperCaseChars = '';
            if (preg_match_all("/[A-Z]/u", $password, $matches)) {
                $numUpperCaseChars = implode('', $matches[0]);
            }
            if (mb_strlen($numUpperCaseChars) < $this->minUpperCaseLetters) {
                $this->addError($object, $attribute, Module::t(
                    'errors',
                    "{attribute} should contain at least {n} upper case {n, plural, =1{character} other{characters}}.",
                    ['n' => $this->minLength]
                ));
                return false;
            }
        }
        if ($this->minLowerCaseLetters) {
            $numLowerCaseChars = '';
            if (preg_match_all("/[a-z]/u", $password, $matches)) {
                $numLowerCaseChars = implode('', $matches[0]);
            }
            if (mb_strlen($numLowerCaseChars) < $this->minLowerCaseLetters) {
                $this->addError($object, $attribute, Module::t(
                    'errors',
                    "{attribute} should contain at least {n} lower case {n, plural, =1{character} other{characters}}.",
                    ['n' => $this->minLength]
                ));
                return false;
            }
        }
        if ($this->minSpecialChars) {
            $numSpecialChars = '';
            if (preg_match_all("/[" . implode('|', $this->specialChars) . "]/u", $password, $matches)) {
                $numSpecialChars = implode('', $matches[0]);
            }
            if (mb_strlen($numSpecialChars) < $this->minSpecialChars) {
                $this->addError($object, $attribute, Module::t(
                    'errors',
                    "{attribute} should contain at least {n} non alpha numeric {n, plural, =1{character} other{characters}}.",
                    ['n' => $this->minLength]
                ));
                return false;
            }
        }
        return true;
    }
}