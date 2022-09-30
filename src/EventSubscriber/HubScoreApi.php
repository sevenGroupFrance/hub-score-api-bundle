<?php

namespace SevenGroupFrance\HubScoreApiBundle\EventSubscriber;

class HubScoreApi
{
    private $id;
    private $pwd;
    private $forms;
    private $client;
    private $response;
    private $login_token;

    public function __construct($id, $pwd, $forms, $client)
    {
        $this->id = $id;
        $this->pwd = $pwd;
        $this->forms = $forms;
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
        $config = [];
        $fields = [];
        $userInfos = [];
        $email = '';
        $count = 1;
        foreach ($this->forms as $key => $value) {
            // if the key in the yaml config file is equal to the validated form title
            if ($key === $form['title']) {
                // save the form config and fields from the yaml config file in 2 arrays
                $config = $value['config'];
                $fields = $value['fields'];
                foreach ($form['fields'] as $formField) {
                    // if form has undesirable fields, we don't count them
                    if ($formField['key'] === "freeText") {
                        continue;
                    }
                    // if form has an email, we save it in a $email variable
                    if ($formField['key'] === "email") {
                        $email = $formField['value'];
                        continue;
                    }
                    // else, we save the other data in an associative array, $fieldDefinedInYamlConfig => $valueOfFieldInForm
                    $userInfos[$fields[$count]] = $formField['value'];
                    $count++;
                }
                break;
            }
        }
        dump($userInfos);
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
                    "campagnId" => $config['campaign_id'],
                    "databaseId" => $config['database_id'],
                    "userInfos" => $userInfos,
                    "overwriteUserInfos" => 1,
                    "alwaysInsert" => 0
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
