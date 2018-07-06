<?php declare(strict_types=1);

namespace AppBundle\EventSubscriber;

use AppBundle\Criticalmass\Gps\PolylineGenerator\ReducedPolylineGenerator;
use AppBundle\Entity\Track;
use AppBundle\Event\Track\TrackTrimmedEvent;
use AppBundle\Criticalmass\Gps\GpxReader\TrackReader;
use AppBundle\Criticalmass\Gps\LatLngListGenerator\RangeLatLngListGenerator;
use AppBundle\Criticalmass\Gps\PolylineGenerator\PolylineGenerator;
use AppBundle\Criticalmass\Statistic\RideEstimate\RideEstimateHandler;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TrackEventSubscriber implements EventSubscriberInterface
{
    /** @var TrackReader $trackReader */
    protected $trackReader;

    /** @var PolylineGenerator $polylineGenerator */
    protected $polylineGenerator;

    /** @var ReducedPolylineGenerator $reducedPolylineGenerator */
    protected $reducedPolylineGenerator;

    /** @var RangeLatLngListGenerator $rangeLatLngListGenerator */
    protected $rangeLatLngListGenerator;

    /** @var RideEstimateHandler $rideEstimateHandler */
    protected $rideEstimateHandler;

    /** @var Registry $registry */
    protected $registry;

    public function __construct(Registry $registry, RideEstimateHandler $rideEstimateHandler, TrackReader $trackReader, PolylineGenerator $polylineGenerator, ReducedPolylineGenerator $reducedPolylineGenerator, RangeLatLngListGenerator $rangeLatLngListGenerator)
    {
        $this->polylineGenerator = $polylineGenerator;

        $this->reducedPolylineGenerator = $reducedPolylineGenerator;

        $this->rangeLatLngListGenerator = $rangeLatLngListGenerator;

        $this->trackReader = $trackReader;

        $this->rideEstimateHandler = $rideEstimateHandler;

        $this->registry = $registry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TrackTrimmedEvent::NAME => 'onTrackTrimmed',
        ];
    }

    public function onTrackTrimmed(TrackTrimmedEvent $trackTrimmedEvent): void
    {
        $track = $trackTrimmedEvent->getTrack();

        $this->updatePolyline($track);

        $this->updateLatLngList($track);

        $this->updateTrackProperties($track);

        $this->updateEstimates($track);

        $this->registry->getManager()->flush();
    }

    protected function updatePolyline(Track $track): void
    {
        $polyline = $this->polylineGenerator
            ->loadTrack($track)
            ->execute()
            ->getPolyline();

        $track->setPolyline($polyline);

        $reducedPolyline = $this->reducedPolylineGenerator
            ->loadTrack($track)
            ->execute()
            ->getPolyline();

        $track->setReducedPolyline($reducedPolyline);
    }

    protected function updateLatLngList(Track $track): void
    {
        $this->rangeLatLngListGenerator
            ->loadTrack($track)
            ->execute();

        $track->setLatLngList($this->rangeLatLngListGenerator->getList());
    }

    protected function updateTrackProperties(Track $track): void
    {
        $this->trackReader->loadTrack($track);

        $track
            ->setStartDateTime($this->trackReader->getStartDateTime())
            ->setEndDateTime($this->trackReader->getEndDateTime())
            ->setDistance($this->trackReader->calculateDistance());
    }

    public function updateEstimates(Track $track): void
    {
        $this->rideEstimateHandler
            ->setRide($track->getRide())
            ->flushEstimates()
            ->addEstimateFromTrack($track);

        $this->rideEstimateHandler->calculateEstimates();
    }
}
