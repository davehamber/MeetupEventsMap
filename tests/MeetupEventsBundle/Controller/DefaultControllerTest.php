<?php

namespace DaveHamber\Bundles\MeetupEventsMapBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertContains('Log', $client->getResponse()->getContent());
    }
}
