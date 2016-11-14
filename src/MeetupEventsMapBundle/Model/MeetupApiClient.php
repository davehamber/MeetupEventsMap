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

    public function findEvents($startDate = null, $endDate = null)
    {
        $uri = $this->apiUri . $this->findEventsEndpoint . '?' . 'only=name,venue,time,description';

        if ($endDate != null) {
            $compareDate = clone $endDate;
            $compareDate->modify("+1 day");
        }

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

        $eventData = [];
        if (Response::HTTP_OK == $httpCode) {

            if (isset($response->headers)) {

                $link = $response->headers->get('link');

                $link = $link;

                if ($link[0] == '<') {
                    $index = strpos($link, '>');
                    if (false !== $index) {
                        $uri = urldecode(substr($link, 1, $index - 1));
                    }
                }

                $queryString = parse_url($link, PHP_URL_QUERY);
                $parameters = [];
                parse_str($queryString, $parameters);

                if (isset($parameters['scroll'])) {
                    $scroll = $parameters['scroll'];
                    $commencementDate = substr($scroll, 6, 10);
                }

                $a = 0;

            }

            $decodedJsonEventData = json_decode($response->getContent());
            $decodedJsonEventData = array_unique($decodedJsonEventData, SORT_REGULAR);
            foreach ($decodedJsonEventData as $eventDataClass) {
                if (isset ($eventDataClass->name, $eventDataClass->venue,
                    $eventDataClass->time, $eventDataClass->time,
                    $eventDataClass->venue->lat, $eventDataClass->venue->lon) &&
                    !($eventDataClass->venue->lat == 0 && $eventDataClass->venue->lon == 0)
                ) {
                    if ($startDate != null && $endDate != null) {
                        $date = new \DateTime('@' . (int)($eventDataClass->time / 1000));

                        if ($date >= $startDate && $date < $compareDate) {
                            $eventData[] = $eventDataClass;
                        }
                    } else {
                        $eventData[] = $eventDataClass;
                    }
                }
            }
        }
        return $eventData;
    }
}