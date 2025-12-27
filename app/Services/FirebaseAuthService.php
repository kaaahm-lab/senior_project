<?php

namespace App\Services;

use GuzzleHttp\Client;

class FirebaseService
{
    protected $serverKey;

    public function __construct()
    {
        $this->serverKey = config('firebase.server_key');
    }

    public function sendNotification($token, $title, $body, $data = [])
    {
        $client = new Client();

        $response = $client->post('https://fcm.googleapis.com/fcm/send', [
            'headers' => [
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'to' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                ],
                'data' => $data,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
