<?php


namespace App\Services;


use Illuminate\Support\Facades\Http;

class Wazzup
{
    const BASE_API = 'http://api.wazzup24.com/v2/';

    private function send($api_key, $api, $method = 'GET', $params = [])
    {
        return Http::withHeaders([
            'Authorization' => 'Basic ' . $api_key
        ])->send($method, self::BASE_API . $api, $params);
    }

    public  function updateOrCreate($api_key, $id, $username)
    {
        return self::result($this->send($api_key, 'users', 'patch', [
            [
                'id' => $id,
                'name' => $username
            ]
        ]));
    }

    public  function sendMessage($api_key, $channelId, $chatId, $text)
    {
        return self::result($this->send($api_key, 'send_message', 'post', [
            'channelId' => $channelId,
            'chatId' => $chatId,
            'text' => $text,
            'chatType' => 'whatsapp',
        ]));

    }

    public static function getChannels($api_key)
    {
        return self::result(self::instance($api_key, 'channels'));
    }

    public static function openChat($api_key, $wazzup_id, $username, $scope = 'global', $filter = [])
    {
        return self::result(self::instance($api_key, 'iframe', 'post', [
            'user' => [
                'id' => $wazzup_id,
                'name' => $username
            ],
            'scope' => $scope,
            'filter' => [$filter]
        ]));
    }

    public static function setWebhook($api_key, $id)
    {
        return self::result(self::instance($api_key, 'webhooks', 'post', [
            'url' => route('webhook', $id)
        ]));
    }

    private static function result($response)
    {
        if ($response->status() > 400) {
            if ($response->status() == 404) {
                return ['success' => false, 'errors' => 'Chat not found', 'status' => $response->status()];
            }
            return ['success' => false, 'errors' => $response->object()->errors, 'status' => $response->status()];
        }

        return ['success' => true, 'data' => $response->object()];
    }
}
