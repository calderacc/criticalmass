<?php declare(strict_types=1);

namespace Tests\Profile;

use AppBundle\Entity\Ride;
use AppBundle\Criticalmass\Profile\Streak\StreakCalculator;
use PHPUnit\Framework\TestCase;

class StreakTest extends TestCase
{
    protected function createRide(\DateTime $dateTime): Ride
    {
        $ride = new Ride();
        $ride->setDateTime($dateTime);

        return $ride;
    }

    public function testLongestStreak(): void
    {
        $streakCalculator = new StreakCalculator();

        $streakCalculator
            ->addRide($this->createRide(new \DateTime('2011-06-24 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2011-08-26 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2011-09-30 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2011-10-28 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2011-11-25 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2011-12-30 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2012-04-27 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2012-05-25 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2012-06-29 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2012-07-27 00:00:00')));

        $streak = $streakCalculator->calculateLongestStreak();

        $this->assertEquals(new \DateTime('2011-08-01 00:00:00'), $streak->getStartDateTime());
        $this->assertEquals(new \DateTime('2011-12-01 00:00:00'), $streak->getEndDateTime());
        $this->assertEquals(5, count($streak->getRideList()));
    }

    public function testLongestStreakReversed(): void
    {
        $streakCalculator = new StreakCalculator();

        $streakCalculator
            ->addRide($this->createRide(new \DateTime('2011-06-24 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2011-08-26 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2011-09-30 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2011-10-28 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2011-11-25 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2011-12-30 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2012-04-27 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2012-05-25 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2012-06-29 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2012-07-27 00:00:00')));

        $streak = $streakCalculator->calculateLongestStreak();

        $this->assertEquals(new \DateTime('2011-08-01 00:00:00'), $streak->getStartDateTime());
        $this->assertEquals(new \DateTime('2011-12-01 00:00:00'), $streak->getEndDateTime());
        $this->assertEquals(5, count($streak->getRideList()));
    }

    public function testCurrentStreak(): void
    {
        $streakCalculator = new StreakCalculator();

        $streakCalculator
            ->addRide($this->createRide(new \DateTime('2011-06-24 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2011-08-26 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2011-09-30 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2011-10-28 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2011-11-25 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2011-12-30 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2012-04-27 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2012-05-25 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2012-06-29 00:00:00')))
            ->addRide($this->createRide(new \DateTime('2012-07-27 00:00:00')));

        $streak = $streakCalculator->calculateCurrentStreak(new \DateTime('2012-07-30 00:00:00'));

        $this->assertEquals(new \DateTime('2012-04-01 00:00:00'), $streak->getStartDateTime());
        $this->assertEquals(new \DateTime('2012-07-01 00:00:00'), $streak->getEndDateTime());
        $this->assertEquals(4, count($streak->getRideList()));
    }
}
