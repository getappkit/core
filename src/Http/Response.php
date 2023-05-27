<?php

declare(strict_types=1);

namespace Appkit\Http;

use InvalidArgumentException;
use Psr\Http\Message\{ResponseInterface, StreamInterface};
use function is_int;
use function is_string;
use function sprintf;
use const ENT_QUOTES;

class Response implements ResponseInterface
{
    use MessageTrait;

    /** @var array Map of standard HTTP status code/reason phrases */
    private const PHRASES = [
        100 => 'Continue', 101 => 'Switching Protocols', 102 => 'Processing',
        200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content', 207 => 'Multi-status', 208 => 'Already Reported',
        300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', 306 => 'Switch Proxy', 307 => 'Temporary Redirect',
        400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Time-out', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Large', 415 => 'Unsupported Media Type', 416 => 'Requested range not satisfiable', 417 => 'Expectation Failed', 418 => 'I\'m a teapot', 422 => 'Unprocessable Entity', 423 => 'Locked', 424 => 'Failed Dependency', 425 => 'Unordered Collection', 426 => 'Upgrade Required', 428 => 'Precondition Required', 429 => 'Too Many Requests', 431 => 'Request Header Fields Too Large', 451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Time-out', 505 => 'HTTP Version not supported', 506 => 'Variant Also Negotiates', 507 => 'Insufficient Storage', 508 => 'Loop Detected', 511 => 'Network Authentication Required',
    ];

    /** @var string */
    private $reasonPhrase = '';

    /** @var int */
    private $statusCode;

    /**
     * @param int $status Status code
     * @param array $headers Response headers
     * @param string|resource|StreamInterface|null $body Response body
     * @param string $version Protocol version
     * @param string|null $reason Reason phrase (when empty a default will be used based on the status code)
     */
    public function __construct(int $status = 200, array $headers = [], $body = null, string $version = '1.1', string $reason = null)
    {
        // If we got no body, defer initialization of the stream until Response::getBody()
        if ('' !== $body && null !== $body) {
            $this->stream = Stream::create($body);
        }

        $this->statusCode = $status;
        $this->setHeaders($headers);
        if (null === $reason && isset(self::PHRASES[$this->statusCode])) {
            $this->reasonPhrase = self::PHRASES[$status];
        } else {
            $this->reasonPhrase = $reason ?? '';
        }

        $this->protocol = $version;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function withStatus($code, $reasonPhrase = ''): self
    {
        if (!is_int($code) && !is_string($code)) {
            throw new InvalidArgumentException('Status code has to be an integer');
        }

        $code = (int) $code;
        if ($code < 100 || $code > 599) {
            throw new InvalidArgumentException(sprintf('Status code has to be an integer between 100 and 599. A status code of %d was given', $code));
        }

        $new = clone $this;
        $new->statusCode = $code;
        if ((null === $reasonPhrase || '' === $reasonPhrase) && isset(self::PHRASES[$new->statusCode])) {
            $reasonPhrase = self::PHRASES[$new->statusCode];
        }
        $new->reasonPhrase = $reasonPhrase;

        return $new;
    }

    public static function empty(): Response
    {
        return new static(204, [], null);
    }

    public static function json(string|array $data, int $status = 200): Response
    {
        $body = is_array($data) ? json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) : $data;
        return new static($status, ['Content-Type' =>'application/json'], Stream::create($body));
    }

    public static function html(string $data, int $status = 200): Response
    {
        return new static($status, ['Content-Type' =>'text/html'], Stream::create($data));
    }

    public static function redirect(string $url, int $status = 302): Response
    {
        if (!in_array($status, [301, 302], true)) {
            throw new InvalidArgumentException('The redirect status code must be 301 or 302');
        }
        return new static($status, ['Location' => $url], (
            sprintf('<!DOCTYPE html>
            <html>
                <head>
                    <meta charset="UTF-8" />
                    <meta http-equiv="refresh" content="0;url=\'%1$s\'" />
                    <title>Redirecting to %1$s</title>
                </head>
                <body>
                    Redirecting to <a href="%1$s">%1$s</a>.
                </body>
            </html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8'))
        ));
    }
}
