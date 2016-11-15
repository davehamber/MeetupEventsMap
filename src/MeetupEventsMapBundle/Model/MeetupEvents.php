<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 14/11/16
 * Time: 11:44
 */

namespace DaveHamber\Bundles\MeetupEventsMapBundle\Model;

use DateTime;
use DaveHamber\Bundles\MeetupEventsMapBundle\Model\UriBuilder;

class MeetupEvents
{
    const FIND_EVENTS_ENDPOINT = "find/events";
    const GET_EVENT_ENDPOINT = "/events/";
    const REQUEST_PARAMETER_ONLY = "only";
    const REQUEST_PARAMETER_SCROLL = "scroll";

    public function getFindEventsUri(DateTime $startDate)
    {
        $requestOnly = new RequestParameter(MeetupEvents::REQUEST_PARAMETER_ONLY);
        $requestOnly
            ->setValue(UriBuilder::NAME)
            ->setValue(UriBuilder::TIME)
            ->setValue(UriBuilder::ID)
            ->setValue(UriBuilder::GROUP)
            ->setValue(UriBuilder::VENUE);

        $requestScroll = new RequestParameter(MeetupEvents::REQUEST_PARAMETER_SCROLL);
        $requestScroll->setValue('since:' . $startDate->format('Y-m-d') . 'T00:00:00.000-05:00');

        $uri = new UriBuilder(MeetupEvents::FIND_EVENTS_ENDPOINT);

        $uri->setRequestParameter($requestOnly);
        $uri->setRequestParameter($requestScroll);

        return $uri->getUri();
    }

    public function getEvent($groupUrl, $eventId)
    {
        $requestOnly = new RequestParameter(MeetupEvents::REQUEST_PARAMETER_ONLY);
        $requestOnly
            ->setValue(UriBuilder::NAME)
            ->setValue(UriBuilder::TIME)
            ->setValue(UriBuilder::ID)
            ->setValue(UriBuilder::GROUP)
            ->setValue(UriBuilder::VENUE)
            ->setValue(UriBuilder::DESCRIPTION);

        $uri = new UriBuilder($groupUrl . MeetupEvents::GET_EVENT_ENDPOINT . $eventId);

        $uri->setRequestParameter($requestOnly);

        return $uri->getUri();
    }
}