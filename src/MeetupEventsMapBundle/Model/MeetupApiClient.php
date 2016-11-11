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
        $uri = $this->apiUri . $this->findEventsEndpoint . '?' . 'only=name,venue,time,description';

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
            $decodedJsonEventData = json_decode($response->getContent());
            $decodedJsonEventData = array_unique($decodedJsonEventData, SORT_REGULAR);
            foreach ($decodedJsonEventData as $eventDataClass) {
                if (isset ($eventDataClass->name, $eventDataClass->venue) &&
                    isset($eventDataClass->time, $eventDataClass->time) &&
                    isset($eventDataClass->venue->lat, $eventDataClass->venue->lon) &&
                    !($eventDataClass->venue->lat == 0 && $eventDataClass->venue->lon == 0)
                ) {

                    $eventData[] = $eventDataClass;
                }
            }

        } else {
            $eventData = array();
        }
        $eventData = $eventData;
        return $eventData;
    }
}