<?php

namespace app\components\bots\base;

interface BotInterface 
{
    /**
     * Send data to bot api
     * 
     * @param string $event bot api event/uri
     * @param array $data data to send
     * 
     */
    public function send(string $event, array $data);

    /**
     * Get info from bot
     * 
     * @param string $event bot api event/uri
     */
    public function get(string $event);
}