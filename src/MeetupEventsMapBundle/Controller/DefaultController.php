<?php

namespace DaveHamber\Bundles\MeetupEventsMapBundle\Controller;

use DateTime;
use DaveHamber\Bundles\MeetupEventsMapBundle\Form\Type\DateRangeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $securityAuthorizationChecker = $this->container->get('security.authorization_checker');

        $form = $this->createForm(DateRangeType::class);

        $startDate = null;
        $endDate = null;

        $startDate = new DateTime('today midnight');
        $endDate = new DateTime('today midnight');

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $startDate = $form->get('startDate')->getData();
                $endDate = $form->get('endDate')->getData();
            }
        }

        if ($securityAuthorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            $meetupAPIClient = $this->get('meetup_api_client');

            $eventData = $meetupAPIClient->findEventsByDate($startDate, $endDate);
        } else {
            $eventData = array();
        }

        return $this->render('MeetupEventsBundle:Default:index.html.twig', array('event_data' => $eventData, 'form' => $form->createView()));
    }

    public function eventAction($groupUrl, $eventId)
    {
        $meetupAPIClient = $this->get('meetup_api_client');

        $eventData = $meetupAPIClient->getEvent($groupUrl, $eventId);

        return $this->render('MeetupEventsBundle:Event:event.html.twig', ['event_data' => $eventData]);
    }
}
