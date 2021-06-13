<?php


namespace App\Services;


use Illuminate\Support\Facades\Http;

class Wazzup
{
    const BASE_API = 'http://api.wazzup24.com/v2/';

    private static function instance($api_key)
    {
        return Http::withHeaders([
            'Authorization' => 'Basic ' . $api_key
        ]);
    }

    public static function updateOrCreate($api_key, $id, $username )
    {
        $response = self::instance($api_key)->patch(self::BASE_API . 'users', [
            [
                'id' => $id,
                'name' => $username
            ]
        ]);

        if ($response->status() !== 200) {
            return ['success' => false, 'errors' => $response->object()->errors];
        }

        return ['success' => true];
    }

    public static function sendMessage($api_key, $channelId, $chatId, $text)
    {
        $response = self::instance($api_key)->patch(self::BASE_API . 'sendMessage', [
            [
                'channelId' => $channelId,
                'chatId' => $chatId,
                'text' => $text
            ]
        ]);

        if ($response->status() !== 200) {
            return ['success' => false, 'errors' => $response->object()->errors];
        }

        return ['success' => true];
    }

    public static function getChannels($api_key)
    {
        $response = self::instance($api_key)->get(self::BASE_API.'channels');
        dd($response->object());
    }
}
