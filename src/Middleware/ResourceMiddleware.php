<?php

namespace Appkit\Middleware;


use Appkit\Toolkit\Config;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ResourceMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $resources  = Config::get('resources');
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();

        // If no resources are defined, pass the request to the next middleware
        if (empty($resources)) {
            return $handler->handle($request);
        }

        // Loop through the resources
        foreach ($resources as $baseUri => $resource) {
            foreach ($this->routes() as $route) {
                $pattern = '/' . $baseUri . $route['pattern'];
                $pattern = rtrim($pattern, '/');
                if ($method === $route['method'] && preg_match("#^$pattern$#", $path, $matches)) {
                    array_shift($matches);
                    if (is_callable($callback = [new $resource(), $route['action']])) {
                        return call_user_func_array($callback, array_merge([$request], $matches));
                    }
                }
            }
        }

        return $handler->handle($request);
    }

    private function routes(): array
    {
        return [
            [
                'pattern' => '/',
                'method' => 'GET',
                'action' => 'index'
            ],
            [
                'pattern' => '/',
                'method' => 'POST',
                'action' => 'store'
            ],
            [
                'pattern' => '/create',
                'method' => 'GET',
                'action' => 'create'
            ],
            [
                'pattern' => '/(\d+)',
                'method' => 'GET',
                'action' => 'show'
            ],
            [
                'pattern' => '/(\d+)/edit',
                'method' => 'GET',
                'action' => 'edit'
            ],
            [
                'pattern' => '/(\d+)',
                'method' => 'PUT',
                'action' => 'update'
            ],
            [
                'pattern' => '/(\d+)',
                'method' => 'DELETE',
                'action' => 'destroy'
            ],
        ];

    }
}
