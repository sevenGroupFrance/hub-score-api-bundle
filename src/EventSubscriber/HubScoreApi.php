<?php

namespace SevenGroupFrance\HubScoreApiBundle\EventSubscriber;

class HubScoreApi
{
    /**
     * @var string $id
     */
    private $id;
    /**
     * @var string $pwd
     */
    private $pwd;
    /**
     * @var array $forms
     */
    private $forms;
    /**
     * @var object $client
     */
    private $client;
    /**
     * @var object $response
     */
    private $response;
    /**
     * @var string $login_token
     */
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

    /**
     * private function login.
     * Sends an API call to https://api.hub-score.com/login_check and returns the response.
     * 
     * @param string $id
     * @param string $pwd
     * @param object $client
     * 
     * @return object
     */
    private function login(string $id, string $pwd, object $client): object
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

    /**
     * public function sendForm
     * Gets the form and the form configuration from the yaml file,
     * then does an API call to https://api.hub-score.com/v1/sends/mails.
     * 
     * @param object $client
     * @param array $form
     * 
     * @return array
     */
    public function sendForm($client, $form): array
    {
        $config = [];
        $fields = [];
        $messages = [];
        $userInfos = [];
        $email = '';
        $count = 1;
        foreach ($this->forms as $key => $value) {
            $str1 = strtolower(preg_replace("/[^a-z0-9]+/i", "", $form['title']));
            $str2 = strtolower(preg_replace("/[^a-z0-9]+/i", "", $key));
            // if the key in the yaml config file is equal to the validated form title
            if ($str1 === $str2) {
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
                if (isset($value['messages'])) {
                    $messages = $value['messages'];
                }
                break;
            }
        }

        $response = $client->request(
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
                    "overwriteUserInfos" => 1
                ]
            ]
        );

        return [
            "reponse" => $response,
            "messages" => $messages
        ];
    }

    /**
     * public function getResponse
     * 
     * @return object
     */
    public function getResponse(): object
    {
        return $this->response;
    }

    /**
     * public function getLoginToken
     * 
     * @return string
     */
    public function getLoginToken(): string
    {
        return $this->login_token;
    }
}
