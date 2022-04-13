<?php

namespace SevenGroupFrance\HubScoreApiBundle\EventSubscriber;

class HubScoreApiCall
{
    public function login($id, $pswd, $client)
    {
        $response = $client->request(
            'POST',
            'https://api.hub-score.com/login_check',
            [
                'json' => [
                    'Username' => $id,
                    'Password' => $pswd
                ]
            ]
        );

        return $response;
    }
}
