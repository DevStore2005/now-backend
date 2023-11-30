<?php

namespace App\Http\Helpers;

use App\Utils\UserType;
use App\Utils\PlatformType;
use Illuminate\Support\Facades\Log;

class Fcm
{
    public static function push_notification($data, $device_token, $userType = null, $osType = null)
    {
        if (!$userType)
            return;

        if ($osType && $osType == PlatformType::IOS) {
            Log::info(__METHOD__ . ' IOS');
            $data = [
                'to' => $device_token[0],
                "notification" => [
                    "title" => $data['title'],
                    "body" => $data['body'],
                    "sound" => "default",
                ],
                "data" => $data,
                "options" => [
                    'mutableContent' => true,
                    'contentAvailable' => true,
                    'apnsPushType' => 'background',
                ],
                "headers" => [
                    "apns-push-type" => "background",
                    "apns-priority" => "5",
                    "apns-topic" => "io.flutter.plugins.firebase.messaging"
                ]
            ];
        } else {
            Log::info(__METHOD__ . ' Android or Web');
            $data = [
                'to' => $device_token[0],
                "data" => $data,
            ];
        }


        $dataString = json_encode($data);

        $headers = [
            "Content-Type: application/json"
        ];

        if ($userType == UserType::USER) {
            $headers[] = 'Authorization: key=' . config('services.user_app_key');
        } else {
            $headers[] = 'Authorization: key=' . config('services.provider_app_key');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        $res = curl_exec($ch);
        Log::info(__METHOD__, ["res" => $res, "data" => $data]);
        try {
            curl_close($ch);
        } catch (\Throwable $th) {
            //throw $th;
        }
        return $res;
    }
}
