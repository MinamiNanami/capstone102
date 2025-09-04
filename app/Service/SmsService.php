<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsService
{
    public static function send($phoneNumber, $message)
    {
        $response = Http::asForm()->post('https://sms.iprogtech.com/api/v1/sms_messages', [
            'api_token'    => 'fb541f1db87f41b70b6a9521c7b13192d09dffa4',
            'message'      => $message,
            'phone_number' => $phoneNumber,
        ]);

        return $response->successful();
    }
}
