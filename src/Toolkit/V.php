<?php

namespace Appkit\Toolkit;

use Countable;
use Exception;

/**
 * V
 *
 * @package   Toolkit
 * @author    Maarten Thiebou
 * @copyright Modufolio
 * @license   https://opensource.org/licenses/MIT
 */
class V
{
    public static array $methods = [
        'array' => 'is_array',
        'bool' => 'is_bool',
        'callable' => 'is_callable',
        'file' => 'is_file',
        'float' => 'is_float',
        'int' => 'is_int',
        'iterable' => 'is_iterable',
        'null' => 'is_null',
        'numeric' => 'is_numeric',
        'num' => 'is_numeric',
        'object' => 'is_object',
        'resource' => 'is_resource',
        'scalar' => 'is_scalar',
        'string' => 'is_string',
    ];

    /**
     * @throws Exception
     */
    public static function __callStatic(string $method, array $parameters)
    {
        // check for missing validators
        if (!array_key_exists($method, self::$methods)) {
            throw new Exception('The validator does not exist: ' . $method);
        }

        return call_user_func_array(self::$methods[$method], $parameters);
    }


    public static function date($date): bool
    {
        if (!is_string($date)) {
            return false;
        }

        $date = date_parse($date);
        return $date['error_count'] === 0 && $date['warning_count'] === 0;
    }

    public static function dateInterval(string $interval): bool
    {
        return self::match($interval, '/^P((\d+Y)?(\d+M)?(\d+D)?(T(\d+H)?(\d+M)?(\d+S)?)?|\d+W)$/');
    }

    public static function bit($value): bool
    {
        return ($value === 0 || $value === 1);
    }

    public static function blank($value): bool
    {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return false;
        }

        if ($value instanceof Countable) {
            return count($value) === 0;
        }

        return empty($value);
    }

    public static function camelCase(string $value): bool
    {
        return self::match($value, '/^[a-z]+(?:[A-Z][a-z]+)*$/');
    }


    public static function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function json($value): bool
    {
        if (!is_string($value) || $value === '') {
            return false;
        }

        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Checks if the given string is camelCase
     *
     * @param $value
     * @param $pattern
     * @return bool
     */
    public static function match($value, $pattern): bool
    {
        return preg_match($pattern, $value) === 1;
    }

    /**
     * Checks if the given string is a URL
     *
     * @param string|null $url
     * @return bool
     */
    public static function url(string $url = null): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    public static function xml(string $xml): bool
    {
        $prev = libxml_use_internal_errors(true);

        $doc = simplexml_load_string($xml);
        $errors = libxml_get_errors();

        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        if ($doc === false || !empty($errors)) {
            return false;
        }

        return true;
    }
}
