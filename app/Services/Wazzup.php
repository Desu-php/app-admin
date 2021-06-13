<?php


namespace App\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Wazzup
{
    private const BASE_API = 'http://api.wazzup24.com/v2/';
    private $api_key = '';

    public function __construct($api_key)
    {
        $this->api_key = $api_key;
    }

    private function send($api, $method = 'GET', $params = [])
    {
        $method = mb_strtolower($method);
        return Http::withHeaders([
            'Authorization' => 'Basic ' . $this->api_key
        ])->$method(self::BASE_API . $api, $params);
    }

    public function updateOrCreate($id, $username)
    {
        return $this->result($this->send('users', 'patch', [
            [
                'id' => $id,
                'name' => $username
            ]
        ]));
    }

    public function sendMessage($channelId, $chatId, $text)
    {
        return $this->result($this->send('send_message', 'post', [
            'channelId' => $channelId,
            'chatId' => $chatId,
            'text' => $text,
            'chatType' => 'whatsapp',
        ]));

    }

    public function getChannels()
    {
        return $this->result($this->send('channels'));
    }

    public function openChat($wazzup_id, $username, $scope = 'global', $filter = [])
    {
        return $this->result($this->send('iframe', 'post', [
            'user' => [
                'id' => $wazzup_id,
                'name' => $username
            ],
            'scope' => $scope,
            'filter' => [$filter]
        ]));
    }

    public function setWebhook($id)
    {
        Log::info('HOOK-INFO - '.route('webhook', $id));
        return $this->result($this->send('webhooks', 'PUT', [
            'url' => route('webhook', $id)
        ]));
    }

    private function result($response)
    {
        if ($response->status() >= 400) {
            if ($response->status() == 404) {
                return ['success' => false, 'errors' => 'Chat not found', 'status' => $response->status()];
            }elseif ($response->status() == 405) {
                return ['success' => false, 'errors' => $response->object()->error->message, 'status' => $response->status()];
            }
            return ['success' => false, 'errors' => !empty($response->object())?$response->object()->errors:'Что-то пошло не так', 'status' => $response->status()];
        }

        return ['success' => true, 'data' => $response->object()];
    }
}
