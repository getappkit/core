<?php

namespace Toolkit;

use Appkit\Toolkit\Timer;
use PHPUnit\Framework\TestCase;

class TimerTest extends TestCase
{
    public function testGetTimerValue()
    {
        Timer::$timers['timer1'] = 1.23456789;

        $this->assertEquals('1234.57', Timer::get('timer1', 2));
    }

    public function testGetTimerValueWithDefaultDecimals()
    {
        Timer::$timers['timer2'] = 2.3456789;

        $this->assertEquals('2345.68', Timer::get('timer2'));
    }

    public function testGetNonexistentTimer()
    {
        $this->assertNull(Timer::get('nonexistent_timer'));
    }

    public function testStartTimer()
    {
        Timer::start('timer3');

        $this->assertArrayHasKey('timer3', Timer::$timers);
    }

    public function testResetTimer()
    {
        Timer::$timers['timer5'] = 5.4321;
        Timer::reset('timer5');

        $this->assertEquals(0, Timer::$timers['timer5']);
    }
}
