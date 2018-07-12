<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Geocoding\LocationBuilder;

use AppBundle\Criticalmass\Geocoding\LocationBuilderStrategy\LocationBuilderStrategyInterface;
use AppBundle\Criticalmass\Geocoding\ReverseGeocodeable;
use Geocoder\Location;

class LocationBuilder implements LocationBuilderInterface
{
    /** @var LocationBuilderStrategyInterface $locationBuilderStrategy */
    protected $locationBuilderStrategy;

    public function __construct(LocationBuilderStrategyInterface $locationBuilderStrategy)
    {
        $this->locationBuilderStrategy = $locationBuilderStrategy;
    }

    public function build(ReverseGeocodeable $reverseGeocodeable, Location $location): ReverseGeocodeable
    {
        $reverseGeocodeable->setLocation($this->locationBuilderStrategy->buildLocation($location));

        return $reverseGeocodeable;
    }
}
