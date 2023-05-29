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

        return Tpl::load(Roots::SNIPPETS . '/' . $name . '.php', $data);
    }
}
View::$data['site'] = (object)Config::get('site');
