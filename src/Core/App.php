<?php

declare(strict_types=1);

namespace Appkit\Core;

use Appkit\Http\Emitter;
use Appkit\Http\Response;
use Appkit\Http\Stream;
use Appkit\Middleware\ErrorHandlerMiddleware;
use Appkit\Toolkit\Timer;
use Closure;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

/**
 * App
 *
 * @package   Appkit Core
 * @author    Maarten Thiebou
 * @copyright Modufolio
 * @license   https://opensource.org/licenses/MIT
 */
class App implements RequestHandlerInterface
{
    protected static $instance;
    public const VERSION = '0.0.6';
    protected array $options;
    public ServerRequestInterface $request;
    private $middlewares;
    private $response;

    public function __construct($config = [])
    {
        Timer::start('app');
        $this->bootApp($config);
        App::$instance = $this;
    }

    public function add(string| MiddlewareInterface $middleware): App
    {
        $middleware = is_string($middleware) ? new $middleware() : $middleware;
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function bootApp($config): void
    {
        $this->setupApp($config);
        error_reporting($this->debug() ? E_ALL : 0);
        ini_set('display_errors', $this->debug() ? '1' : '0');
        ini_set('display_startup_errors', $this->debug() ? '1' : '0');

        if ($this->debug()) {
            $this->add(ErrorHandlerMiddleware::class);
        }
    }

    public function debug(): bool
    {
        return $this->options['debug'] ?? false;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = array_shift($this->middlewares);

        return $middleware ? $middleware->process($request, $this) : new Response(204, [], Stream::create('No Content'));
    }

    public static function instance(): App
    {
        if (self::$instance == null) {
            self::$instance = new App();
        }
        return self::$instance;
    }


    /**
     * @throws Throwable
     */
    public function response(): ResponseInterface
    {
        return $this->response;
    }

    public function request(): ServerRequestInterface
    {
        return $this->request;
    }

    private function setupApp(array $setup): void
    {
        foreach ($setup as $key => $value) {
            if (!property_exists($this, $key)) {
                continue;
            }
            $this->{$key} = $value instanceof Closure ? $value() : $value;
        }
    }

    public function run(): void
    {
        if (empty($this->middlewares) === true) {
            throw new InvalidArgumentException('Can\'t run, no middleware found');
        }

        $response = $this->handle($this->request());
        $this->response = $response;
        Timer::stop('app');
        $emitter = new Emitter();
        $emitter->emit($this->debug() ? $response->withHeader('App', Timer::get('app') . ' ms') : $response);
    }

    public function url(string $path = ''): string
    {
        $uri = $this->request()->getUri();
        $scheme = $uri->getScheme();
        $host = $uri->getHost();
        $port = $uri->getPort();

        $baseUrl = $scheme . '://' . $host;

        if (($scheme === 'http' && $port != 80) || ($scheme === 'https' && $port != 443)) {
            $baseUrl .= ':' . $port;
        }

        return $path === '' ? $baseUrl : $baseUrl . '/' . $path;
    }
}
