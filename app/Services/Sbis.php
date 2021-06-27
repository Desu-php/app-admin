<?php


namespace App\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Sbis
{
    private $sid;

    public function __construct($app_client_id, $app_secret, $secret_key)
    {
        $this->sid = $this->getToken($app_client_id, $app_secret, $secret_key)->sid;
    }

    public function getToken($app_client_id, $app_secret, $secret_key)
    {
        $response = Http::post('https://online.sbis.ru/oauth/service/', [
            'app_client_id' => $app_client_id,
            'app_secret' => $app_secret,
            'secret_key' => $secret_key
        ]);

        if (!$response->ok()) {
            return ['success' => false, 'errors' => 'Проверьте реквизиты доступа sbis.ru', 'status' => $response->status()];
        }

        return $response->object();
    }

    public function sendMessage()
    {
        $response = $this->send('CRMEvent.AddEvent', [

            'd' => [
                'EventType' => 3,
                'LeadID' => 3821792,
                'RespID' => 43,
                'Comment' => 'Тестинг event',
                'KindOfContact' => 0,
                'ContactType' => 1,
                'CurrentPhaseId' => 161429,
                'PhaseDirectionId' => 161431,
            ],
            's' => [
                'EventType' => 'Число целое',
                'LeadID' => 'Число целое',
                'RespID' => 'Число целое',
                'Comment' => 'Строка',
                'KindOfContact' => 'Число целое',
                'ContactType' => 'Число целое',
                'CurrentPhaseId' => 'Число целое',
                'PhaseDirectionId' => 'Число целое',
            ]

        ], 'EventData');

        $response->object();
    }

    private function send($method, $params, $type = false)
    {
        if ($type) {
            $locParams = [
                $type => $params
            ];
        } else {
            $locParams = $params;
        }

        return Http::withHeaders([
            'X-SBISSessionID' => $this->sid,
            'Content-Type' => 'application/json-rpc; charset=utf-8',
            'Accept' => 'application/json-rpc'
        ])->post('https://online.sbis.ru/service/', [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $locParams,
            'id' => Str::uuid()
        ]);
    }

    public function createLead($theme, $fio, $phone)
    {
        $theme = $this->getTheme($theme);
        $params = [
            'd' => [
                'Регламент' => $theme['result']['d']['ИдентификаторТемы'],
                'КонтактноеЛицо' => [
                    'd' => [
                        'ФИО' => 'Тестов Тест Тестович',
                        'Телефон' => '992929982945'
                    ],
                    's' => [
                        'ФИО' => 'Строка',
                        'Телефон' => 'Строка'
                    ],
                ],
                'Примечание' => 'Лид с wazzup'
            ],
            's' => [
                'Регламент' => 'Строка',
                'КонтактноеЛицо' => 'Запись',
                'Примечание' => 'Строка',
            ],
            '_type' => 'record',
            'f' => 0
        ];

        $response = $this->send('CRMLead.insertRecord', $params, 'Лид');

        return $response->collect();
    }

    public function getTheme($name)
    {
        $params = [
            'НаименованиеТемы' => $name
        ];

        $response = $this->send('CRMLead.getCRMThemeByName', $params);

        return $response->collect()->toArray();
    }

    public function getThemes()
    {
        $response = $this->send('CRMTheme.GetList', [
            "d" => [
                'HideDeleted' => true,
                'WithGroup' => true,
                'CalcCol' => null
            ],
            "s" => [
                'HideDeleted' => 'Логическое',
                'WithGroup' => 'Логическое',
                'CalcCol' => 'Строка'
            ]], 'Param');

        return $response->object();
    }

    public function readStaff()
    {
        $params = [
            "Сотрудник" => [
                "ИдентификаторИС" => "Softjet"
            ]
        ];
        $response = $this->send('СБИС.ПрочитатьСотрудника', $params, 'Параметр');
        return $response->object();
    }

}
