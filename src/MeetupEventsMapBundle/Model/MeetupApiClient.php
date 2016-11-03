<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 02/11/16
 * Time: 14:23
 */

namespace DaveHamber\Bundles\MeetupEventsMapBundle\Model;

use Circle\RestClientBundle\Services\RestInterface;
use \HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use \Symfony\Component\HttpFoundation\Response;

class MeetupApiClient
{
    private $restClient;

    private $accessToken;

    private $apiUri = "https://api.meetup.com/";

    private $findEventsEndpoint = "find/events";

    public function __construct(RestInterface $restClient, OAuthToken $token)
    {
        $this->restClient = $restClient;

        $this->accessToken = $token->getAccessToken();
    }

    public function findEvents()
    {
        $uri = $this->apiUri . $this->findEventsEndpoint . '?' . 'only=name';

        /**
         * @var \Symfony\Component\HttpFoundation\Response
         */
        $response = $this->restClient->get(
            $uri,
            array(
                CURLOPT_HTTPHEADER => array("Authorization: Bearer $this->accessToken")
            )
        );

        $httpCode = $response->getStatusCode();

        if (Response::HTTP_OK == $httpCode) {
            $eventData = json_decode($response->getContent());
        } else {
            $eventData = array();
        }

        return $eventData;
    }
}