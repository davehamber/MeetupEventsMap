<?php

namespace DaveHamber\Bundles\MeetupEventsMapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MeetupEventsBundle:Default:index.html.twig');
    }
}
