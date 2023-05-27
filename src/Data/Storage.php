<?php

declare(strict_types=1);

namespace Appkit\Data;

use Appkit\Toolkit\A;
use Exception;

/**
 * Dispatch
 *
 * @package   Appkit Data
 * @author    Maarten Thiebou
 * @copyright Modufolio
 * @license   https://opensource.org/licenses/MIT
 */
class Storage
{
    public string $filePath;
    public array $data = [];

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->data = include $this->filePath;
    }

    public function insert($key, $value = null): Storage
    {
        if (! is_array($key)) {
            $this->data[$key] = $value;
            return $this;
        }

        $this->data = array_merge($this->data, $key);
        return $this;
    }

    /**
     * @throws Exception
     */
    public function save(): Storage
    {
        PHP::write($this->filePath, $this->data);
        return $this;
    }



    /**
     * @param string|array $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return $this->data;
        }

        $col = array_column($this->data, $key);
        if (count($col) > 0) {
            return $col;
        }

        return A::get($this->data, $key, $default);
    }

    /**
     * Removes an item from the data array
     *
     * @param string|null $key
     * @return array
     */
    public function remove(string $key = null): array
    {
        // reset the entire array
        if ($key === null) {
            return $this->data = [];
        }

        // unset a single key
        unset($this->data[$key]);

        // return the array without the removed key
        return $this->data;
    }
}
