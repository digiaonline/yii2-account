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

use yii\base\Behavior;
use yii\base\ModelEvent;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * Behavior that allows for automatic encoding/decoding of JSON attributes.
 *
 * @property ActiveRecord $owner
 */
class JsonAttributeBehavior extends Behavior
{
    /**
     * @var array list of attributes that this behavior should handle.
     */
    public $attributes = [];

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
        ];
    }

    /**
     * Invoked after finding the owner of this behavior.
     *
     * @param ModelEvent $event event instance.
     */
    public function afterFind($event)
    {
        foreach ($this->attributes as $name) {
            $this->owner->$name = Json::decode($this->owner->$name);
        }
    }

    /**
     * Invoked before saving the owner of this behavior.
     *
     * @param ModelEvent $event event instance.
     */
    public function beforeSave($event)
    {
        foreach ($this->attributes as $name) {
            $this->owner->$name = !empty($this->owner->$name) ? Json::encode($this->owner->$name) : null;
        }
    }
}