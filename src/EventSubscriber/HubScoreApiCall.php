<?php

namespace SevenGroupFrance\HubScoreApiBundle\EventSubscriber;

class HubScoreApiCall
{
    public function login($id, $pwd, $client)
    {
        $response = $client->request(
            'POST',
            'https://api.hub-score.com/login_check',
            [
                'json' => [
                    'Username' => $id,
                    'Password' => $pwd
                ]
            ]
        );

        return $response;
    }
}
