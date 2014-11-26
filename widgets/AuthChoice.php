<?php

namespace nord\yii\account\widgets;

use nord\yii\account\Module;
use yii\authclient\widgets\AuthChoice as BaseAuthChoice;
use yii\helpers\Html;

class AuthChoice extends BaseAuthChoice
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->setBaseAuthUrl([Module::URL_ROUTE_CLIENT_AUTH]);
    }

    /**
     * @inheritdoc
     */
    protected function renderMainContent()
    {
        foreach ($this->getClients() as $externalService) {
            $this->clientLink($externalService);
        }
    }

    /**
     * @inheritdoc
     */
    public function clientLink($client, $text = null, array $htmlOptions = [])
    {
        if ($text === null) {
            $text = $client->getTitle();
        }
        if ($this->popupMode) {
            $viewOptions = $client->getViewOptions();
            if (isset($viewOptions['popupWidth'])) {
                $htmlOptions['data-popup-width'] = $viewOptions['popupWidth'];
            }
            if (isset($viewOptions['popupHeight'])) {
                $htmlOptions['data-popup-height'] = $viewOptions['popupHeight'];
            }
        }
        Html::addCssClass($htmlOptions, 'btn btn-primary');
        echo Html::a($text, $this->createClientUrl($client), $htmlOptions);
    }
}