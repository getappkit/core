<?php

namespace Toolkit;


use Appkit\Toolkit\A;
use Appkit\Toolkit\Str;
use PHPUnit\Framework\TestCase;
use TypeError;

class ATest extends TestCase
{
    protected function _array()
    {
        return [
            'cat'  => 'miao',
            'dog'  => 'wuff',
            'bird' => 'tweet'
        ];
    }

    /**
     * @covers \Appkit\Toolkit\A::append
     */
    public function testAppend()
    {
        // associative
        $one    = ['a' => 'A', 'b' => 'B', 'c' => 'C'];
        $two    = ['d' => 'D', 'e' => 'E', 'f' => 'F'];
        $result = A::append($one, $two);
        $this->assertSame(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'E', 'f' => 'F'], $result);

        // numeric
        $one    = ['a', 'b', 'c'];
        $two    = ['d', 'e', 'f'];
        $result = A::append($one, $two);
        $this->assertSame(['a', 'b', 'c', 'd', 'e', 'f'], $result);

        // mixed
        $one    = ['a' => 'A', 'b' => 'B', 'c' => 'C'];
        $two    = ['d', 'e', 'f'];
        $result = A::append($one, $two);
        $this->assertSame(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd', 'e', 'f'], $result);
    }

    /**
     * @covers \Appkit\Toolkit\A::apply
     */
    public function testApply()
    {
        $array = [
            'level' => [
                'foo' => 'bar',
                'homer' => function () {
                    return 'simpson';
                }
            ],
            'a' => function ($b) {
                return $b;
            }
        ];

        $expected = [
            'level' => [
                'foo' => 'bar',
                'homer' => 'simpson'
            ],
            'a' => 'b'
        ];

        $this->assertSame($expected, A::apply($array, 'b'));
        $this->assertSame($expected, A::apply($array, 'b', 'c'));

        $array['a'] = function ($b, $c) {
            return $b . ' or ' . $c;
        };
        $expected['a'] = 'b or c';
        $this->assertSame($expected, A::apply($array, 'b', 'c'));
    }

    /**
     * @covers \Appkit\Toolkit\A::count
     */
    public function testCount()
    {
        $array = $this->_array();

        $this->assertSame(3, A::count($array));
        $this->assertSame(2, A::count(['cat', 'dog']));
        $this->assertSame(0, A::count([]));
    }

    /**
     * @covers \Appkit\Toolkit\A::contains
     */
    public function testContains()
    {
        // Haystack contains the needle
        $haystack = ['apple', 'banana', 'cherry', 'date', 'elderberry'];
        $needle = 'a';
        $expectedResult = ['apple', 'banana', 'date'];
        $this->assertEquals($expectedResult, A::contains($needle, $haystack));

        // Haystack doesn't contain the needle
        $needle = 'z';
        $this->assertNull(A::contains($needle, $haystack));

        // Empty haystack
        $haystack = [];
        $this->assertNull(A::contains($needle, $haystack));

        // Needle is a case-insensitive match
        $haystack = ['apple', 'banana', 'cherry', 'date', 'elderberry'];
        $needle = 'A';
        $expectedResult = ['apple', 'banana', 'date'];
        $this->assertEquals($expectedResult, A::contains($needle, $haystack));
    }

