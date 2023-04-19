<?php

namespace app\components\bots;

use app\components\bots\base\BaseBot;
use GuzzleHttp\Client;

class TelegramBot extends BaseBot
{
    const BOT_ID = 1;

    protected $chat_id;

    public function __construct($text = '', $chat_id = '1020272528')
    {
        parent::__construct();

        $this->text = $text;
        $this->chat_id = $chat_id;
    }

    protected function setToken()
    {
        // test bot
        // $this->token = '5191732915:AAFe4lSeqS244SZXSagHrWb09fA852kyaJA';
        $this->token = '5191732915:AAFe4lSeqS244SZXSagHrWb09fA852kyaJA';
    }

    protected function setEndpoints()
    {
        $this->endpoints = [
            'send' => '/sendMessage',
            'updates' => '/getUpdates',
            'webhook' => '/setWebhook',
          	'info' => '/getWebhookInfo'
        ];
    }

    protected function setHost()
    {
        $this->host = "https://api.telegram.org/bot";
    }

    protected function setClient()
    {
        $this->client = new Client(['base_uri' => $this->host]);
    }

    public function sendMessage()
    {
        return $this->send('send', [
            'form_params' => [
                'text' => $this->text,
                'chat_id' => $this->chat_id
            ]
        ]);
    }

    public function setWebhook($url = "https://cc19244api.tmweb.ru/notification/tg-updates")
    {
        return $this->send('webhook', [
            'form_params' => [
                'url' => $url
            ]
        ]);
    }

    public function getUpdates()
    {
        return $this->get('updates');
    }
  
  	public function getWebhookInfo()
    {
        return $this->get('info');
    }
}