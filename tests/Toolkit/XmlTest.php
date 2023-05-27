<?php

namespace Toolkit;


use Appkit\Toolkit\Xml;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Appkit\Toolkit\Xml
 */
class XmlTest extends TestCase
{

    /**
     * @covers ::attr
     */
    public function testAttrArrayValue()
    {
        $result = Xml::attr('a', ['a', 'b']);
        $this->assertSame('a="a b"', $result);

        $result = Xml::attr('a', ['a', 1]);
        $this->assertSame('a="a 1"', $result);

        $result = Xml::attr('a', ['a', null]);
        $this->assertSame('a="a"', $result);

        $result = Xml::attr('a', ['value' => '&', 'escape' => true]);
        $this->assertSame('a="&#38;"', $result);

        $result = Xml::attr('a', ['value' => '&', 'escape' => false]);
        $this->assertSame('a="&"', $result);
    }

    /**
     * @covers ::parse
     * @covers ::simplify
     * @covers ::create
     */

    /**
     * @covers ::encode
     * @covers ::decode
     */
    public function testEncodeDecode()
    {
        $expected = 'S&#252;per &#214;nenc&#339;ded &#223;tring';
        $this->assertSame($expected, Xml::encode('Süper Önencœded ßtring'));
        $this->assertSame('Süper Önencœded ßtring', Xml::decode($expected));

        $this->assertSame('S&#252;per Täst', Xml::encode('S&uuml;per Täst', false));

        $this->assertSame('', Xml::decode(''));
        $this->assertSame('', Xml::encode(''));
        $this->assertSame('', Xml::decode(null));
        $this->assertSame('', Xml::encode(null));
    }

    /**
     * @covers ::entities
     */
    public function testEntities()
    {
        $this->assertSame(Xml::$entities, Xml::entities());
    }

    /**
     * @covers ::tag
     */
    public function testTag()
    {
        $tag = Xml::tag('name', 'content');
        $this->assertSame('<name>content</name>', $tag);

        $tag = Xml::tag('name', 'content', [], '  ', 1);
        $this->assertSame('  <name>content</name>', $tag);

        $tag = Xml::tag('name', 'content', ['foo' => 'bar']);
        $this->assertSame('<name foo="bar">content</name>', $tag);

        $tag = Xml::tag('name', null, ['foo' => 'bar']);
        $this->assertSame('<name foo="bar" />', $tag);

        $tag = Xml::tag('name', 'String with <not> a tag & some text', ['foo' => 'bar']);
        $this->assertSame('<name foo="bar"><![CDATA[String with <not> a tag & some text]]></name>', $tag);

        $tag = Xml::tag('name', 'content', ['foo' => 'bar'], '  ', 1);
        $this->assertSame('  <name foo="bar">content</name>', $tag);

        $tag = Xml::tag('name', 'content', ['foo' => 'bar'], '    ', 1);
        $this->assertSame('    <name foo="bar">content</name>', $tag);

        $tag = Xml::tag('name', ['Test', 'Test2'], ['foo' => 'bar'], ' ', 2);
        $this->assertSame('  <name foo="bar">' . PHP_EOL . '   Test' . PHP_EOL . '   Test2' . PHP_EOL . '  </name>', $tag);
    }

    /**
     * @covers       ::value
     * @dataProvider valueProvider
     */
    public function testValue($input, $expected)
    {
        $this->assertSame($expected, Xml::value($input));
    }

    public function valueProvider()
    {
        return [
            [true, 'true'],
            [false, 'false'],
            [1, '1'],
            [null, null],
            ['', null],
            ['<![CDATA[test]]>', '<![CDATA[test]]>'],
            ['<![CDATA[String with <not> a tag & some text]]>', '<![CDATA[String with <not> a tag & some text]]>'],
            ['test', 'test'],
            ['String with <not> a tag & some text', '<![CDATA[String with <not> a tag & some text]]>'],
            ['This is a <![CDATA[test]]> with CDATA', '<![CDATA[This is a <![CDATA[test]]]]><![CDATA[> with CDATA]]>'],
            ['te]]>st', '<![CDATA[te]]]]><![CDATA[>st]]>'],
            ['tö]]>st', '<![CDATA[tö]]]]><![CDATA[>st]]>']
        ];
    }
}