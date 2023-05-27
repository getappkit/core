<?php

declare(strict_types=1);

namespace Appkit\Layouts;

use Appkit\Core\Roots;
use Appkit\Toolkit\Tpl;

/**
 * Template
 *
 * @package   Kirby Layouts
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier
 * @license   MIT
 */
class Template
{
    public static array $data = [];
    protected string $name;

    public function __construct(string $name)
    {
        $this->name = strtolower($name);
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function exists(): bool
    {
        if ($file = $this->file()) {
            return file_exists($file);
        }

        return false;
    }

    public function file(): string|null
    {
        return Roots::TEMPLATES . DS . $this->name() . '.php';
    }

    public function name(): string
    {
        return $this->name;
    }

    public function render(array $data = []): string
    {
        // load the template
        $template = Tpl::load($this->file(), $data);

        if (Layout::$name === null) {
            return $template;
        }

        // set the default content slot if no slots exist
        if (empty(Slots::$slots) === true) {
            Slots::$slots['content'] = [
                'name'    => 'content',
                'content' => $template
            ];
        }

        return Layout::render($data);
    }
}
