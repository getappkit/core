<?php

declare(strict_types=1);

namespace Appkit\Http;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

interface ServerRequestCreatorInterface
{
    public function fromGlobals(): ServerRequestInterface;

    public function fromArrays(
        array  $server,
        array  $headers = [],
        array  $cookie = [],
        array  $get = [],
        ?array $post = null,
        array  $files = [],
        $body = null
    ): ServerRequestInterface;

    public static function getHeadersFromServer(array $server): array;
}
