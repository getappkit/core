<?php

declare(strict_types=1);

namespace Appkit\Core;

use Appkit\Toolkit\Config;
use Appkit\Toolkit\Tpl;
use Throwable;

/**
 * View
 *
 * @package   Appkit Core
 * @author    Maarten Thiebou
 * @copyright Modufolio
 * @license   https://opensource.org/licenses/MIT
 */
class View
{
    public static array $data = [];
    /**
     * Render a view file
     *
     * @param string $view The view file
     * @param array $args Associative array of data to display in the view (optional)
     *
     * @return string
     * @throws Throwable
     */
    public static function render(string $view, array $args = []): string
    {
        return Tpl::load(Roots::VIEWS . DS . $view . '.php', $args);
    }

    /**
     * Render a view snippet
     *
     * @param string $name
     * @param array $data
     * @return string|null
     * @throws Throwable
     */
    public static function snippet(string $name, array $data = []): ?string
    {
        $data = array_merge($data, static::$data);

        return Tpl::load(Roots::SNIPPETS . DS . $name . '.php', $data);
    }
}
View::$data['site'] = (object)Config::get('site');
