<?php

namespace Toolkit;

use Appkit\Toolkit\Obj;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ObjTest extends TestCase
{
    /**
     * @covers \Appkit\Toolkit\Obj::__call
     */
    public function test__call()
    {
        $obj = new Obj(['foo' => 'bar']);
        $this->assertSame('bar', $obj->foo());
    }

    /**
     * @covers \Appkit\Toolkit\Obj::__get
     */
    public function test__get()
    {
        $obj = new Obj();
        $this->assertNull($obj->foo);
    }

    /**
     * @covers \Appkit\Toolkit\Obj::get
     */
    public function testGetMultiple()
    {
        $obj = new Obj([
            'one'   => 'first',
            'two'   => 'second',
            'three' => 'third'
        ]);

        $this->assertSame('first', $obj->get('one'));
        $this->assertSame(['one' => 'first', 'three' => 'third'], $obj->get(['one', 'three']));
        $this->assertSame([
            'one'   => 'first',
            'three' => 'third',
            'four'  => 'fallback',
            'eight' => null
        ], $obj->get(['one', 'three', 'four', 'eight'], ['four' => 'fallback']));
        $this->assertSame($obj->toArray(), $obj->get(['one', 'two', 'three']));
    }

    /**
     * @covers \Appkit\Toolkit\Obj::toArray
     */
    public function testToArray()
    {
        $obj = new Obj($expected = ['foo' => 'bar']);
        $this->assertSame($expected, $obj->toArray());
    }

    /**
     * @covers \Appkit\Toolkit\Obj::toArray
     */
    public function testToArrayWithChild()
    {
        $parent = new Obj([
            'child' => new Obj(['foo' => 'bar'])
        ]);

        $expected = [
            'child' => [
                'foo' => 'bar'
            ]
        ];

        $this->assertSame($expected, $parent->toArray());
    }

    /**
     * @covers \Appkit\Toolkit\Obj::toJson
     */
    public function testToJson()
    {
        $obj = new Obj($expected = ['foo' => 'bar']);
        $this->assertSame(json_encode($expected), $obj->toJson());
    }

    /**
     * @covers \Appkit\Toolkit\Obj::__debugInfo
     */
    public function test__debugInfo()
    {
        $obj = new Obj($expected = ['foo' => 'bar']);
        $this->assertSame($expected, $obj->__debugInfo());
    }
}
