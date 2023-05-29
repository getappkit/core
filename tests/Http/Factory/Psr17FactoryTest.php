<?php

declare(strict_types=1);
namespace Http\Factory;

use Appkit\Http\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class Psr17FactoryTest extends TestCase
{
    protected $factory;

    protected function setUp(): void
    {
        $this->factory = new Psr17Factory();
    }

    public function testCreateRequest()
    {
        $request = $this->factory->createRequest('GET', 'http://example.com');

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('http://example.com', (string) $request->getUri());
    }


    public function testCreateResponse()
    {

        $r = $this->factory->createResponse(200);
        $this->assertEquals('OK', $r->getReasonPhrase());

        $r = $this->factory->createResponse(200, '');
        $this->assertEquals('', $r->getReasonPhrase());

        $r = $this->factory->createResponse(200, 'Foo');
        $this->assertEquals('Foo', $r->getReasonPhrase());

        /*
         * Test for non-standard response codes
         */
        $r = $this->factory->createResponse(567);
        $this->assertEquals('', $r->getReasonPhrase());

        $r = $this->factory->createResponse(567, '');
        $this->assertEquals(567, $r->getStatusCode());
        $this->assertEquals('', $r->getReasonPhrase());

        $r = $this->factory->createResponse(567, 'Foo');
        $this->assertEquals(567, $r->getStatusCode());
        $this->assertEquals('Foo', $r->getReasonPhrase());
    }
}