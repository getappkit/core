<?php

declare(strict_types=1);

namespace Appkit\Core;

/**
 * Roots
 *
 * @package   Appkit Core
 * @author    Maarten Thiebou
 * @copyright Modufolio
 * @license   https://opensource.org/licenses/MIT
 */
class Roots
{
    public const APP  = self::BASE . 'app';
    public const BASE = BASE_DIR . '/';
    public const CONFIG = self::BASE . 'config';
    public const CORE = self::BASE . 'Core';
    public const DATABASE = self::BASE . 'database';
    public const ERRORS = self::SITE. '/system';
    public const LAYOUTS = self::BASE . 'site/layouts';
    public const ROUTES = self::BASE . 'routes';
    public const SITE = self::BASE . 'site';
    public const SNIPPETS = self::BASE . 'site/snippets';
    public const STORAGE  = self::BASE . 'storage';
    public const TEMPLATES = self::BASE . 'site/templates';
    public const VIEWS = self::BASE . 'site/views';
}
