<?php

namespace nord\yii\account\components\mailsender;

use GuzzleHttp\Client;
use Ivory\HttpAdapter\Guzzle6HttpAdapter;
use SparkPost\SparkPost;
use yii\base\Component;

/**
 * Class SparkPostMailSender
 * @package nord\yii\account\components\mailsender
 */
class SparkPostMailSender extends Component implements MailSenderInterface
{

    const TEMPLATE_ACTIVATE       = 'activateAccount';
    const TEMPLATE_RESET_PASSWORD = 'resetPassword';

    /**
     * @var string the SparkPost API key
     */
    public $apiKey;

    /**
     * @var array
     */
    public $templates = [];

    /**
     * @var SparkPost the SparkPost client
     */
    private $_client;


    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        // Configure the client
        $httpAdapter   = new Guzzle6HttpAdapter(new Client());
        $this->_client = new SparkPost($httpAdapter, [
            'key' => $this->apiKey,
        ]);
    }


    /**
     * @inheritDoc
     */
    public function sendActivationMail(array $config = [])
    {
        return $this->sendEmail($config, $this->templates[self::TEMPLATE_ACTIVATE]);
    }


    /**
     * @inheritDoc
     */
    public function sendResetPasswordMail(array $config = [])
    {
        return $this->sendEmail($config, $this->templates[self::TEMPLATE_RESET_PASSWORD]);
    }


    /**
     * Sends an e-mail using the specified configuration and template
     *
     * @param array  $config
     * @param string $template
     *
     * @return bool
     */
    private function sendEmail(array $config, $template)
    {
        $substitutionData = $config['data'];
        $recipients       = [];

        foreach ($config['to'] as $recipient) {
            $recipients[] = [
                'address' => [
                    'email' => $recipient,
                ],
            ];
        }

        try {
            $this->_client->transmission->send([
                'substitutionData' => $substitutionData,
                'recipients'       => $recipients,
                'template'         => $template,
            ]);

            return true;
        } catch (\Exception $err) {
            return false;
        }
    }

}
