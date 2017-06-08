<?php

namespace AppBundle\Traits;

use AppBundle\Entity\Ride;
use AppBundle\Entity\Track;
use AppBundle\Gps\DistanceCalculator\TrackDistanceCalculator;
use AppBundle\Gps\GpxReader\TrackReader;
use AppBundle\Gps\LatLngListGenerator\RangeLatLngListGenerator;
use AppBundle\Gps\LatLngListGenerator\SimpleLatLngListGenerator;
use AppBundle\Gps\TrackPolyline\TrackPolyline;
use AppBundle\Statistic\RideEstimate\RideEstimateService;

trait TrackHandlingTrait
{
    protected function loadTrackProperties(Track $track)
    {
        /**
         * @var TrackReader $gr
         */
        $gr = $this->get('caldera.criticalmass.gps.trackreader');
        $gr->loadTrack($track);

        $track->setPoints($gr->countPoints());

        $track->setStartPoint(0);
        $track->setEndPoint($gr->countPoints() - 1);

        $track->setStartDateTime($gr->getStartDateTime());
        $track->setEndDateTime($gr->getEndDateTime());

        /**
         * @var TrackDistanceCalculator $tdc
         */
        $tdc = $this->get('caldera.criticalmass.gps.distancecalculator.track');
        $tdc->loadTrack($track);

        $track->setDistance($tdc->calculate());

        $track->setMd5Hash($gr->getMd5Hash());
    }

    protected function addRideEstimate(Track $track, Ride $ride)
    {
        /**
         * @var RideEstimateService $estimateService
         */
        $estimateService = $this->get('caldera.criticalmass.statistic.rideestimate.track');
        $estimateService->addEstimate($track);
        $estimateService->calculateEstimates($ride);
    }

    /**
     * @deprecated
     */
    protected function generateSimpleLatLngList(Track $track)
    {
        /**
         * @var SimpleLatLngListGenerator $generator
         */
        $generator = $this->get('caldera.criticalmass.gps.latlnglistgenerator.simple');
        $list = $generator
            ->loadTrack($track)
            ->execute()
            ->getList();

        $track->setLatLngList($list);

        $em = $this->getDoctrine()->getManager();
        $em->persist($track);
        $em->flush();
    }

    /**
     * @deprecated
     */
    protected function saveLatLngList(Track $track)
    {
        /**
         * @var RangeLatLngListGenerator $llag
         */
        $llag = $this->container->get('caldera.criticalmass.gps.latlnglistgenerator.range');
        $llag->loadTrack($track);
        $llag->execute();
        $track->setLatLngList($llag->getList());

        $em = $this->getDoctrine()->getManager();
        $em->persist($track);
        $em->flush();
    }

    protected function updateTrackProperties(Track $track)
    {
        /**
         * @var TrackReader $gr
         */
        $tr = $this->get('caldera.criticalmass.gps.trackreader');
        $tr->loadTrack($track);

        $track->setStartDateTime($tr->getStartDateTime());
        $track->setEndDateTime($tr->getEndDateTime());
        $track->setDistance($tr->calculateDistance());

        $em = $this->getDoctrine()->getManager();
        $em->persist($track);
        $em->flush();
    }

    protected function calculateRideEstimates(Track $track)
    {
        /**
         * @var RideEstimateService $res
         */
        $res = $this->get('caldera.criticalmass.statistic.rideestimate.track');
        $res->flushEstimates($track->getRide());

        $res->refreshEstimate($track->getRideEstimate());
        $res->calculateEstimates($track->getRide());
    }

    protected function generatePolyline(Track $track)
    {
        /**
         * @var TrackPolyline $trackPolyline
         */
        $trackPolyline = $this->get('caldera.criticalmass.gps.polyline.track');

        $trackPolyline->loadTrack($track);

        $trackPolyline->execute();

        $track->setPolyline($trackPolyline->getPolyline());

        $em = $this->getDoctrine()->getManager();
        $em->persist($track);
        $em->flush();
    }
}
