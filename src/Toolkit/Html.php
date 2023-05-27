<?php

namespace Appkit\Toolkit;

use Exception;

/**
 * HTML builder for the most common elements
 *
 * @package   Kirby Toolkit
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier GmbH
 * @license   https://opensource.org/licenses/MIT
 */
class Html extends Xml
{
    /**
     * An internal store for an HTML entities translation table
     *
     * @var array
     */
    public static $entities;

    /**
     * Closing string for void tags;
     * can be used to switch to trailing slashes if required
     *
     * ```php
     * Html::$void = ' />'
     * ```
     *
     * @var string
     */
    public static $void = '>';

    /**
     * List of HTML tags that are considered to be self-closing
     *
     * @var array
     */
    public static $voidList = [
        'area',
        'base',
        'br',
        'col',
        'command',
        'embed',
        'hr',
        'img',
        'input',
        'keygen',
        'link',
        'meta',
        'param',
        'source',
        'track',
        'wbr'
    ];

    /**
     * Generic HTML tag generator
     * Can be called like `Html::p('A paragraph', ['class' => 'text'])`
     *
     * @param string $tag Tag name
     * @param array $arguments Further arguments for the Html::tag() method
     * @return string
     */
    public static function __callStatic(string $tag, array $arguments = []): string
    {
        if (static::isVoid($tag) === true) {
            return static::tag($tag, null, ...$arguments);
        }

        return static::tag($tag, ...$arguments);
    }

    /**
     * Generates a single attribute or a list of attributes
     *
     * @param string|array $name String: A single attribute with that name will be generated.
     *                           Key-value array: A list of attributes will be generated. Don't pass a second argument in that case.
     * @param mixed $value If used with a `$name` string, pass the value of the attribute here.
     *                     If used with a `$name` array, this can be set to `false` to disable attribute sorting.
     * @return string|null The generated HTML attributes string
     */
    public static function attr($name, $value = null): ?string
    {
        // HTML supports boolean attributes without values
        if (is_array($name) === false && is_bool($value) === true) {
            return $value === true ? strtolower($name) : null;
        }

        // all other cases can share the XML variant
        $attr = parent::attr($name, $value);

        // HTML supports named entities
        $entities = parent::entities();
        $html = array_keys($entities);
        $xml = array_values($entities);
        return str_replace($xml, $html, $attr);
    }

    /**
     * Converts lines in a string into HTML breaks
     *
     * @param string $string
     * @return string
     */
    public static function breaks(string $string): string
    {
        return nl2br($string);
    }

    /**
     * Converts a string to an HTML-safe string
     *
     * @param string|null $string
     * @param bool $keepTags If true, existing tags won't be escaped
     * @return string The HTML string
     *
     * @psalm-suppress ParamNameMismatch
     */
    public static function encode(?string $string, bool $keepTags = false): string
    {
        if ($string === null) {
            return '';
        }

        if ($keepTags === true) {
            $list = static::entities();
            unset($list['"'], $list['<'], $list['>'], $list['&']);

            $search = array_keys($list);
            $values = array_values($list);

            return str_replace($search, $values, $string);
        }

        return htmlentities($string, ENT_COMPAT, 'utf-8');
    }

    /**
     * Returns the entity translation table
     *
     * @return array
     */
    public static function entities(): array
    {
        return self::$entities = self::$entities ?? get_html_translation_table(HTML_ENTITIES);
    }

    /**
     * Checks if a tag is self-closing
     *
     * @param string $tag
     * @return bool
     */
    public static function isVoid(string $tag): bool
    {
        return in_array(strtolower($tag), static::$voidList);
    }


    /**
     * Builds an HTML tag
     *
     * @param string $name Tag name
     * @param array|string $content Scalar value or array with multiple lines of content; self-closing
     *                              tags are generated automatically based on the `Html::isVoid()` list
     * @param array $attr An associative array with additional attributes for the tag
     * @param string|null $indent Indentation string, defaults to two spaces or `null` for output on one line
     * @param int $level Indentation level
     * @return string The generated HTML
     */
    public static function tag(string $name, $content = '', array $attr = null, string $indent = null, int $level = 0): string
    {
        // treat an explicit `null` value as an empty tag
        // as void tags are already covered below
        if ($content === null) {
            $content = '';
        }

        // force void elements to be self-closing
        if (static::isVoid($name) === true) {
            $content = null;
        }

        return parent::tag($name, $content, $attr, $indent, $level);
    }


    /**
     * Properly encodes tag contents
     *
     * @param mixed $value
     * @return string|null
     */
    public static function value($value): ?string
    {
        if ($value === true) {
            return 'true';
        }

        if ($value === false) {
            return 'false';
        }

        if (is_numeric($value) === true) {
            return (string)$value;
        }

        if ($value === null || $value === '') {
            return null;
        }

        return static::encode($value, false);
    }
}
