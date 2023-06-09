<?php

namespace Toolkit;

use Appkit\Toolkit\Silo;
use PHPUnit\Framework\TestCase;

class SiloTest extends TestCase
{
    public function setUp(): void
    {
        Silo::$data = [];
    }

    /**
     * @covers \Appkit\Toolkit\Silo::get
     * @covers \Appkit\Toolkit\Silo::set
     */
    public function testSetAndGet()
    {
        Silo::set('foo', 'bar');
        $this->assertSame('bar', Silo::get('foo'));
    }

    /**
     * @covers \Appkit\Toolkit\Silo::set
     */
    public function testSetArray()
    {
        Silo::set([
            'a' => 'A',
            'b' => 'B'
        ]);

        $this->assertSame(['a' => 'A', 'b' => 'B'], Silo::get());
    }

    /**
     * @covers \Appkit\Toolkit\Silo::get
     */
    public function testGetArray()
    {
        Silo::set('a', 'A');
        Silo::set('b', 'B');

        $this->assertSame(['a' => 'A', 'b' => 'B'], Silo::get());
    }

    /**
     * @covers \Appkit\Toolkit\Silo::remove
     */
    public function testRemoveByKey()
    {
        Silo::set('a', 'A');
        $this->assertSame('A', Silo::get('a'));
        Silo::remove('a');
        $this->assertNull(Silo::get('a'));
    }

    /**
     * @covers \Appkit\Toolkit\Silo::remove
     */
    public function testRemoveAll()
    {
        Silo::set('a', 'A');
        Silo::set('b', 'B');
        $this->assertSame('A', Silo::get('a'));
        $this->assertSame('B', Silo::get('b'));
        Silo::remove();
        $this->assertNull(Silo::get('a'));
        $this->assertNull(Silo::get('b'));
    }
}
