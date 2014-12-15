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

use app\models\Account;
use nord\yii\account\components\passwordhasher\PasswordHasherInterface;
use nord\yii\account\Module;
use nord\yii\account\validators\PasswordStrengthValidator;
use Yii;
use yii\base\Behavior;
use yii\base\ModelEvent;
use yii\db\ActiveRecord;

/**
 * Behavior that ensures that the password field is updated correctly.
 *
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
     * Compares the given password against the owner's password hash.
     *
     * @param string $password password to validate.
     * @return boolean whether the password matches the one on record.
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
        if ($password !== $this->_passwordHash && $password !== '') {
            $passwordHash = $this->getPasswordHasher()->generatePasswordHash($password);
            $this->_passwordHash = $this->owner->{$this->attribute} = $passwordHash;
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
        if ($password !== $this->_passwordHash) {
            $validatorClass = Module::getInstance()->getClassName(Module::CLASS_PASSWORD_VALIDATOR);
            $config = Module::getInstance()->passwordConfig;
            /** @var PasswordStrengthValidator $validator */
            $validator = Yii::createObject($validatorClass, $config);
            $validator->attributes = [$this->attribute];
            $validator->validateAttributes($event->sender);
        }
    }

    /**
     * @return PasswordHasherInterface
     */
    protected function getPasswordHasher()
    {
        return Module::getInstance()->getPasswordHasher();
    }
}