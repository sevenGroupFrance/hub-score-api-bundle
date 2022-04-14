<?php

namespace SevenGroupFrance\HubScoreApiBundle\EventSubscriber;

class HubScoreApi
{
    private $id;
    private $pwd;
    private $client;
    private $response;
    private $login_token;

    public function __construct($id, $pwd, $client)
    {
        $this->id = $id;
        $this->pwd = $pwd;
        $this->client = $client;
        $this->response = $this->login($this->id, $this->pwd, $this->client);
        $this->login_token = $this->response->toArray()['token'];
    }

    private function login($id, $pwd, $client)
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

    public function sendForm($client, $form)
    {
        $email = $form['fields'][1]['value'];
        $firstName = $form['fields'][2]['value'];
        $lastName = $form['fields'][3]['value'];
        // $phone = $form['fields'][4]['value'];
        // $function = $form['fields'][5]['value'];
        // $company = $form['fields'][6]['value'];
        // $select = $form['fields'][7]['value'];
        $textarea = $form['fields'][8]['value'];

        $client->request(
            'POST',
            'https://api.hub-score.com/v1/sends/mails',
            [
                "headers" =>
                [
                    'Authorization: Bearer ' . $this->login_token
                ],
                "json" =>
                [
                    "userMail" => $email,
                    "campagnId" => 356,
                    "databaseId" => 14,
                    "userInfos" =>
                    [
                        "nom" => $lastName,
                        "prnom" => $firstName,
                        "test" => $textarea
                    ],
                    "overwriteUserInfos" => 1,
                    "html" => "string",
                    "attachementName1" => "string",
                    "attachement1" => "string",
                    "attachementName2" => "string",
                    "attachement2" => "string",
                    "alwaysInsert" => "string"
                ]
            ]
        );
    }

    public function getResponse()
    {
        return $this->response;
    }
    public function getLoginToken()
    {
        return $this->login_token;
    }
}
