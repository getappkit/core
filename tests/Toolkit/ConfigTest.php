<?php

namespace Toolkit;

use Appkit\Toolkit\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    protected function setUp(): void
    {
        Config::set('testvar', 'testvalue');
    }

    public function tearDown(): void
    {
        Config::$data = [];
    }

    public function testGet()
    {
        $this->assertSame('testvalue', Config::get('testvar'));
        $this->assertSame('defaultvalue', Config::get('nonexistentvar', 'defaultvalue'));
    }

    public function testSet()
    {
        Config::set('anothervar', 'anothervalue');
        Config::set('testvar', 'overwrittenvalue');

        $this->assertSame('anothervalue', Config::get('anothervar'));
        $this->assertSame('overwrittenvalue', Config::get('testvar'));

        Config::set([
            'var1' => 'value1',
            'var2' => 'value2'
        ]);

        $this->assertSame('value1', Config::get('var1'));
        $this->assertSame('value2', Config::get('var2'));
    }
}