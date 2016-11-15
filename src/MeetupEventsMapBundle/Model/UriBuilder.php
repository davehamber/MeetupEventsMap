<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 14/11/16
 * Time: 12:10
 */

namespace DaveHamber\Bundles\MeetupEventsMapBundle\Model;


class UriBuilder
{
    const APIURI = "https://api.meetup.com/";

    const NAME = 'name';
    const VENUE = 'venue';
    const TIME = 'time';
    const DESCRIPTION = 'description';
    const ID = 'id';
    const GROUP = 'group';

    private $endPoint;

    /**
     * @var RequestParameter[]
     */
    private $requestParameters = [];

    public function __construct($endPoint)
    {
        $this->endPoint = $endPoint;
    }

    public function setEndPoint($endPoint)
    {
        $this->endPoint = $endPoint;

        return $this;
    }

    public function setRequestParameter($requestParameter)
    {
        if (false !== array_search($requestParameter, $this->requestParameters)) {
            return $this;
        }

        $this->requestParameters[] = $requestParameter;

        return $this;
    }

    public function getUri()
    {
        $uri = static::APIURI . $this->endPoint;

        if ($this->requestParameters == []) {
            return $uri;
        }

        list(, $requestParameter) = each($this->requestParameters);
        $uri .= '?' . $requestParameter->getRequestParameter();

        while (list(, $requestParameter) = each($this->requestParameters)) {
            $uri .= '&' . $requestParameter->getRequestParameter();
        }

        return $uri;
    }
}