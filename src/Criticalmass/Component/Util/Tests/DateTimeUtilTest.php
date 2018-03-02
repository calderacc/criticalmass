<?php declare(strict_types=1);

namespace Criticalmass\Component\Util\Tests;

use Criticalmass\Component\Util\DateTimeUtil;
use PHPUnit\Framework\TestCase;

class DateTimeUtilTest extends TestCase
{
    public function testDayStart(): void
    {
        $dateTime = new \DateTime('2011-06-24 19:15:00');

        $testDateTime = DateTimeUtil::getDayStartDateTime($dateTime);
        $expectedDateTime = new \DateTime('2011-06-24 00:00:00');

        $this->assertEquals($expectedDateTime, $testDateTime);
    }

    public function testDayEnd(): void
    {
        $dateTime = new \DateTime('2011-06-24 19:15:00');

        $testDateTime = DateTimeUtil::getDayEndDateTime($dateTime);
        $expectedDateTime = new \DateTime('2011-06-24 23:59:59');

        $this->assertEquals($expectedDateTime, $testDateTime);
    }

    public function testMonthStart(): void
    {
        $dateTime = new \DateTime('2011-06-24 19:15:00');

        $testDateTime = DateTimeUtil::getMonthStartDateTime($dateTime);
        $expectedDateTime = new \DateTime('2011-06-01 00:00:00');

        $this->assertEquals($expectedDateTime, $testDateTime);
    }

    public function testMonthEnd(): void
    {
        $dateTime = new \DateTime('2011-06-24 19:15:00');

        $testDateTime = DateTimeUtil::getMonthEndDateTime($dateTime);
        $expectedDateTime = new \DateTime('2011-06-30 23:59:59');

        $this->assertEquals($expectedDateTime, $testDateTime);
    }

    public function testYearStart(): void
    {
        $dateTime = new \DateTime('2011-06-24 19:15:00');

        $testDateTime = DateTimeUtil::getYearStartDateTime($dateTime);
        $expectedDateTime = new \DateTime('2011-01-01 00:00:00');

        $this->assertEquals($expectedDateTime, $testDateTime);
    }

    public function testYearEnd(): void
    {
        $dateTime = new \DateTime('2011-06-24 19:15:00');

        $testDateTime = DateTimeUtil::getYearEndDateTime($dateTime);
        $expectedDateTime = new \DateTime('2011-12-31 23:59:59');

        $this->assertEquals($expectedDateTime, $testDateTime);
    }
}
