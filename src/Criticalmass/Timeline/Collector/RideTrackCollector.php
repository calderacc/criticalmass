<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Timeline\Collector;

use AppBundle\Entity\Track;
use AppBundle\Criticalmass\Timeline\Item\RideTrackItem;

class RideTrackCollector extends AbstractTimelineCollector
{
    protected $entityClass = Track::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var Track $trackEntity */
        foreach ($groupedEntities as $trackEntity) {
            $item = new RideTrackItem();

            $item
                ->setUser($trackEntity->getUser())
                ->setRide($trackEntity->getRide())
                ->setTrack($trackEntity)
                ->setRideTitle($trackEntity->getRide()->getTitle())
                ->setDistance($trackEntity->getDistance())
                ->setDuration($trackEntity->getDurationInSeconds())
                ->setPolyline($trackEntity->getPolyline())
                ->setPolylineColor('rgb(' . $trackEntity->getUser()->getColorRed() . ', ' . $trackEntity->getUser()->getColorGreen() . ', ' . $trackEntity->getUser()->getColorBlue() . ')')
                ->setDateTime($trackEntity->getCreationDateTime());

            $this->addItem($item);
        }

        return $this;
    }
}
