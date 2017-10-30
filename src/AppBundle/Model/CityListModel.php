<?php

namespace AppBundle\Model;

use AppBundle\Entity\City;
use AppBundle\Entity\Ride;

class CityListModel
{
    /** @var City */
    protected $city;

    /** @var Ride */
    protected $currentRide;

    /** @var int $countRides */
    protected $countRides;

    /** @var array $cycles */
    protected $cycles;

    public function __construct(City $city, Ride $currentRide = null, array $cycles = [], int $countRides = 0)
    {
        $this->city = $city;
        $this->currentRide = $currentRide;
        $this->cycles = $cycles;
        $this->countRides = $countRides;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function getCurrentRide(): ?Ride
    {
        return $this->currentRide;
    }

    public function getCycles(): array
    {
        return $this->cycles;
    }

    public function countRides(): int
    {
        return $this->countRides;
    }
}
