<?php

namespace Appkit\Toolkit;

use InvalidArgumentException;
use stdClass;

/**
 * Super simple stdClass extension with
 * magic getter methods for all properties
 *
 * @package   Kirby Toolkit
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier GmbH
 * @license   https://opensource.org/licenses/MIT
 */
class Obj extends stdClass
{
    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $val) {
            $this->$key = $val;
        }
    }

    /**
     * Magic getter
     *
     * @param string $property
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $property, array $arguments)
    {
        return $this->$property ?? null;
    }

    /**
     * Improved `var_dump` output
     *
     * @return array
     */
    public function __debugInfo(): array
    {
        return $this->toArray();
    }

    /**
     * Magic property getter
     *
     * @param string $property
     * @return mixed
     */
    public function __get(string $property)
    {
        return null;
    }

    /**
     * Gets one or multiple properties of the object
     *
     * @param mixed $fallback If multiple properties are requested:
     *                        Associative array of fallback values per key
     */
    public function get(string|array $property, $fallback = null)
    {
        if (is_array($property)) {
            $fallback ??= [];

            if (is_array($fallback) === false) {
                throw new InvalidArgumentException('The fallback value must be an array when getting multiple properties');
            }

            $result = [];
            foreach ($property as $key) {
                $result[$key] = $this->$key ?? $fallback[$key] ?? null;
            }
            return $result;
        }

        return $this->$property ?? $fallback;
    }

    /**
     * Converts the object to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = [];

        foreach ((array)$this as $key => $value) {
            if (is_object($value) === true && method_exists($value, 'toArray')) {
                $result[$key] = $value->toArray();
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Converts the object to a json string
     *
     * @param mixed ...$arguments
     * @return string
     */
    public function toJson(...$arguments): string
    {
        return json_encode($this->toArray(), ...$arguments);
    }
}
