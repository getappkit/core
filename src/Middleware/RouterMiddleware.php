<?php

declare(strict_types=1);

namespace Appkit\Middleware;

use Appkit\Core\Load;
use Appkit\Core\Roots;
use Appkit\Http\Response;
use Appkit\Http\Router;
use Appkit\Http\Stream;
use Appkit\Toolkit\Config;
use Appkit\Toolkit\Tpl;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @package   Appkit
 * @author    Maarten Thiebou
 * @copyright Modufolio
 * @license   https://opensource.org/licenses/MIT
 *
 */
class RouterMiddleware implements MiddlewareInterface
{
    protected array $routes;

    public function __construct()
    {
        $this->routes = Load::routes('web.php');
    }


    public function io($input): ResponseInterface
    {
        // Simple HTML response
        if (is_string($input) === true) {
            return Response::html($input);
        }

        // array to json conversion
        if (is_array($input) === true) {
            return Response::json($input);
        }

        // Response Configuration
        if (is_a($input, '\Appkit\Http\Response') === true) {
            return $input;
        }

        return Response::empty();
    }

    public function pageNotFound(ServerRequestInterface $request): Response
    {
        $file = Roots::ERRORS . DS . '404.php';
        return (new Response(404, ['Content-Type' =>'text/html'], Stream::create(Tpl::load($file))));
    }


    /**
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $response  = $this->io((new Router($this->routes))->call($request->getUri()->getPath(), $request->getMethod()));
        } catch (Exception $e) {
            if (Config::get('debug') === true) {
                throw $e;
            }
            $response = $this->pageNotFound($request);
        }

        return $response;
    }
}
