<?php

namespace DaveHamber\Bundles\MeetupEventsMapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MeetupEventsBundle:Default:index.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testAction()
    {
        $securityAuthorizationChecker = $this->container->get('security.authorization_checker');

        if ($securityAuthorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            $meetupAPIClient = $this->get('meetup_api_client');

            $eventData = $meetupAPIClient->findEvents();
        } else {
            $eventData = array();
        }

        return $this->render('MeetupEventsBundle:Default:test.html.twig', array('event_data' => $eventData));
    }
}
