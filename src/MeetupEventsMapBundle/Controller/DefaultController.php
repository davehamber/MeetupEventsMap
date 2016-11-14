<?php

namespace DaveHamber\Bundles\MeetupEventsMapBundle\Controller;

use DaveHamber\Bundles\MeetupEventsMapBundle\Form\Type\DateRangeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MeetupEventsBundle:Default:index.html.twig');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testAction(Request $request)
    {
        $securityAuthorizationChecker = $this->container->get('security.authorization_checker');

        $form = $this->createForm(DateRangeType::class);

        $startDate = null;
        $endDate = null;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $startDate = $form->get('startDate')->getData();
                $endDate = $form->get('endDate')->getData();
            }
        }

        if ($securityAuthorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            $meetupAPIClient = $this->get('meetup_api_client');

            $eventData = $meetupAPIClient->findEvents($startDate, $endDate);
            $eventData = array_unique($eventData, SORT_REGULAR);
        } else {
            $eventData = array();
        }

        return $this->render('MeetupEventsBundle:Test:test.html.twig', array('event_data' => $eventData, 'form' => $form->createView()));
    }
}
