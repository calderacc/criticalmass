<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CityController extends Controller
{
    public function listAction()
    {
        $cities = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->findBy(array('enabled' => true), array('city' => 'ASC'));

        return $this->render('CalderaCriticalmassDesktopBundle:City:list.html.twig', array('cities' => $cities));
    }

    public function showAction($citySlug)
    {
        $city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug)->getCity();

        if (!$city->getEnabled())
        {
            throw new NotFoundHttpException('Wir konnten keine Stadt unter der Bezeichnung "'.$citySlug.'" finden :(');
        }

        $currentRide = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findOneBy(array('city' => $city->getId()), array('dateTime' => 'DESC'));
        $rides = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findBy(array('city' => $city->getId()), array('dateTime' => 'DESC'));

        // shift the first ride from the array as the first one is the current and should not be displayed at the recent rides list
        array_shift($rides);

        return $this->render('CalderaCriticalmassDesktopBundle:City:show.html.twig', array('city' => $city, 'currentRide' => $currentRide, 'rides' => $rides));
    }
}