    /**
     * @covers \Appkit\Toolkit\A::dot
     */
    public function testDot()
    {
        // Arrange
        $inputArray = [
            'key1' => 'value1',
            'key2' => [
                'subkey1' => 'subvalue1',
                'subkey2' => [
                    'subsubkey1' => 'subsubvalue1',
                    'subsubkey2' => 'subsubvalue2',
                ],
            ],
            'key3' => 'value3',
        ];

        $expectedResult = [
            'key1' => 'value1',
            'key2.subkey1' => 'subvalue1',
            'key2.subkey2.subsubkey1' => 'subsubvalue1',
            'key2.subkey2.subsubkey2' => 'subsubvalue2',
            'key3' => 'value3',
        ];

        // Act
        $result = A::dot($inputArray);

        // Assert
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @covers \Appkit\Toolkit\A::divide
     */
    public function testDivide()
    {
        // Arrange
        $inputArray = ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3'];
        $expectedKeys = ['key1', 'key2', 'key3'];
        $expectedValues = ['value1', 'value2', 'value3'];

        // Act
        $result = A::divide($inputArray);
        $resultKeys = $result[0];
        $resultValues = $result[1];

        // Assert
        $this->assertEquals($expectedKeys, $resultKeys);
        $this->assertEquals($expectedValues, $resultValues);
    }

    /**
     * @covers \Appkit\Toolkit\A::duplicates
     */
    public function testDuplicates()
    {
        // Test with an array containing duplicates
        $array = ['apple', 'banana', 'apple', 'date', 'banana', 'kiwi'];
        $expectedResult = ['apple', 'banana'];
        $this->assertEquals($expectedResult, array_values(A::duplicates($array)));

        // Test with an array not containing duplicates
        $array = ['apple', 'banana', 'date', 'kiwi'];
        $expectedResult = [];
        $this->assertEquals($expectedResult, A::duplicates($array));

        // Test with an empty array
        $array = [];
        $expectedResult = [];
        $this->assertEquals($expectedResult, A::duplicates($array));
    }

    /**
     * @covers \Appkit\Toolkit\A::get
     */
    public function testGet()
    {
        $array = $this->_array();

        // single key
        $this->assertSame('miao', A::get($array, 'cat'));

        // multiple keys
        $this->assertSame([
            'cat'  => 'miao',
            'dog'  => 'wuff',
        ], A::get($array, ['cat', 'dog']));

        // null key
        $this->assertSame($array, A::get($array, null));

        // fallback value
        $this->assertNull(A::get($array, 'elephant'));
        $this->assertSame('toot', A::get($array, 'elephant', 'toot'));

        $this->assertSame([
            'cat' => 'miao',
            'elephant'  => null,
        ], A::get($array, ['cat', 'elephant']));

        $this->assertSame([
            'cat' => 'miao',
            'elephant'  => 'toot',
        ], A::get($array, ['cat', 'elephant'], 'toot'));
    }

    /**
     * @covers \Appkit\Toolkit\A::get
     */
    public function testGetWithDotNotation()
    {
        $data = [
            'grand.ma' => $grandma = [
                'mother' => $mother = [
                    'child' => $child = 'a',
                    'another.nested.child' => $anotherChild = 'b',
                ],
                'uncle.dot' => $uncle = 'uncle',
                'cousins' => [
                    ['name' => $cousinA = 'tick'],
                    ['name' => $cousinB = 'trick'],
                    ['name' => $cousinC = 'track'],
                ]
            ],
            'grand.ma.mother' => $anotherMother = 'another mother'
        ];

        $this->assertSame($grandma, A::get($data, 'grand.ma'));
        $this->assertSame($uncle, A::get($data, 'grand.ma.uncle.dot'));
        $this->assertSame($anotherMother, A::get($data, 'grand.ma.mother'));
        $this->assertSame($child, A::get($data, 'grand.ma.mother.child'));
        $this->assertSame($anotherChild, A::get($data, 'grand.ma.mother.another.nested.child'));
        $this->assertSame($cousinC, A::get($data, 'grand.ma.cousins.2.name'));

        // with default
        $this->assertSame('default', A::get($data, 'grand', 'default'));
        $this->assertSame('default', A::get($data, 'grand.grandaunt', 'default'));
        $this->assertSame('default', A::get($data, 'grand.ma.aunt', 'default'));
        $this->assertSame('default', A::get($data, 'grand.ma.uncle.dot.cousin', 'default'));
        $this->assertSame('default', A::get($data, 'grand.ma.mother.sister', 'default'));
        $this->assertSame('default', A::get($data, 'grand.ma.mother.child.grandchild', 'default'));
        $this->assertSame('default', A::get($data, 'grand.ma.mother.child.another.nested.sister', 'default'));
        $this->assertSame('default', A::get($data, 'grand.ma.cousins.4.name', 'default'));
    }

    /**
     * @covers \Appkit\Toolkit\A::get
     */
    public function testGetWithNonexistingOptions()
    {
        $data = [
            // 'alexander.the.great' => 'should not be fetched',
            'alexander' => 'not great yet'
        ];

        $this->assertNull(A::get($data, 'alexander.the.greate'));
        $this->assertSame('not great yet', A::get($data, 'alexander'));
    }

    /**
     * @covers \Appkit\Toolkit\A::groupBy
     */
    public function testGroupBy()
    {
        // Test with non-empty array
        $array = [
            ['color' => 'red', 'value' => 'apple'],
            ['color' => 'yellow', 'value' => 'banana'],
            ['color' => 'red', 'value' => 'cherry'],
            ['color' => 'yellow', 'value' => 'lemon']
        ];
        $key = 'color';
        $expectedResult = [
            'red' => [
                ['color' => 'red', 'value' => 'apple'],
                ['color' => 'red', 'value' => 'cherry']
            ],
            'yellow' => [
                ['color' => 'yellow', 'value' => 'banana'],
                ['color' => 'yellow', 'value' => 'lemon']
            ]
        ];
        $this->assertEquals($expectedResult, A::groupBy($key, $array));

        // Test with empty array
        $array = [];
        $expectedResult = [];
        $this->assertEquals($expectedResult, A::groupBy($key, $array));

        // Test with key that doesn't exist in the array
        $key = 'size';
        $array = [
            ['color' => 'red', 'value' => 'apple'],
            ['color' => 'yellow', 'value' => 'banana']
        ];
        $expectedResult = [];
        $this->assertEquals($expectedResult, A::groupBy($key, $array));
    }

    /**
     * @covers \Appkit\Toolkit\A::has
     */
    public function testHas()
    {
        $array = $this->_array();

        $this->assertTrue(A::has($array, 'miao'));
        $this->assertFalse(A::has($array, 'cat'));
        $this->assertFalse(A::has($array, 4));
        $this->assertFalse(A::has($array, ['miao']));
    }

    /**
     * @covers \Appkit\Toolkit\A::map
     */
    public function testMap()
    {
        $array = [
            'Peter', 'Bob', 'Mary'
        ];

        $expected = [
            ['name' => 'Peter'],
            ['name' => 'Bob'],
            ['name' => 'Mary']
        ];

        $this->assertSame($expected, A::map($array, function ($name) {
            return ['name' => $name];
        }));
    }

    public function testMapWithFunction()
    {
        $array    = [' A ', 'B ', ' C'];
        $expected = ['A', 'B', 'C'];

        $this->assertSame($expected, A::map($array, 'trim'));
    }

    public function testMapWithClassMethod()
    {
        $array    = ['a', 'b', 'c'];
        $expected = ['A', 'B', 'C'];

        $this->assertSame($expected, A::map($array, [Str::class, 'upper']));
    }

    /**
     * @covers \Appkit\Toolkit\A::merge
     */
    public function testMerge()
    {
        // simple non-associative arrays
        $a        = ['a', 'b'];
        $b        = ['c', 'd'];
        $expected = ['a', 'b', 'c', 'd'];
        $result   = A::merge($a, $b);
        $this->assertSame($expected, $result);

        $a        = ['a', 'b'];
        $b        = ['c', 'd', 'a'];
        $expected = ['a', 'b', 'c', 'd', 'a'];
        $result   = A::merge($a, $b);
        $this->assertSame($expected, $result);

        // simple associative arrays
        $a        = ['a' => 'b'];
        $b        = ['c' => 'd'];
        $expected = ['a' => 'b', 'c' => 'd'];
        $result   = A::merge($a, $b);
        $this->assertSame($expected, $result);

        $a        = ['a' => 'b'];
        $b        = ['a' => 'c'];
        $expected = ['a' => 'c'];
        $result   = A::merge($a, $b);
        $this->assertSame($expected, $result);

        // recursive merging
        $a        = ['a' => ['b', 'c']];
        $b        = ['a' => ['b', 'd']];
        $expected = ['a' => ['b', 'c', 'b', 'd']];
        $result   = A::merge($a, $b);
        $this->assertSame($expected, $result);

        $a        = ['a' => ['b' => 'c', 'd' => 'e']];
        $b        = ['a' => ['b' => 'd']];
        $expected = ['a' => ['b' => 'd', 'd' => 'e']];
        $result   = A::merge($a, $b);
        $this->assertSame($expected, $result);

        $a        = ['a' => 'b'];
        $b        = ['a' => ['c', 'd']];
        $expected = ['a' => ['c', 'd']];
        $result   = A::merge($a, $b);
        $this->assertSame($expected, $result);

        $a        = ['a' => ['c', 'd']];
        $b        = ['a' => 'b'];
        $expected = ['a' => 'b'];
        $result   = A::merge($a, $b);
        $this->assertSame($expected, $result);
    }

    /**
     * @covers \Appkit\Toolkit\A::merge
     */
    public function testMergeMultiples()
    {
        // simple non-associative arrays
        $a        = ['a', 'b'];
        $b        = ['c', 'd'];
        $c        = ['e', 'f'];
        $expected = ['a', 'b', 'c', 'd', 'e', 'f'];
        $result   = A::merge($a, $b, $c);
        $this->assertSame($expected, $result);

        // simple associative arrays
        $a        = ['a' => 'b'];
        $b        = ['c' => 'd'];
        $c        = ['e' => 'f'];
        $expected = ['a' => 'b', 'c' => 'd', 'e' => 'f'];
        $result   = A::merge($a, $b, $c);
        $this->assertSame($expected, $result);

        // recursive merging
        $a        = ['a' => ['b', 'c']];
        $b        = ['a' => ['b', 'd']];
        $c        = ['a' => ['e'], 'e' => 'f'];
        $expected = ['a' => ['b', 'c', 'b', 'd', 'e'], 'e' => 'f'];
        $result   = A::merge($a, $b, $c);
        $this->assertSame($expected, $result);
    }

    /**
     * @covers \Appkit\Toolkit\A::merge
     */
    public function testMergeModes()
    {
        // simple non-associative arrays
        $a        = [1 => 'a', 4 => 'b'];
        $b        = [1 => 'c', 3 => 'd', 5 => 'a'];

        // A::MERGE_APPEND
        $expected = ['a', 'b', 'c', 'd', 'a'];
        $result   = A::merge($a, $b);
        $this->assertSame($expected, $result);
        $result   = A::merge($a, $b);
        $this->assertSame($expected, $result, true);
        $result   = A::merge($a, $b, A::MERGE_APPEND);
        $this->assertSame($expected, $result);

        // A::MERGE_OVERWRITE
        $expected = [1 => 'c', 4 => 'b', 3 => 'd', 5 => 'a'];
        $result   = A::merge($a, $b, false);
        $this->assertSame($expected, $result);
        $result   = A::merge($a, $b, A::MERGE_OVERWRITE);
        $this->assertSame($expected, $result);


        // recursive merging
        $a        = ['a' => [1 => 'b', 4 => 'c']];
        $b        = ['a' => [1 => 'c', 3 => 'd', 5 => 'a']];

        // A::MERGE_APPEND
        $expected = ['a' => ['b', 'c', 'c', 'd', 'a']];
        $result   = A::merge($a, $b);
        $this->assertSame($expected, $result);
        $result   = A::merge($a, $b, true);
        $this->assertSame($expected, $result);
        $result   = A::merge($a, $b, A::MERGE_APPEND);
        $this->assertSame($expected, $result);

        // A::MERGE_OVERWRITE
        $expected = ['a' => [1 => 'c', 4 => 'c', 3 => 'd', 5 => 'a']];
        $result   = A::merge($a, $b, false);
        $this->assertSame($expected, $result);
        $result   = A::merge($a, $b, A::MERGE_OVERWRITE);
        $this->assertSame($expected, $result);


        // A::MERGE_REPLACE
        $a        = ['a' => ['a', 'b', 'c']];
        $b        = ['a' => ['d', 'e', 'f']];
        $expected = ['a' => ['d', 'e', 'f']];
        $result   = A::merge($a, $b, A::MERGE_REPLACE);
        $this->assertSame($expected, $result);
    }

    /**
     * @covers \Appkit\Toolkit\A::pluck
     */
    public function testPluck()
    {
        $array = [
            ['id' => 1, 'username' => 'bastian'],
            ['id' => 2, 'username' => 'sonja'],
            ['id' => 3, 'username' => 'lukas']
        ];

        $this->assertSame([
            'bastian',
            'sonja',
            'lukas'
        ], A::pluck($array, 'username'));
    }

    /**
     * @covers \Appkit\Toolkit\A::shuffle
     */
    public function testShuffle()
    {
        $array = $this->_array();
        $shuffled = A::shuffle($array);

        $this->assertSame($array['cat'], $shuffled['cat']);
        $this->assertSame($array['dog'], $shuffled['dog']);
        $this->assertSame($array['bird'], $shuffled['bird']);
    }

    /**
     * @covers \Appkit\Toolkit\A::reduce
     */
    public function testReduce()
    {
        $array = $this->_array();

        $reduced = A::reduce($array, function ($carry, $item) {
            return $carry . $item;
        }, '');
        $this->assertSame('miaowufftweet', $reduced);

        $reduced = A::reduce([1, 2, 3], function ($carry, $item) {
            return $carry + $item;
        }, 42);
        $this->assertSame(48, $reduced);

        $reduced = A::reduce([], function ($carry, $item) {
            return $carry + $item;
        });
        $this->assertSame(null, $reduced);
    }

    /**
     * @covers \Appkit\Toolkit\A::search
     */
    public function testSearch()
    {
        // The key exists at top level
        $array = ['apple' => 'fruit', 'carrot' => 'vegetable'];
        $key = 'apple';
        $expectedResult = 'fruit';
        $this->assertEquals($expectedResult, A::search($key, $array));

        // The key exists in nested array
        $array = ['fruits' => ['apple' => 'red', 'banana' => 'yellow'], 'vegetables' => ['carrot' => 'orange']];
        $key = 'banana';
        $expectedResult = 'yellow';
        $this->assertEquals($expectedResult, A::search($key, $array));

        // The key doesn't exist
        $key = 'pear';
        $this->assertNull(A::search($key, $array));

        // Empty array
        $array = [];
        $this->assertNull(A::search($key, $array));
    }

    /**
     * @covers \Appkit\Toolkit\A::slice
     */
    public function testSlice()
    {
        $array = $this->_array();

        $this->assertSame(['cat' => 'miao'], A::slice($array, 0, 1));
        $this->assertSame(['dog' => 'wuff', 'bird' => 'tweet'], A::slice($array, 1));
        $this->assertSame(['bird' => 'tweet'], A::slice($array, -1));
        $this->assertSame(['dog' => 'wuff'], A::slice($array, -2, 1));
        $this->assertSame($array, A::slice($array, 0));
    }

    /**
     * @covers \Appkit\Toolkit\A::sum
     */
    public function testSum()
    {
        $array = $this->_array();

        $this->assertSame(0, A::sum([]));
        $this->assertSame(6, A::sum([1, 2, 3]));
        $this->assertSame(6, A::sum([1, -1, 6]));
        $this->assertSame(6.0, A::sum([1.2, 2.4, 2.4]));
    }

    /**
     * @covers \Appkit\Toolkit\A::first
     */
    public function testFirst()
    {
        $this->assertSame('miao', A::first($this->_array()));
    }

    /**
     * @covers \Appkit\Toolkit\A::last
     */
    public function testLast()
    {
        $this->assertSame('tweet', A::last($this->_array()));
    }

    /**
     * @covers \Appkit\Toolkit\A::random
     */
    public function testRandom()
    {
        $array = $this->_array();
        $arrayKeys = array_flip(array_keys($array));
        $arrayValues = array_flip(array_values($array));

        // Assert existence and correctness of keys
        $random1 = A::random($array, 1);
        $this->assertTrue(in_array(array_values($random1)[0], $array));
        $this->assertTrue(array_key_exists(array_key_first($random1), $array));

        // Assert order of keys in non-shuffled random
        $random2 = A::random($array, 2);
        $this->assertTrue($arrayKeys[array_key_first($random2)] < $arrayKeys[array_key_last($random2)]);

        // Assert count in completely shuffled array
        $random3 = A::random($array, 3, true);
        $this->assertCount(3, $random3);
        foreach ($random3 as $key => $value) {
            $this->assertContains($key, array_keys($array));
            $this->assertContains($value, array_values($array));
            $this->assertSame($arrayKeys[$key], $arrayValues[$value]);
        }
    }

    /**
     * @covers \Appkit\Toolkit\A::fill
     */
    public function testFill()
    {
        $array = [
            'miao',
            'wuff',
            'tweet'
        ];

        // placholder
        $this->assertSame([
            'miao',
            'wuff',
            'tweet',
            'placeholder'
        ], A::fill($array, 4));

        // custom value
        $this->assertSame([
            'miao',
            'wuff',
            'tweet',
            'elephant',
            'elephant'
        ], A::fill($array, 5, 'elephant'));

        // Callable
        $this->assertSame([
            'miao',
            'wuff',
            'tweet',
            'elephant',
            'elephant',
            'elephant'
        ], A::fill($array, 6, fn () => 'elephant'));

        // Callable with Closure
        $this->assertSame([1, 2, 3], A::fill([], 3, fn (int $i) => $i + 1));
    }

    /**
     * @covers \Appkit\Toolkit\A::move
     */
    public function testMove()
    {
        $input = [
            'a',
            'b',
            'c',
            'd'
        ];

        $this->assertSame(['a', 'b', 'c', 'd'], A::move($input, 0, 0));
        $this->assertSame(['b', 'a', 'c', 'd'], A::move($input, 0, 1));
        $this->assertSame(['b', 'c', 'a', 'd'], A::move($input, 0, 2));
        $this->assertSame(['b', 'c', 'd', 'a'], A::move($input, 0, 3));

        $this->assertSame(['d', 'a', 'b', 'c'], A::move($input, 3, 0));
        $this->assertSame(['c', 'a', 'b', 'd'], A::move($input, 2, 0));
        $this->assertSame(['b', 'a', 'c', 'd'], A::move($input, 1, 0));
    }

    /**
     * @covers \Appkit\Toolkit\A::move
     */
    public function testMoveWithInvalidFrom()
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('Invalid "from" index');

        A::move(['a', 'b', 'c'], -1, 2);
    }

