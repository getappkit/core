<?php

namespace Appkit\Toolkit;

use Closure;
use Generator;

/**
 * Generate
 *
 * @package   Toolkit
 * @author    Maarten Thiebou
 * @copyright Modufolio
 * @license   https://opensource.org/licenses/MIT
 */
class Generate
{
    public static function factorial(int $count): Generator
    {
        $a = 1;
        for ($i = 1; $i <= $count; $i++) {
            $a *= $i;
            yield $a;
        }
    }

    public static function fibonacci(int $count): Generator
    {
        $a = 0;
        $b = 1;
        for ($i = 0; $i < $count; $i++) {
            yield $a;
            $temp = $a;
            $a = $b;
            $b = $temp + $b;
        }
    }

    public static function permutations(array $items): Generator
    {
        if (count($items) == 0) {
            yield [];
            return;
        }
        for ($i = 0; $i < count($items); $i++) {
            $copy = $items;
            $item = array_splice($copy, $i, 1);
            foreach (self::permutations($copy) as $perm) {
                yield array_merge($item, $perm);
            }
        }
    }
}
