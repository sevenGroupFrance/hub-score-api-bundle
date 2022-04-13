<?php

namespace SevenGroupFrance\HubScoreApiBundle\EventSubscriber;


use SevenGroupFrance\HubScoreApiBundle\EventSubscriber\HubScoreApiCall;
use Doctrine\Common\EventSubscriber;
use Sulu\Bundle\FormBundle\Entity\Dynamic;
use Sulu\Bundle\FormBundle\Event\FormSavePostEvent;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HubScoreApi implements EventSubscriber
{
    private $id;
    private $pswd;
    private $client;
    public function __construct($id, $pswd, HttpClientInterface $client)
    {
        $this->id = $id;
        $this->pswd = $pswd;
        $this->client = $client;
    }

    public function getSubscribedEvents(): array
    {
        return [
            FormSavePostEvent::NAME => "hubAPI"
        ];
    }

    public function hubAPI(FormSavePostEvent $event): void
    {
        $dynamic = $event->getData();

        if (!$dynamic instanceof Dynamic) {
            return;
        }

        $form = $dynamic->getForm()->serializeForLocale($dynamic->getLocale(), $dynamic);
        if ($form) {
            $apiCall = new HubScoreApiCall();
            $response = $apiCall->login($this->id, $this->pswd, $this->client);
            $statusCode = $response->getStatusCode();
            $content = $response->getContent();
            $content = $response->toArray();

            $login_token = $content['token'];


            $email = $form['fields'][1]['value'];
            $firstName = $form['fields'][2]['value'];
            $lastName = $form['fields'][3]['value'];
            // $phone = $form['fields'][4]['value'];
            // $function = $form['fields'][5]['value'];
            // $company = $form['fields'][6]['value'];
            // $select = $form['fields'][7]['value'];
            $textarea = $form['fields'][8]['value'];

            if ($statusCode === 200 && $login_token) {
                $after_connect_response = $this->client->request(
                    'POST',
                    'https://api.hub-score.com/v1/sends/mails',
                    [
                        "headers" =>
                        [
                            'Authorization: Bearer ' . $login_token
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
                /* $after_connect_statusCode = $after_connect_response->getStatusCode(); */
            }
        }
    }
}
