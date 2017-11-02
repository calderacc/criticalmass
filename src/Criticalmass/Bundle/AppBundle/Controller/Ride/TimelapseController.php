<?php

namespace AppBundle\Controller\Ride;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Track;
use AppBundle\Gps\LatLngListGenerator\TimeLatLngListGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TimelapseController extends AbstractController
{
    public function showAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $tracks = $this->getTrackRepository()->findTracksByRide($ride);

        return $this->render(
            'AppBundle:Timelapse:show.html.twig',
            array(
                'ride' => $ride,
                'tracks' => $tracks
            )
        );
    }

    public function loadtrackAction(Request $request, $citySlug, $rideDate, $trackId)
    {
        /**
         * @var Track $track
         */
        $track = $this->getTrackRepository()->find($trackId);

        /**
         * @var TimeLatLngListGenerator $generator
         */
        $generator = $this->get('caldera.criticalmass.gps.latlnglistgenerator.time');

        $generator->loadTrack($track);

        $generator->execute();

        $list = $generator->getList();

        return new Response($list, 200, ['Content-Type' => 'text/json']);
    }
}
