<?php

namespace OrderNotificationForTelegramBot\Common\Helper;

use OrderNotificationForTelegramBot\Base\Singleton;

class TelegramBot extends Singleton
{
    protected array $requestArgs = [
        'timeout' => 50,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'cookies' => array()
    ];

    protected $chatID = null;
    protected ?string $token = null;
    protected string $parseMode;
    protected string $accessTags;

    protected string $defaultEndpoint;
    protected string $googleScript;

    protected bool $useTelegramEndPoint;

    function setChatID($chatID)
    {
        $this->chatID = $chatID;
    }

    function setToken(string $token)
    {
        $this->token = $token;
    }

    public function setParseMode(string $parseMode): void
    {
        $this->parseMode = $parseMode;
    }

    public function setAccessTags(string $accessTags): void
    {
        $this->accessTags = $accessTags;
    }

    public function setDefaultEndpoint(string $defaultEndpoint): void
    {
        $this->defaultEndpoint = $defaultEndpoint;
    }

    public function setGoogleScript(string $googleScript): void
    {
        $this->googleScript = $googleScript;
    }

    function getTelegramBotEndpoint(): string
    {
        return "https://api.telegram.org/bot$this->token/";
    }

    public function setUseTelegramEndPoint(bool $useTelegramEndPoint): void
    {
        $this->useTelegramEndPoint = $useTelegramEndPoint;
    }

    protected function sendMessage($chatId, $text, $token, $parseMode, $requestArgs)
    {
        do_action('onftb_telegram_send_message_before');

        if (!empty($this->googleScript)) {
            $data = apply_filters('onftb_telegram_send_message_data', [
                'chat_id' => $chatId,
                'text' => stripcslashes(html_entity_decode($text)),
                'parse_mode' => $parseMode,
                'token' => $token,
                'method' => __FUNCTION__
            ]);

            $requestArgs['body'] = $data;
            $return = $this->wpPostRequest($this->googleScript, $requestArgs);
            $this->printResponse($return);
            return;
        }

        $data = apply_filters('onftb_telegram_send_message_data', [
            'chat_id' => $chatId,
            'text' => stripcslashes(html_entity_decode($text)),
            'parse_mode' => $parseMode,
        ]);

        $endpoint = $this->getEndpoint() . __FUNCTION__;
        $requestArgs['body'] = $data;
        $requestArgs['headers'] = ["Token" => $token];
        $return = $this->wpPostRequest($endpoint, $requestArgs);

        $this->printResponse($return);
    }


    public function init()
    {

    }

    public function request($text)
    {
        if (!isset($this->chatID) || !$this->chatID || !isset($this->token) || !$this->token) {
            return;
        }

        $text = strip_tags($text, $this->accessTags);

        $chatIds = explode(',', $this->chatID);

        if (is_array($chatIds) && count($chatIds) > 1) {
            foreach ($chatIds as $chatId) {
                $this->sendMessage($chatId, $text, $this->token, $this->parseMode,  $this->requestArgs);
            }
        } else {
            $this->sendMessage($this->chatID, $text, $this->token, $this->parseMode, $this->requestArgs);
        }
    }


    protected function wpPostRequest($endpoint, $args)
    {
        return wp_remote_post($endpoint, $args);
    }

    function printResponse($return)
    {
        if (is_wp_error($return)) {
            json_encode(['ok' => false, 'curl_error_code' => $return->get_error_message()]);
        } else {
            json_decode($return['body'], true);
        }
    }

    private function getEndpoint(): string
    {
        if ($this->useTelegramEndPoint) {
            return $this->getTelegramBotEndpoint();
        }

        return $this->defaultEndpoint;
    }
}