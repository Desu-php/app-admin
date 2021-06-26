<?php


namespace App\Services;


use App\Models\SbisAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Sbis
{
    private $app_client_id;
    private $app_secret;
    private $secret_key;
    private $sid;

    public function __construct($app_client_id, $app_secret, $secret_key)
    {
        $this->app_client_id = $app_client_id;
        $this->app_secret = $app_secret;
        $this->secret_key = $secret_key;
        $this->sid = $this->getToken()->sid;
    }

    public function getToken()
    {
        $response = Http::post('https://online.sbis.ru/oauth/service/', [
            'app_client_id' => $this->app_client_id,
            'app_secret' => $this->app_secret,
            'secret_key' => $this->secret_key
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

        dd($response->object());
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

    public function createLead()
    {
        $theme = $this->getTheme();
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
                'Примечание' => 'Тест лид'
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

        dd($response->object());
    }

    public function getTheme()
    {
        $params = [
            'НаименованиеТемы' => 'Голосовой бот'
        ];

        $response = $this->send('CRMLead.getCRMThemeByName', $params);

        return $response->collect()->toArray();
    }

    public function getThemes()
    {
        $response = $this->send('CRMTheme.GetList', [
            "d" => [
                true,
                true,
                null
            ],
            "s" => [
                [
                    "t" => "Логическое",
                    "n" => "HideDeleted"
                ],
                [
                    "t" => "Логическое",
                    "n" => "WithGroup"
                ],
                [
                    "t" => [
                        "n" => "Массив",
                        "t" => "Строка"
                    ],
                    "n" => "CalcCol"
                ],
            ]], 'Param');
        dd($response->object());
    }

    public function readStaff()
    {
        $params = [
            "Сотрудник" => [
                "ИдентификаторИС" => "Softjet"
            ]
        ];
        $response = $this->send('СБИС.ПрочитатьСотрудника', $params, 'Параметр');
        dd($response->object());
    }

//    public function getStates()
//    {
//        $response = $this->getThemes();
//        $params = [
//            "ID" => null,
//            "UUID" => theme_uuid, "Parameters": None}, "id": 1
//        ];
//
//        $response = $this->send('CRMTheme.ReadTheme', $params);
//    }
}
