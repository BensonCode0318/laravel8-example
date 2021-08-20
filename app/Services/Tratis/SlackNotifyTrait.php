<?php

namespace App\Services\Traits;

use Illuminate\Support\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Exception\RequestException;

trait SlackNotifyTrait
{
    /**
     * 發送slack通知
     * @param int $companyId
     * @param int $storeId
     * @param array $opitons [
     *          "title": "xxxx" 通知標題
     *          "message": "xxxx" 通知內容
     *      ]
     *
     */

    public static function SlackNotify($opitons)
    {
        $client  = new Client();
        // TODO: 待改env或者constant
        $url     = env('SLACK_MESSAGE_URL');
        $headers = ["Content-Type: application/json"];
        try {
            $response = $client->request(
                'POST',
                $url,
                [
                'headers'         => $headers,
                'json'            => self::messageFormat($opitons),
                'connect_timeout' => 3,
            ]
            )->getBody()->getContents();
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $errorResponse = Message::toString($e->getResponse());
                
                // slack notify異常。
                \Log::debug('SlackNotifyTrait::SlackNotify', [
                    'error message' => $errorResponse,
                ]);
            }
        }
    }

    private static function messageFormat($opitons)
    {
        return [
            "text" => $opitons['title'],
            "attachments" => [
                [
                    "mrkdwn_in" => "text",
                    "color" => "#FF0000",
                    "fields" => [
                        [
                            "title" => "Detail:",
                            "value" => $opitons['message'],
                            "short" => false
                        ]
                    ],
                    "footer" => Carbon::now()->toDateTimeString(),
                ]
            ]
        ];
    }
}