    /**
     * @covers \Appkit\Toolkit\A::move
     */
    public function testMoveWithInvalidTo()
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('Invalid "to" index');

        A::move(['a', 'b', 'c'], 0, 4);
    }

    /**
     * @covers \Appkit\Toolkit\A::missing
     */
    public function testMissing()
    {
        $required = ['cat', 'elephant'];

        $this->assertSame(['elephant'], A::missing($this->_array(), $required));
        $this->assertSame([], A::missing($this->_array(), ['cat']));
    }

    /**
     * @covers \Appkit\Toolkit\A::nest
     */
    public function testNest()
    {
        // simple example
        $input = [
            'a' => 'a value',
            'b.c' => [
                'd.e.f' => 'another value'
            ]
        ];
        $expected = [
            'a' => 'a value',
            'b' => [
                'c' => [
                    'd' => [
                        'e' => [
                            'f' => 'another value'
                        ]
                    ]
                ]
            ]
        ];
        $this->assertSame($expected, A::nest($input));

        // ignored key
        $input = [
            'a' => 'a value',
            'b' => 'another value',
            'b.c' => [
                'd.e.f' => 'a third value'
            ]
        ];
        $expected = $input;
        $this->assertSame($expected, A::nest($input, ['b']));

        // nested ignored key
        $expected = [
            'a' => 'a value',
            'b' => [
                'c' => [
                    'd.e.f' => 'a third value'
                ]
            ]
        ];
        $this->assertSame($expected, A::nest($input, ['b.c']));

        // ignored key with partially nested input
        $input = $expected;
        $this->assertSame($expected, A::nest($input, ['b.c']));

        // recursive array replacement
        $input = [
            // replace strings with arrays within deep structures
            'a' => 'this will be overwritten',
            'a.b' => [
                'c' => 'this as well',
                'd' => 'and this',
                'e' => 'but this will be preserved'
            ],
            'a.b.c' => 'a value',
            'a.b.d.f' => 'another value',

            // replace arrays with strings
            'g.h' => [
                'i' => 'this will be overwritten as well'
            ],
            'g' => 'and another value',

            // replacements within two different trees
            'j.k' => [
                'l' => 'this will be replaced',
                'm' => 'but this will not be'
            ],
            'j' => [
                'k.l' => 'a nice replacement',
                'n' => 'and this string is nice too'
            ]
        ];
        $expected = [
            'a' => [
                'b' => [
                    'c' => 'a value',
                    'd' => [
                        'f' => 'another value'
                    ],
                    'e' => 'but this will be preserved'
                ]
            ],
            'g' => 'and another value',
            'j' => [
                'k' => [
                    'l' => 'a nice replacement',
                    'm' => 'but this will not be'
                ],
                'n' => 'and this string is nice too'
            ]
        ];
        $this->assertSame($expected, A::nest($input));

        // merged arrays
        $input1 = [
            'a' => 'a-1',
            'b' => [
                'c' => 'b.c-1',
                'd' => 'b.d-1'
            ],
            'e.f' => [
                'g.h' => 'e.f.g.h-1',
                'g.i' => 'e.f.g.i-1'
            ],
            'l' => [
                'm' => 'l.m-1',
                'o.p' => 'l.o.p-1'
            ]
        ];
        $input2 = [
            'a' => 'a-2',
            'b.c' => 'b.c-2',
            'e' => [
                'f.g' => [
                    'h' => 'e.f.g.h-2',
                    'j' => 'e.f.g.j-2'
                ],
                'k' => 'e.k-2'
            ],
            'l' => [
                'm.n' => 'l.m.n-2',
                'o' => 'l.o-2'
            ]
        ];
        $expected = [
            'a' => 'a-2',
            'b' => [
                'c' => 'b.c-2',
                'd' => 'b.d-1'
            ],
            'e' => [
                'f' => [
                    'g' => [
                        'h' => 'e.f.g.h-2',
                        'i' => 'e.f.g.i-1',
                        'j' => 'e.f.g.j-2'
                    ]
                ],
                'k' => 'e.k-2'
            ],
            'l' => [
                'm' => 'l.m-1',
                'o.p' => 'l.o.p-1',
                'm.n' => 'l.m.n-2',
                'o' => 'l.o-2'
            ]
        ];
        $this->assertSame($expected, A::nest(array_replace_recursive($input1, $input2), ['l.m', 'l.o']));
        $this->assertSame($expected, A::nest(A::merge($input1, $input2, A::MERGE_REPLACE), ['l.m', 'l.o']));

        // with numeric keys
        $input = [
            'a' => 'a value',
            'b.2.e.f' => 'another value'
        ];
        $expected = [
            'a' => 'a value',
            'b' => [
                2 => [
                    'e' => [
                        'f' => 'another value'
                    ]
                ]
            ]
        ];
        $this->assertSame($expected, A::nest($input));
    }

    /**
     * @covers \Appkit\Toolkit\A::nestByKeys
     */
    public function testNestByKeys()
    {
        $this->assertSame('test', A::nestByKeys('test', []));
        $this->assertSame(['a' => 'test'], A::nestByKeys('test', ['a']));
        $this->assertSame(['a' => ['b' => 'test']], A::nestByKeys('test', ['a', 'b']));
    }
    /**
     * @covers \Appkit\Toolkit\A::startsWith
     */
    public function testStartsWith()
    {
        // Test with non-empty array and valid needle
        $haystack = ['apple', 'banana', 'date', 'apricot'];
        $needle = 'ap';
        $expectedResult = [0 => 'apple', 3 => 'apricot'];
        $this->assertEquals($expectedResult, A::startsWith($needle, $haystack));

        // Test with non-empty array and needle that doesn't exist in the array
        $needle = 'kiwi';
        $this->assertEquals(null, A::startsWith($needle, $haystack));

        // Test with empty array
        $haystack = [];
        $this->assertEquals(null, A::startsWith($needle, $haystack));
    }

    /**
     * @covers \Appkit\Toolkit\A::sort
     */
    public function testSort()
    {
        $array = [
            ['id' => 1, 'username' => 'bastian'],
            ['id' => 2, 'username' => 'sonja'],
            ['id' => 3, 'username' => 'lukas']
        ];

        // ASC
        $sorted = A::sort($array, 'username', 'asc');

        $this->assertSame(0, array_search('bastian', array_column($sorted, 'username')));
        $this->assertSame(2, array_search('sonja', array_column($sorted, 'username')));
        $this->assertSame(1, array_search('lukas', array_column($sorted, 'username')));

        // DESC
        $sorted = A::sort($array, 'username', 'desc');

        $this->assertSame(2, array_search('bastian', array_column($sorted, 'username')));
        $this->assertSame(0, array_search('sonja', array_column($sorted, 'username')));
        $this->assertSame(1, array_search('lukas', array_column($sorted, 'username')));

        //SORT_NATURAL
        $array = [
            ['file' => 'img12.png'],
            ['file' => 'img10.png'],
            ['file' => 'img2.png'],
            ['file' => 'img1.png']
        ];

        $regular = A::sort($array, 'file', 'asc');
        $natural = A::sort($array, 'file', 'asc', SORT_NATURAL);

        $this->assertSame(0, array_search('img1.png', array_column($regular, 'file')));
        $this->assertSame(1, array_search('img10.png', array_column($regular, 'file')));
        $this->assertSame(2, array_search('img12.png', array_column($regular, 'file')));
        $this->assertSame(3, array_search('img2.png', array_column($regular, 'file')));

        $this->assertSame(0, array_search('img1.png', array_column($natural, 'file')));
        $this->assertSame(1, array_search('img2.png', array_column($natural, 'file')));
        $this->assertSame(2, array_search('img10.png', array_column($natural, 'file')));
        $this->assertSame(3, array_search('img12.png', array_column($natural, 'file')));
    }

    /**
     * @covers \Appkit\Toolkit\A::isList
     */
    public function testIsList()
    {
        // Test with a sequential array
        $seqArray = ['red', 'yellow', 'green'];
        $this->assertTrue(A::isList($seqArray));

        // Test with an associative array
        $assocArray = ['apple' => 'red', 'banana' => 'yellow', 'kiwi' => 'green'];
        $this->assertFalse(A::isList($assocArray));

        // Test with an array with missing indices
        $missingIndicesArray = [0 => 'red', 2 => 'yellow', 3 => 'green'];
        $this->assertFalse(A::isList($missingIndicesArray));

        // Test with an empty array
        $emptyArray = [];
        $this->assertTrue(A::isList($emptyArray));
    }

    /**
     * @covers \Appkit\Toolkit\A::isAssociative
     */
    public function testIsAssociative()
    {
        $yes = $this->_array();
        $no = ['cat', 'dog', 'bird'];

        $this->assertTrue(A::isAssociative($yes));
        $this->assertFalse(A::isAssociative($no));
    }

    /**
     * @covers \Appkit\Toolkit\A::average
     */
    public function testAverage()
    {
        $array = [5, 2, 4, 7, 9.7];

        $this->assertSame(6.0, A::average($array));
        $this->assertSame(5.5, A::average($array, 1));
        $this->assertSame(5.54, A::average($array, 2));
        $this->assertNull(A::average([]));
    }

    /**
     * @covers \Appkit\Toolkit\A::extend
     */
    public function testExtend()
    {
        // simple
        $a = $this->_array();
        $b = [
            'elephant' => 'toot',
            'snake'    => 'zzz',
            'fox'      => 'what does the fox say?'
        ];

        $merged = [
            'cat'      => 'miao',
            'dog'      => 'wuff',
            'bird'     => 'tweet',
            'elephant' => 'toot',
            'snake'    => 'zzz',
            'fox'      => 'what does the fox say?'
        ];

        $this->assertSame($merged, A::extend($a, $b));

        // complex
        $a = [
            'verb'         => 'care',
            'prepositions' => ['not for', 'about', 'of']
        ];
        $b = [
            'prepositions' => ['for'],
            'object'       => 'others'
        ];

        $merged = [
            'verb'         => 'care',
            'prepositions' => ['not for', 'about', 'of', 'for'],
            'object'       => 'others'
        ];

        $this->assertSame($merged, A::extend($a, $b));
    }

    /**
     * @covers \Appkit\Toolkit\A::join
     */
    public function testJoin()
    {
        $array = ['a', 'b', 'c'];
        $this->assertSame('a, b, c', A::join($array));

        $array = ['a', 'b', 'c'];
        $this->assertSame('a/b/c', A::join($array, '/'));

        $this->assertSame('a/b/c', A::join('a/b/c'));
    }

    /**
     * @covers \Appkit\Toolkit\A::keyBy
     */
    public function testKeyBy()
    {
        $array = [
            [ 'id' => 1, 'username' => 'bastian'],
            [ 'id' => 2, 'username' => 'sonja'],
            [ 'id' => 3, 'username' => 'lukas']
        ];

        $array_by_id = [
            1 => [ 'id' => 1, 'username' => 'bastian'],
            2 => [ 'id' => 2, 'username' => 'sonja'],
            3 => [ 'id' => 3, 'username' => 'lukas']
        ];

        $array_by_name = [
            'bastian' => [ 'id' => 1, 'username' => 'bastian'],
            'sonja' => [ 'id' => 2, 'username' => 'sonja'],
            'lukas' => [ 'id' => 3, 'username' => 'lukas']
        ];

        $array_by_cb = [
            'bastian-1' => [ 'id' => 1, 'username' => 'bastian'],
            'sonja-2' => [ 'id' => 2, 'username' => 'sonja'],
            'lukas-3' => [ 'id' => 3, 'username' => 'lukas']
        ];

        $this->assertSame($array_by_id, A::keyBy($array, 'id'));
        $this->assertSame($array_by_name, A::keyBy($array, 'username'));
        $this->assertSame($array_by_cb, A::keyBy($array, function ($item) {
            return $item['username'] . '-' . $item['id'];
        }));

        // test with associative array
        $this->assertSame($array_by_id, A::keyBy($array_by_cb, 'id'));
    }

    /**
     * @covers \Appkit\Toolkit\A::keyBy
     */
    public function testKeyByWithNonexistentKeys()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The "key by" argument must be a valid key or a callable');

        $array = [
            [ 'id' => 1, 'username' => 'bastian'],
            [ 'id' => 2, 'username' => 'sonja'],
            [ 'id' => 3, 'username' => 'lukas']
        ];

        A::keyBy($array, 'nonexistent');
    }

    /**
     * @covers \Appkit\Toolkit\A::update
     */
    public function testUpdate()
    {
        $array = $this->_array();
        $updated = [
            'cat'  => 'meow',
            'dog'  => 'wuff',
            'bird' => 'tweet'
        ];

        // value
        $this->assertSame($updated, A::update($array, ['cat' => 'meow']));

        // callback
        $this->assertSame($updated, A::update($array, ['cat' => function ($value) {
            return 'meow';
        }]));
    }

    /**
     * @covers \Appkit\Toolkit\A::query
     */
    public function testQueryMethod()
    {
        // Arrange
        $input = ['name' => 'John Doe', 'email' => 'john@doe.com'];
        $expectedOutput = 'name=John%20Doe&email=john%40doe.com';

        // Act
        $output = A::query($input);

        // Assert
        $this->assertEquals($expectedOutput, $output);
    }

    /**
     * @covers \Appkit\Toolkit\A::query
     */
    public function testQueryMethodWithSpecialCharacters()
    {
        // Arrange
        $input = ['param' => '$special@characters#'];
        $expectedOutput = 'param=%24special%40characters%23';

        // Act
        $output = A::query($input);

        // Assert
        $this->assertEquals($expectedOutput, $output);
    }

    /**
     * @covers \Appkit\Toolkit\A::query
     */
    public function testQueryMethodWithEmptyArray()
    {
        // Arrange
        $input = [];
        $expectedOutput = '';

        // Act
        $output = A::query($input);

        // Assert
        $this->assertEquals($expectedOutput, $output);
    }

    /**
     * @covers \Appkit\Toolkit\A::wrap
     */
    public function testWrap()
    {
        $result = A::wrap($expected = ['a', 'b']);
        $this->assertSame($expected, $result);

        $result = A::wrap('a');
        $this->assertSame(['a'], $result);

        $result = A::wrap(null);
        $this->assertSame([], $result);
    }


    /**
     * @covers \Appkit\Toolkit\A::filter
     */
    public function testFilter()
    {
        $associativeArray = $this->_array();
        $indexedArray = array_keys($associativeArray);

        $result = A::filter($associativeArray, function ($value, $key) {
            return in_array($key, ['cat', 'dog']);
        });
        $this->assertSame(['cat'  => 'miao', 'dog'  => 'wuff'], $result);

        $result = A::filter($associativeArray, function ($value, $key) {
            return in_array($value, ['miao', 'tweet']);
        });
        $this->assertSame(['cat'  => 'miao', 'bird' => 'tweet'], $result);

        $result = A::filter($associativeArray, function ($value, $key) {
            return $key === 'cat' || $value === 'tweet';
        });
        $this->assertSame(['cat'  => 'miao', 'bird' => 'tweet'], $result);

        $result = A::filter($indexedArray, function ($value, $key) {
            return $key > 0;
        });
        $this->assertSame([1 => 'dog', 2 => 'bird'], $result);
    }

    /**
     * @covers \Appkit\Toolkit\A::unique
     */
    public function testUniqueMethodWithIntegers()
    {
        // Arrange
        $input = [1, 2, 2, 3, 4, 4, 5];
        $expectedOutput = [1, 2, 3, 4, 5];

        // Act
        $output = A::unique($input);

        // Assert
        $this->assertEquals($expectedOutput, array_values($output));
    }

    /**
     * @covers \Appkit\Toolkit\A::unique
     */
    public function testUniqueMethodWithStrings()
    {
        // Arrange
        $input = ["apple", "orange", "apple", "banana", "banana"];
        $expectedOutput = ["apple", "orange", "banana"];

        // Act
        $output = A::unique($input);

        // Assert
        $this->assertEquals($expectedOutput, array_values($output));
    }

    /**
     * @covers \Appkit\Toolkit\A::unique
     */
    public function testUniqueMethodWithEmptyArray()
    {
        // Arrange
        $input = [];
        $expectedOutput = [];

        // Act
        $output = A::unique($input);

        // Assert
        $this->assertEquals($expectedOutput, $output);
    }

    /**
     * @covers \Appkit\Toolkit\A::without
     */
    public function testWithout()
    {
        $associativeArray = $this->_array();
        $indexedArray = [...array_keys($associativeArray), ...array_keys($associativeArray)];

        $this->assertSame(['dog' => 'wuff', 'bird' => 'tweet'], A::without($associativeArray, 'cat'));
        $this->assertSame(['dog' => 'wuff'], A::without($associativeArray, ['cat', 'bird']));
        $this->assertSame([], A::without($associativeArray, ['cat', 'dog', 'bird']));
        $this->assertSame(['dog' => 'wuff', 'bird' => 'tweet'], A::without($associativeArray, ['this', 'cat', 'doesnt', 'exist']));

        $this->assertSame([0 => 'cat', 4 => 'dog', 5 => 'bird'], A::without($indexedArray, range(1, 3)));
        $this->assertSame([1 => 'dog', 2 => 'bird', 3 => 'cat', 4 => 'dog', 5 => 'bird'], A::without($indexedArray, 0));
        $this->assertSame(['cat', 'dog', 'bird', 'cat', 'dog', 'bird'], A::without($indexedArray, -1));
    }
}