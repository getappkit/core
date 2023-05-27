<?php

declare(strict_types=1);

namespace Appkit\Http\Factory;

use Http\Message\{MessageFactory, StreamFactory, UriFactory};
use Appkit\Http\{Request, Response, Stream, Uri};
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class HttplugFactory implements MessageFactory, StreamFactory, UriFactory
{
    public function createRequest($method, $uri, array $headers = [], $body = null, $protocolVersion = '1.1'): RequestInterface
    {
        return new Request($method, $uri, $headers, $body, $protocolVersion);
    }

    public function createResponse($statusCode = 200, $reasonPhrase = null, array $headers = [], $body = null, $version = '1.1'): ResponseInterface
    {
        return new Response((int) $statusCode, $headers, $body, $version, $reasonPhrase);
    }

    public function createStream($body = null): StreamInterface
    {
        return Stream::create($body ?? '');
    }

    public function createUri($uri = ''): UriInterface
    {
        if ($uri instanceof UriInterface) {
            return $uri;
        }

        return new Uri($uri);
    }
}
