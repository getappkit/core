<?php

namespace Appkit\Toolkit;

class Func
{
    static function compose(callable ...$fns): callable
    {
        $compose = static function ($composition, $fn) {
            return static function (...$args) use ($composition, $fn) {
                return null === $composition
                    ? $fn(...$args)
                    : $fn($composition(...$args));
            };
        };

        return self::reduce($compose, $fns);
    }

    static function pipe($data, callable ...$fns)
    {
        return self::compose(...$fns)($data);
    }

    static function reduce(callable $fn, iterable $coll, $initial = null)
    {
        $acc = $initial;

        foreach ($coll as $key => $value) {
            $acc = $fn($acc, $value, $key);
        }

        return $acc;
    }

}