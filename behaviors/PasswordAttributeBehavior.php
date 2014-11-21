<?php
/*
 * This file is part of Account.
 *
 * (c) 2014 Nord Software
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nord\yii\account\behaviors;

use nord\yii\account\components\passwordhasher\PasswordHasherInterface;
use nord\yii\account\Module;
use Yii;
use yii\base\Behavior;
use yii\base\ModelEvent;
use yii\db\ActiveRecord;

/**
 * @property ActiveRecord $owner
 */
class PasswordAttributeBehavior extends Behavior
{
    /**
     * @var string name of the password attribute.
     */
    public $attribute = 'password';

    /**
     * @var string holds the current password, used to detect whether the password has been changed.
     */
    protected $_passwordHash;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
        ];
    }

    /**
     * @param $password
     * @param bool $runValidation
     * @return bool
     */
    public function changePassword($password, $runValidation = true)
    {
        $this->owner->{$this->attribute} = $password;
        return $this->owner->save($runValidation, [$this->attribute]);
    }

    /**
     * Compares the given password against the owner's password hash.
     *
     * @param string $password password to validate.
     * @return bool whether the password matches the one on record.
     */
    public function validatePassword($password)
    {
        return $this->getPasswordHasher()->validatePassword($password, $this->owner->{$this->attribute});
    }

    /**
     * Invoked after querying the owner of this behavior.
     *
     * @param ModelEvent $event event instance.
     */
    public function afterFind($event)
    {
        $this->_passwordHash = $event->sender->{$this->attribute};
    }

    /**
     * Invoked before saving the owner of this behavior.
     *
     * @param ModelEvent $event event instance.
     */
    public function beforeSave($event)
    {
        $password = $event->sender->{$this->attribute};
        if ($password !== '' && $this->isPasswordChanged($password)) {
            $this->changePasswordInternal($password);
        }
    }

    /**
     * Invoked before validating the owner of this behavior.
     *
     * @param ModelEvent $event event instance.
     */
    public function beforeValidate($event)
    {
        $password = $event->sender->{$this->attribute};
        if ($password !== '' && $this->isPasswordChanged($password)) {
            $config = Module::getInstance()->passwordStrategy;
            $validatorClass = Module::getInstance()->getClassName(Module::CLASS_PASSWORD_VALIDATOR);
            $validator = Yii::createObject($validatorClass, $config);
            $validator->attributes = [$this->attribute];
            $validator->validateAttributes($event->sender);
        }
    }

    /**
     * Changes the value of the password attribute without performing a save operation.
     *
     * @param string $password the new password.
     */
    protected function changePasswordInternal($password)
    {
        $passwordHash = $this->getPasswordHasher()->generatePasswordHash($password);
        $this->_passwordHash = $this->owner->{$this->attribute} = $passwordHash;
    }

    /**
     * Returns whether the password has been changed.
     *
     * @param string $password password to compare.
     * @return bool whether the password has changed.
     */
    protected function isPasswordChanged($password)
    {
        return $password !== $this->_passwordHash;
    }

    /**
     * @return PasswordHasherInterface
     */
    protected function getPasswordHasher()
    {
        return Module::getInstance()->getPasswordHasher();
    }
}