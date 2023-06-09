<?php

declare(strict_types=1);

namespace Appkit\Layouts;

/**
 * Slots
 *
 * @package   Kirby Layouts
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier
 * @license   MIT
 */
class Slots
{
    /**
     * @var boolean
     */
    public static $render = false;

    /**
     * @var array
     */
    public static $slots = [];

    /**
     * @var array
     */
    public static $started = [];

    /**
     * Activate rendering mode
     *
     * @return void
     */
    public static function render(): void
    {
        static::$render = true;
    }

    /**
     * Starts a new slot
     *
     * @param string|null $name
     * @return void
     */
    public static function start(?string $name = 'content'): void
    {
        ob_start();

        static::$started[] = $name ?? 'content';

        if (static::$render) {
            return;
        }

        static::$slots[$name] = [
            'name'    => $name,
            'content' => null,
        ];
    }

    /**
     * Ends the currently started slot
     *
     * @return void
     */
    public static function end(): void
    {
        $slotName = array_pop(static::$started);
        $content  = ob_get_contents();
        ob_end_clean();

        if (static::$render === true) {
            echo static::$slots[$slotName]['content'] ?? $content;
        } else {
            static::$slots[$slotName]['content'] = $content;
        }
    }
}
