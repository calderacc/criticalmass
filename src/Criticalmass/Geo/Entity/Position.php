<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Entity;

use App\Criticalmass\Geo\EntityInterface\PositionInterface;
use Caldera\GeoBasic\Coord\Coord;

class Position extends Coord implements PositionInterface
{
    /** @var float $accuracy */
    protected $accuracy;

    /** @var float $altitude */
    protected $altitude;

    /** @var float $altitudeAccuracy */
    protected $altitudeAccuracy;

    /** @var float $heading */
    protected $heading;

    /** @var float $speed */
    protected $speed;

    /** @var int $timestamp */
    protected $timestamp;

    /** @var \DateTime $dateTime */
    protected $dateTime;

    public function setLatitude(float $latitude): PositionInterface
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function setLongitude(float $longitude): PositionInterface
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function setAccuracy(float $accuracy): PositionInterface
    {
        $this->accuracy = $accuracy;

        return $this;
    }

    public function getAccuracy(): ?float
    {
        return $this->accuracy;
    }

    public function setAltitude(float $altitude): PositionInterface
    {
        $this->altitude = $altitude;

        return $this;
    }

    public function getAltitude(): ?float
    {
        return $this->altitude;
    }

    public function setAltitudeAccuracy(float $altitudeAccuracy): PositionInterface
    {
        $this->altitudeAccuracy = $altitudeAccuracy;

        return $this;
    }

    public function getAltitudeAccuracy(): ?float
    {
        return $this->altitudeAccuracy;
    }

    public function setHeading(float $heading): PositionInterface
    {
        $this->heading = $heading;

        return $this;
    }

    public function getHeading(): ?float
    {
        return $this->heading;
    }

    public function setSpeed(float $speed): PositionInterface
    {
        $this->speed = $speed;

        return $this;
    }

    public function getSpeed(): ?float
    {
        return $this->speed;
    }

    public function setTimestamp(int $timestamp): PositionInterface
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function setDateTime(\DateTime $creationDateTime): PositionInterface
    {
        $this->dateTime = $creationDateTime;

        return $this;
    }

    public function getDateTime(): ?\DateTime
    {
        return $this->dateTime;
    }
}
