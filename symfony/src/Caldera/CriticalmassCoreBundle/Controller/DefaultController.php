<?php

namespace Caldera\CriticalmassCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CalderaCriticalmassCoreBundle:Default:index.html.twig', array('name' => $name));
    }
}
