<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $cities = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->findBy(array('enabled' => true), array('city' => 'ASC'));

        return $this->render('CalderaCriticalmassDesktopBundle:Default:index.html.twig');
    }

    public function slugindexAction($slug)
    {
        $city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($slug)->getCity();

        return $this->render('CalderaCriticalmassDesktopBundle:Default:index.html.twig', array('citySlug' => $city->getMainSlugString()));
    }
}
