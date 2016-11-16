<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 02/11/16
 * Time: 14:23
 */

namespace DaveHamber\Bundles\MeetupEventsMapBundle\Model;

use Circle\RestClientBundle\Services\RestInterface;
use DateTime;
use \HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use \Symfony\Component\HttpFoundation\Response;

class MeetupApiClient
{
    const GET_SELF_ENDPOINT = "2/member/self";

    private $restClient;

    private $accessToken;

    private $curlOpts;

    public function __construct(RestInterface $restClient, OAuthToken $token)
    {
        $this->restClient = $restClient;

        $this->accessToken = $token->getAccessToken();

        $this->curlOpts =
            [
                CURLOPT_HTTPHEADER => ["Authorization: Bearer $this->accessToken"]
            ];
    }

    public function findEventsByDate(DateTime $startDate, DateTime $endDate)
    {
        $eventData = [];

        if ($startDate > $endDate) {
            return $eventData;
        }

        $meetupEvents = new MeetupEvents();

        list($eventData, $nextUri) =
            $this->fetchEventData($meetupEvents->getFindEventsUri($startDate));

        $endDay = clone $endDate;
        $endDay->modify("+1 day");

        list($eventData, $endDateReached) = $this->filterEventData($eventData, $startDate, $endDay);

        while (!$endDateReached && $nextUri) {
            list($nextEventData, $nextUri) =
                $this->fetchEventData($nextUri);

            list($nextEventData, $endDateReached) = $this->filterEventData($nextEventData, $startDate, $endDay);

            $eventData = array_merge($eventData, $nextEventData);

            if ($nextUri == null) {
                break;
            }
        }

        return $eventData;
    }

    public function getUserLonLat()
    {
        $requestOnly = new RequestParameter(MeetupEvents::REQUEST_PARAMETER_ONLY);
        $requestOnly
            ->setValue(UriBuilder::LON)
            ->setValue(UriBuilder::LAT);

        $uri = new UriBuilder(MeetupApiClient::GET_SELF_ENDPOINT);

        $uri->setRequestParameter($requestOnly);

        $response = $this->restClient->get(
            $uri->getUri(),
            $this->curlOpts
        );

        $httpCode = $response->getStatusCode();

        if (Response::HTTP_OK != $httpCode) {
            return [];
        }

        $lonLatData = json_decode($response->getContent(), true);

        return $lonLatData;
    }

    public function getEvent($groupUrl, $eventId)
    {
        $meetupEvents = new MeetupEvents();

        $eventData = [];
        /**
         * @var \Symfony\Component\HttpFoundation\Response
         */
        $response = $this->restClient->get(
            $meetupEvents->getEvent($groupUrl, $eventId),
            $this->curlOpts
        );

        $httpCode = $response->getStatusCode();

        if (Response::HTTP_OK != $httpCode) {
            return $eventData;
        }

        $eventData = json_decode($response->getContent());

        if (isset($eventData->time)) {
            $eventData->date = date("d-m-Y", $eventData->time / 1000);
            $eventData->time = date("H:i:s", $eventData->time / 1000);
        }

        return $eventData;
    }

    private function fetchEventData($uri)
    {
        $eventData = [];
        /**
         * @var \Symfony\Component\HttpFoundation\Response
         */
        $response = $this->restClient->get(
            $uri,
            $this->curlOpts
        );

        $httpCode = $response->getStatusCode();

        if (Response::HTTP_OK != $httpCode) {
            return [$eventData, null];
        }

        $headerLink = $response->headers->get('link');

        if (null != $headerLink) {
            $nextUri = $this->getNextUri($headerLink);
        } else {
            $nextUri = null;
        }

        $eventData = json_decode($response->getContent());
        $eventData = array_unique($eventData, SORT_REGULAR);

        return [$eventData, $nextUri];
    }

    private function filterEventData($eventData, DateTime $startDate, DateTime $endDate)
    {
        $filteredEventData = [];

        $endDateReached = false;

        foreach ($eventData as $eventDataRow) {
            if (!isset ($eventDataRow->name,
                    $eventDataRow->venue,
                    $eventDataRow->time,
                    $eventDataRow->time,
                    $eventDataRow->venue->lat,
                    $eventDataRow->venue->lon)) {
                continue;
            }

            if ($eventDataRow->venue->lat == 0 && $eventDataRow->venue->lon == 0) {
                continue;
            }

            $rowDate = new DateTime('@' . (int)($eventDataRow->time / 1000));

            if ($rowDate >= $startDate) {
                if ($rowDate <= $endDate) {
                    $filteredEventData[] = $eventDataRow;
                } else {
                    $endDateReached = true;
                }
            }
        }

        return [$filteredEventData, $endDateReached];
    }

    private function getNextUri($headerLink)
    {
        $uri = '';

        if ($headerLink[0] == '<') {
            $index = strpos($headerLink, '>');
            if (false !== $index) {
                $uri = urldecode(substr($headerLink, 1, $index - 1));
            }
        }

        return $uri;
    }
}