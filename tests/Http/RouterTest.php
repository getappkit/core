<?php

namespace Http;

use Appkit\Http\Route;
use Appkit\Http\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function testRegisterSingleRoute()
    {
        $router = new Router([
            [
                'pattern' => '/',
                'method'  => 'GET',
                'action'  => function () {
                }
            ]
        ]);

        $result = $router->find('/', 'GET');

        $this->assertInstanceOf(Route::class, $result);
        $this->assertSame('', $result->pattern());
        $this->assertSame('GET', $result->method());
    }

    public function testRegisterMultipleRoutes()
    {
        $router = new Router([
            [
                'pattern' => 'a',
                'method'  => 'GET',
                'action'  => function () {
                }
            ],
            [
                'pattern' => 'b',
                'method'  => 'POST',
                'action'  => function () {
                }
            ]
        ]);

        $resultA = $router->find('a', 'GET');
        $resultB = $router->find('b', 'POST');

        $this->assertInstanceOf(Route::class, $resultA);
        $this->assertSame('a', $resultA->pattern());
        $this->assertSame('GET', $resultA->method());

        $this->assertInstanceOf(Route::class, $resultB);
        $this->assertSame('b', $resultB->pattern());
        $this->assertSame('POST', $resultB->method());
    }

    public function testRegisterInvalidRoute()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Invalid route parameters');

        $router = new Router([
            'test' => 'test'
        ]);
    }

    public function testRegisterInvalidData()
    {
        $this->expectException('TypeError');

        $router = new Router('route');
    }

    public function testFindWithNonexistingMethod()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Invalid routing method: KIRBY');
        $this->expectExceptionCode(400);

        $router = new Router();
        $router->find('a', 'KIRBY');
    }

    public function testFindNonexistingRoute()
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('No route found for path: "a" and request method: "GET"');
        $this->expectExceptionCode(404);

        $router = new Router();
        $router->find('a', 'GET');
    }

    public function testNext()
    {
        $router = new Router([
            [
                'pattern' => '(:any)',
                'action'  => function ($slug) {
                    if ($slug === 'a') {
                        return 'a';
                    }

                    /** @var Route $this */
                    $this->next();
                }
            ],
            [
                'pattern' => '(:any)',
                'action'  => function ($slug) {
                    if ($slug === 'b') {
                        return 'b';
                    }

                    /** @var Route $this */
                    $this->next();
                }
            ],
            [
                'pattern' => '(:any)',
                'action'  => function ($slug) {
                    if ($slug === 'c') {
                        return 'c';
                    }

                    /** @var Route $this */
                    $this->next();
                }
            ]
        ]);

        $result = $router->call('a');
        $this->assertSame('a', $result);

        $result = $router->call('b');
        $this->assertSame('b', $result);

        $result = $router->call('c');
        $this->assertSame('c', $result);

        $this->expectException('Exception');
        $this->expectExceptionMessage('No route found for path: "d" and request method: "GET"');

        $result = $router->call('d');
    }

    public function testCallWithCallback()
    {
        $router = new Router([
            [
                'pattern' => '(:any)',
                'action'  => function ($slug) {
                    // does not really get called
                }
            ],
        ]);

        $phpunit = $this;
        $result  = $router->call('test', 'GET', function ($route) use ($phpunit) {
            $phpunit->assertInstanceOf(Route::class, $route);
            return $route->arguments()[0];
        });

        $this->assertSame('test', $result);
    }
}
