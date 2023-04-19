<?php

namespace app\components\bots\base;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

abstract class BaseBot implements BotInterface
{
    public string $host;
    public array $endpoints;
    public Client $client;
    public string $token;
    public string $text;
    public $user_id;

    public function __construct()
    {
        $this->setToken();
        $this->setEndpoints();
        $this->setHost();
        $this->setClient();
    }

    /**
     * Set bot api host
     * 
     * @return void
     */
    abstract protected function setHost();

    /**
     * Set curl client
     * 
     * @return void
     */
    abstract protected function setClient();

    /**
     * Set client api token for bot
     * 
     * @return void
     */
    abstract protected function setToken();

    /**
     * Set api endpoint where need to send messages
     * 
     * @return void
     */
    abstract protected function setEndpoints();

    /**
     * Get bot class by BOT_ID const
     * 
     * @return BaseBot|false - return bot class for notification
     */
    public static function getBotClass($bot_id)
    {
        $classes = array_filter(
            scandir(dirname(__DIR__)),
            fn($el) => str_contains($el, '.php')
        );

        $namespace = str_replace('base', '', __NAMESPACE__);

        $className = fn($str) => $namespace . (explode('.', $str))[0];

        foreach($classes as $class){
            $botName = $className($class);

            if($botName::BOT_ID === $bot_id){
                return $botName;
            };
        }

        return false;
    }


    public function send($event, $data)
    {
        if(!isset($this->endpoints[$event])){
            return false;
        }

        try {
            $promise = $this->client->postAsync(
                $this->host . $this->token . $this->endpoints[$event], $data
            );

            $response = $promise->wait();

            return $response->getBody()->getContents();

        } catch (RequestException $e) {
            return $e->getMessage();
        }
    }

    public function get($event)
    {
        if(!isset($this->endpoints[$event])){
            return false;
        }

        try {
            $promise = $this->client->getAsync(
                $this->host . $this->token . $this->endpoints[$event]
            );

            $response = $promise->wait();

            return $response->getBody()->getContents();

        } catch (RequestException $e) {
            return $e->getMessage();
        }
    }
}