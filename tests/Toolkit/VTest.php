<?php

namespace Toolkit;

use Appkit\Toolkit\V;
use PHPUnit\Framework\TestCase;

class VTest extends TestCase
{

    public function testBit()
    {
        $this->assertTrue(V::bit(0));
        $this->assertTrue(V::bit(1));
        $this->assertFalse(V::bit(2));
        $this->assertFalse(V::bit(3));
    }

    public function testBlank()
    {
        $this->assertTrue(V::blank(null));
        $this->assertTrue(V::blank(''));
        $this->assertTrue(V::blank(' '));
        $this->assertTrue(V::blank('  '));
    }

    public function testCamelCase()
    {
        // Valid camel case strings
        $this->assertTrue(V::camelCase('firstName'));
        $this->assertTrue(V::camelCase('firstNameLastName'));
        $this->assertTrue(V::camelCase('firstNameMiddleNameLastName'));

        // Invalid camel case strings
        $this->assertFalse(V::camelCase('firstName last name'));
        $this->assertFalse(V::camelCase('firstName MiddleName lastName'));
        $this->assertFalse(V::camelCase('firstName middleName lastName'));
        $this->assertFalse(V::camelCase('FirstNameLastName'));
    }


    public function testDate()
    {
        $this->assertTrue(V::date('2020-01-01'));
        $this->assertTrue(V::date('01-01-2020'));
        $this->assertTrue(V::date('01/01/2020'));
        $this->asserttrue(V::date('June 2nd, 2022'));
        $this->assertFalse(V::date('2020-01-32'));
        $this->assertFalse(V::date('0000-00-00'));
    }

    public function testDateInterval()
    {
        $this->assertTrue(V::dateInterval('P1D'));
        $this->assertTrue(V::dateInterval('P1Y'));
        $this->assertTrue(V::dateInterval('P1M'));
        $this->assertTrue(V::dateInterval('P1W'));
        $this->assertTrue(V::dateInterval('P1DT1H'));
        $this->assertTrue(V::dateInterval('P1DT1M'));
        $this->assertTrue(V::dateInterval('P1DT1S'));
        $this->assertTrue(V::dateInterval('P1DT1H1M1S'));
        $this->assertFalse(V::dateInterval('P1DT1H1M1S1'));
        $this->assertFalse(V::dateInterval('P1Y6X'));

    }

    public function testEmail()
    {
        $this->assertTrue(V::email('example@example.com'));
        $this->assertTrue(V::email('john.doe@example.com'));
        $this->assertTrue(V::email('jane+doe@example.com'));
        $this->assertFalse(V::email('example@'));
        $this->assertFalse(V::email('example@example'));
        $this->assertFalse(V::email('example@.com'));
        $this->assertFalse(V::email('example@ex..com'));
        $this->assertFalse(V::email('example@example..com'));
    }



    public function testJson()
    {
        $this->assertTrue(V::json('{"name":"John", "age":30, "city":"New York"}'));
        $this->assertFalse(V::json('{"John", "age":30, "city":"New York"}'));
    }

    public function testMatch()
    {
        $this->assertTrue(v::match('hello', '/^h/'));
        $this->assertTrue(v::match('world', '/^w/'));
        $this->assertTrue(v::match('123', '/^\d+$/'));
        $this->assertFalse(v::match('foo', '/^b/'));
        $this->assertFalse(v::match('bar', '/^f/'));
        $this->assertFalse(v::match('abc123', '/^\d+$/'));
    }

    public function testUrl()
    {
        // Valid URLs
        $this->assertTrue(V::url('https://www.example.com'));
        $this->assertTrue(V::url('https://example.com/test'));
        $this->assertTrue(V::url('http://www.example.com'));
        $this->assertTrue(V::url('http://example.com/test'));
        $this->assertTrue(V::url('ftp://example.com'));
        $this->assertTrue(V::url('ftps://example.com'));
        $this->assertTrue(V::url('http://www.example.com?test=value'));
        $this->assertTrue(V::url('http://www.example.com#test'));
        $this->assertTrue(V::url('https://www.example.com:8080'));

        // Invalid URLs
        $this->assertFalse(V::url('www.example.com'));
        $this->assertFalse(V::url('example.com'));
        $this->assertFalse(V::url('example'));
        $this->assertFalse(V::url('https://'));
        $this->assertFalse(V::url('http://'));
        $this->assertFalse(V::url('ftp://'));
        $this->assertFalse(V::url('ftps://'));
        $this->assertFalse(V::url('http://www.example.com test'));
    }

    public function testXml()
    {
        $this->assertTrue(V::xml('<root><element>text</element></root>'));
        $this->assertFalse(V::xml('<root><element>text</elements></root>'));
    }
}
