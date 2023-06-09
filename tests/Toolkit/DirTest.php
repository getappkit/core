<?php

namespace Toolkit;


use Appkit\Toolkit\A;
use Appkit\Toolkit\Dir;
use Appkit\Toolkit\F;
use Appkit\Toolkit\Str;

use PHPUnit\Framework\TestCase as TestCase;

class DirTest extends TestCase
{
	public const FIXTURES = __DIR__ . '/fixtures/dir';

	protected $fixtures = __DIR__ . '/fixtures/dir';
	protected $tmp = __DIR__ . '/tmp';
	protected $moved = __DIR__ . '/moved';

	public function tearDown(): void
	{
		Dir::remove($this->tmp);
		Dir::remove($this->moved);
	}

	protected function create(array $items, ...$args)
	{
		foreach ($items as $item) {
			$root = $this->tmp . '/' . $item;

			if ($extension = F::extension($item)) {
				F::write($root, '');
			} else {
				Dir::make($root);
			}
		}

		return $this->tmp;
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::copy
	 */
	public function testCopy()
	{
		$src    = $this->fixtures . '/copy';
		$target = $this->tmp . '/copy';

		$result = Dir::copy($src, $target);

		$this->assertTrue($result);

		$this->assertFileExists($target . '/a.txt');
		$this->assertFileExists($target . '/subfolder/b.txt');
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::copy
	 */
	public function testCopyNonRecursive()
	{
		$src    = $this->fixtures . '/copy';
		$target = $this->tmp . '/copy';

		$result = Dir::copy($src, $target, false);

		$this->assertTrue($result);

		$this->assertFileExists($target . '/a.txt');
		$this->assertFileDoesNotExist($target . '/subfolder/b.txt');
		$this->assertFileDoesNotExist($target . '/subfolder/.gitignore');
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::copy
	 */
	public function testCopyIgnore()
	{
		$src    = $this->fixtures . '/copy';
		$target = $this->tmp . '/copy';

		$result = Dir::copy($src, $target, true, [$src . '/subfolder/b.txt']);

		$this->assertTrue($result);

		$this->assertFileExists($target . '/a.txt');
		$this->assertDirectoryExists($target . '/subfolder');
		$this->assertFileDoesNotExist($target . '/subfolder/b.txt');
		$this->assertFileDoesNotExist($target . '/subfolder/.gitignore');
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::copy
	 */
	public function testCopyMissingSource()
	{
		$this->expectException('Exception');
		$this->expectExceptionMessage('The directory "/does-not-exist" does not exist');

		$src    = '/does-not-exist';
		$target = $this->tmp . '/copy';

		Dir::copy($src, $target);
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::copy
	 */
	public function testCopyExistingTarget()
	{
		$src    = $this->fixtures . '/copy';
		$target = $this->fixtures . '/copy';

		$this->expectException('Exception');
		$this->expectExceptionMessage('The target directory "' . $target . '" exists');

		Dir::copy($src, $target);
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::copy
	 */
	public function testCopyInvalidTarget()
	{
		$src    = $this->fixtures . '/copy';
		$target = '';

		$this->expectException('Exception');
		$this->expectExceptionMessage('The target directory "' . $target . '" could not be created');

		Dir::copy($src, $target);
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::exists
	 */
	public function testExists()
	{
		$this->assertFalse(Dir::exists($this->tmp));
		Dir::make($this->tmp);
		$this->assertTrue(Dir::exists($this->tmp));
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::index
	 */
	public function testIndex()
	{
		Dir::make($dir = $this->tmp);
		Dir::make($this->tmp . '/sub');

		F::write($this->tmp . '/a.txt', 'test');
		F::write($this->tmp . '/b.txt', 'test');

		$expected = [
			'a.txt',
			'b.txt',
			'sub',
		];

		$this->assertSame($expected, Dir::index($dir));
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::index
	 */
	public function testIndexRecursive()
	{
		Dir::make($dir = $this->tmp);
		Dir::make($this->tmp . '/sub');
		Dir::make($this->tmp . '/sub/sub');

		F::write($this->tmp . '/a.txt', 'test');
		F::write($this->tmp . '/sub/b.txt', 'test');
		F::write($this->tmp . '/sub/sub/c.txt', 'test');

		$expected = [
			'a.txt',
			'sub',
			'sub/b.txt',
			'sub/sub',
			'sub/sub/c.txt'
		];

		$this->assertSame($expected, Dir::index($dir, true));
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::index
	 */
	public function testIndexIgnore()
	{
		Dir::$ignore = ['z.txt'];

		Dir::make($dir = $this->tmp);
		Dir::make($this->tmp . '/sub');
		Dir::make($this->tmp . '/sub/sub');

		F::write($this->tmp . '/a.txt', 'test');
		F::write($this->tmp . '/d.txt', 'test');
		F::write($this->tmp . '/z.txt', 'test');
		F::write($this->tmp . '/sub/a.txt', 'test');
		F::write($this->tmp . '/sub/b.txt', 'test');
		F::write($this->tmp . '/sub/sub/a.txt', 'test');
		F::write($this->tmp . '/sub/sub/c.txt', 'test');

		// only global static $ignore
		$this->assertSame([
			'a.txt',
			'd.txt',
			'sub',
			'sub/a.txt',
			'sub/b.txt',
			'sub/sub',
			'sub/sub/a.txt',
			'sub/sub/c.txt'
		], Dir::index($dir, true));

	}

	/**
	 * @covers \Appkit\Toolkit\Dir::isWritable
	 */
	public function testIsWritable()
	{
		Dir::make($this->tmp);

		$this->assertSame(is_writable($this->tmp), Dir::isWritable($this->tmp));
	}


	/**
	 * @covers \Appkit\Toolkit\Dir::make
	 */
	public function testMake()
	{
		$this->assertTrue(Dir::make($this->tmp));
		$this->assertFalse(Dir::make(''));
	}


	/**
	 * @covers \Appkit\Toolkit\Dir::modified
	 */
	public function testModified()
	{
		Dir::make($this->tmp);

		$this->assertTrue(is_int(Dir::modified($this->tmp)));
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::move
	 */
	public function testMove()
	{
		Dir::make($this->tmp);

		$this->assertTrue(Dir::move($this->tmp, $this->moved));
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::move
	 */
	public function testMoveNonExisting()
	{
		$this->assertFalse(Dir::move('/does-not-exist', $this->moved));
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::link
	 */
	public function testLink()
	{
		$source = $this->tmp . '/source';
		$link   = $this->tmp . '/link';

		Dir::make($source);

		$this->assertTrue(Dir::link($source, $link));
		$this->assertTrue(is_link($link));
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::link
	 */
	public function testLinkExistingLink()
	{
		$source = $this->tmp . '/source';
		$link   = $this->tmp . '/link';

		Dir::make($source);
		Dir::link($source, $link);

		$this->assertTrue(Dir::link($source, $link));
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::link
	 */
	public function testLinkWithoutSource()
	{
		$source = $this->tmp . '/source';
		$link   = $this->tmp . '/link';

		$this->expectExceptionMessage('Expection');
		$this->expectExceptionMessage('The directory "' . $source . '" does not exist and cannot be linked');

		Dir::link($source, $link);
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::read
	 */
	public function testRead()
	{
		Dir::make($this->tmp);

		touch($this->tmp . '/a.jpg');
		touch($this->tmp . '/b.jpg');
		touch($this->tmp . '/c.jpg');

		// relative
		$files    = Dir::read($this->tmp);
		$expected = [
			'a.jpg',
			'b.jpg',
			'c.jpg'
		];

		$this->assertSame($expected, $files);

		// absolute
		$files    = Dir::read($this->tmp, null, true);
		$expected = [
			$this->tmp . '/a.jpg',
			$this->tmp . '/b.jpg',
			$this->tmp . '/c.jpg'
		];

		$this->assertSame($expected, $files);

		// ignore
		$files    = Dir::read($this->tmp, ['a.jpg']);
		$expected = [
			'b.jpg',
			'c.jpg'
		];

		$this->assertSame($expected, $files);
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::remove
	 */
	public function testRemove()
	{
		Dir::make($this->tmp);

		$this->assertDirectoryExists($this->tmp);
		$this->assertTrue(Dir::remove($this->tmp));
		$this->assertDirectoryDoesNotExist($this->tmp);
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::isReadable
	 */
	public function testIsReadable()
	{
		Dir::make($this->tmp);

		$this->assertSame(is_readable($this->tmp), Dir::isReadable($this->tmp));
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::dirs
	 * @covers \Appkit\Toolkit\Dir::files
	 */
	public function testReadDirsAndFiles()
	{
		Dir::make($root = $this->fixtures . '/dirs');
		Dir::make($root . '/a');
		Dir::make($root . '/b');
		Dir::make($root . '/c');

		touch($root . '/a.txt');
		touch($root . '/b.jpg');
		touch($root . '/c.doc');

		$any = Dir::read($root);
		$expected = ['a', 'a.txt', 'b', 'b.jpg', 'c', 'c.doc'];

		$this->assertSame($any, $expected);

		// relative dirs
		$dirs = Dir::dirs($root);
		$expected = ['a', 'b', 'c'];

		$this->assertSame($expected, $dirs);

		// absolute dirs
		$dirs = Dir::dirs($root, null, true);
		$expected = [
			$root . '/a',
			$root . '/b',
			$root . '/c'
		];

		$this->assertSame($expected, $dirs);

		// relative files
		$files = Dir::files($root);
		$expected = ['a.txt', 'b.jpg', 'c.doc'];

		$this->assertSame($expected, $files);

		// absolute files
		$files = Dir::files($root, null, true);
		$expected = [
			$root . '/a.txt',
			$root . '/b.jpg',
			$root . '/c.doc'
		];

		$this->assertSame($expected, $files);

		Dir::remove($root);
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::size
	 * @covers \Appkit\Toolkit\Dir::niceSize
	 */
	public function testSize()
	{
		Dir::make($this->tmp);

		F::write($this->tmp . '/testfile-1.txt', Str::random(5));
		F::write($this->tmp . '/testfile-2.txt', Str::random(5));
		F::write($this->tmp . '/testfile-3.txt', Str::random(5));

		$this->assertSame(15, Dir::size($this->tmp));
		$this->assertSame(15, Dir::size($this->tmp, false));
		$this->assertSame('15 B', Dir::niceSize($this->tmp));

		Dir::remove($this->tmp);
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::size
	 */
	public function testSizeWithNestedFolders()
	{
		Dir::make($this->tmp);
		Dir::make($this->tmp . '/sub');
		Dir::make($this->tmp . '/sub/sub');

		F::write($this->tmp . '/testfile-1.txt', Str::random(5));
		F::write($this->tmp . '/sub/testfile-2.txt', Str::random(5));
		F::write($this->tmp . '/sub/sub/testfile-3.txt', Str::random(5));

		$this->assertSame(15, Dir::size($this->tmp));
		$this->assertSame('15 B', Dir::niceSize($this->tmp));

		Dir::remove($this->tmp);
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::size
	 */
	public function testSizeOfNonExistingDir()
	{
		$this->assertFalse(Dir::size('/does-not-exist'));
	}

	/**
	 * @covers \Appkit\Toolkit\Dir::wasModifiedAfter
	 */
	public function testWasModifiedAfter()
	{
		$time = time();

		Dir::make($this->tmp);
		Dir::make($this->tmp . '/sub');
		F::write($this->tmp . '/sub/test.txt', 'foo');

		// the modification time of the folder is already later
		// than the given time
		$this->assertTrue(Dir::wasModifiedAfter($this->tmp, $time - 10));

		// ensure that the modified times are consistent
		// to make the test more reliable
		touch($this->tmp, $time);
		touch($this->tmp . '/sub', $time);
		touch($this->tmp . '/sub/test.txt', $time);

		$this->assertFalse(Dir::wasModifiedAfter($this->tmp, $time));

		touch($this->tmp . '/sub/test.txt', $time + 1);

		$this->assertTrue(Dir::wasModifiedAfter($this->tmp, $time));

		touch($this->tmp . '/sub', $time + 1);
		touch($this->tmp . '/sub/test.txt', $time);

		$this->assertTrue(Dir::wasModifiedAfter($this->tmp, $time));

		// sanity check
		touch($this->tmp . '/sub', $time);

		$this->assertFalse(Dir::wasModifiedAfter($this->tmp, $time));
	}
}
