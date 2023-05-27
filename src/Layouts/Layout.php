<?php

declare(strict_types=1);

namespace Appkit\Layouts;

use Appkit\Core\Roots;
use Appkit\Toolkit\Tpl;

/**
 * Layout
 *
 * @package   Kirby Layouts
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier
 * @license   MIT
 */
class Layout
{
    /**
     * @var string|null
     */
    public static $name = null;

    /**
     * @var array|null
     */
    public static $data = null;

    /**
     * Starts a new layout
     *
     * @param string|null $name
     * @param array|null $data
     * @return void
     */
    public static function start(?string $name = null, ?array $data = null): void
    {
        static::$name = $name ?? 'default';
        static::$data = $data ?? [];
    }

    /**
     * Renders a layout with all its slots
     *
     * @param array $data
     * @return string
     */
    public static function render(array $data = []): string
    {
        Slots::render();
        return Tpl::load(Roots::LAYOUTS . DS . static::$name . '.php', array_merge(Layout::$data ?? [], $data));
    }
}
