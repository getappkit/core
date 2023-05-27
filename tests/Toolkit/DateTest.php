<?php

namespace Toolkit;

use Appkit\Toolkit\Date;
use Exception;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{

    public function testBeforeWithEqualDates()
    {
        $this->assertFalse(Date::before('2021-09-01', '2021-09-01'));
    }

    public function testBeforeWithDate1EarlierThanDate2()
    {
        $this->assertTrue(Date::before('2021-08-31', '2021-09-01'));
    }

    public function testBeforeWithDate1LaterThanDate2()
    {
        $this->assertFalse(Date::before('2021-09-02', '2021-09-01'));
    }

    public function testBeforeWithCustomFormat()
    {
        $this->assertTrue(Date::before('31/08/2021', '01/09/2021', 'd/m/Y'));
    }

    public function testConvert()
    {
        $this->assertEquals('2020-01-01', Date::format('01-01-2020', 'd-m-Y', 'Y-m-d'));
    }

    public function testDiffInDays()
    {
        $this->assertEquals(1, Date::diffInDays('2020-01-01', '2020-01-02'));
    }

    public function testFormat()
    {
        $this->assertEquals('2020-01-01', Date::format('01-01-2020', 'd-m-Y', 'Y-m-d'));
    }


    public function testIsValid()
    {
        $this->assertTrue(Date::isValid('2020-01-01'));
        $this->assertTrue(Date::isValid('01-01-2020'));
        $this->assertTrue(Date::isValid('01/01/2020'));
        $this->assertFalse(Date::isValid('2020-01-32'));
        $this->assertFalse(Date::isValid('0000-00-00'));
        $this->asserttrue(Date::isValid('June 2nd, 2022'));
    }

    public function testIsToday()
    {
        $this->assertTrue(Date::isToday(\date('Y-m-d')));

    }

    /**
     * @throws Exception
     */
    public function testRange()
    {
        $range = Date::range('2020-01-01', '2020-01-03');
        $this->assertInstanceOf('DatePeriod', $range);
        $this->assertCount(3, $range);
    }


}