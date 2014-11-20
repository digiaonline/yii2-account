<?php

namespace nord\yii\account\widgets;

use yii\helpers\Html;

class AuthChoice extends \yii\authclient\widgets\AuthChoice
{
    /**
     * @var array
     */
    public $baseAuthUrl = ['/account/authenticate/client'];

    /**
     * @inheritdoc
     */
    protected function renderMainContent()
    {
        foreach ($this->getClients() as $externalService) {
            $this->clientLink($externalService);
        }
    }

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