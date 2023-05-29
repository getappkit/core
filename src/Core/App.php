<?php

declare(strict_types=1);

namespace Appkit\Core;

use Appkit\Http\Emitter;
use Appkit\Http\Response;
use Appkit\Http\Stream;
use Appkit\Middleware\ErrorHandlerMiddleware;
use Appkit\Toolkit\Timer;
use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Fluent;
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
final class App implements RequestHandlerInterface
{
    protected static $instance;
    public const VERSION = '0.0.1';
    protected array $options;
    public ServerRequestInterface $request;
    private $middlewares;
    private $response;
    protected $container;
    protected $manager;

    public function __construct($config = [])
    {
        Timer::start('app');
        $this->bootApp($config);
        $this->bootEloquent();
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

    public function bootEloquent()
    {
        $this->setupContainer(new Container());
        $this->setupDefaultConfiguration();
        $this->setupManager();
        $this->addConnection($this->options['db']);
        Eloquent::setConnectionResolver($this->manager);
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
     * Get a registered connection instance.
     *
     * @param string|null $name
     * @return Connection
     */
    public function getConnection(?string $name = null): Connection
    {
        return $this->manager->connection($name);
    }

    /**
     * Register a connection with the manager.
     *
     * @param  array  $config
     * @param string $name
     * @return void
     */
    public function addConnection(array $config, string $name = 'default')
    {
        $connections = $this->container['config']['database.connections'];
        $connections[$name] = $config;
        $this->container['config']['database.connections'] = $connections;
    }

    /**
     * Get the database manager instance.
     */
    public function getDatabaseManager(): DatabaseManager
    {
        return $this->manager;
    }

    /**
     * Get the current event dispatcher instance.
     */
    public function getEventDispatcher(): ?Dispatcher
    {
        if ($this->container->bound('events')) {
            return $this->container['events'];
        }
        return null;
    }

    public function pages(): Pages
    {
        return new Pages();
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

    /**
     * Set the event dispatcher instance to be used by connections.
     *
     * @param Dispatcher $dispatcher
     * @return void
     */
    public function setEventDispatcher(Dispatcher $dispatcher)
    {
        $this->container->instance('events', $dispatcher);
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

    /**
     * Setup the IoC container instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return void
     */
    protected function setupContainer(\Illuminate\Contracts\Container\Container $container)
    {
        $this->container = $container;

        if (! $this->container->bound('config')) {
            $this->container->instance('config', new Fluent());
        }
    }

    /**
     * Setup the default database configuration options.
     *
     * @return void
     */
    protected function setupDefaultConfiguration()
    {
        $this->container['config']['database.default'] = 'default';
    }

    /**
     * Build the database manager instance.
     *
     * @return void
     */
    protected function setupManager()
    {
        $factory = new ConnectionFactory($this->container);
        $this->manager = new DatabaseManager($this->container, $factory);
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
