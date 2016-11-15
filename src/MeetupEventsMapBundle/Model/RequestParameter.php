<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 14/11/16
 * Time: 12:35
 */

namespace DaveHamber\Bundles\MeetupEventsMapBundle\Model;


class RequestParameter
{
    private $name;

    private $values = [];

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function setValue($value)
    {
        if (false === array_search($value, $this->values)) {
            $this->values[] = $value;
        }

        return $this;
    }

    public function getRequestParameter()
    {
        return $this->name . '=' . implode(',', $this->values);
    }
}