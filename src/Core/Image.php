<?php

namespace Appkit\Core;

namespace Appkit\Core;

use Gregwar\Image\Image as GregwarImage;

/**
 * @method Image brightness($brightness)
 * @method Image contrast($contrast)
 * @method Image crop($x, $y, $width, $height)
 * @method Image flip($flipVertical, $flipHorizontal)
 * @method Image grayscale()
 * @method Image negate()
 * @method Image resize($width = null, $height = null, $background = 'transparent', $force = false, $rescale = false, $crop = false))
 * @method Image scale($width = null, $height = null, $background=0xffffff, $crop = false)
 * @method Image sharp()
 */
class Image
{
    private GregwarImage $image;

    public function __construct(string $path)
    {
        $this->image = new GregwarImage($path);
        $this->image->setCacheDir('media');
    }

    public function __call($name, $arguments)
    {
        $this->image->$name(...$arguments);
        return $this;
    }

    public function url(): string
    {
        return App::instance()->url($this->image->cacheFile());
    }
}
