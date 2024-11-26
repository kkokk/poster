<?php
/**
 * User: lang
 * Date: 2024/11/26
 * Time: 11:03
 */

namespace Kkokk\Poster\Image\Gd;

use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Image\Traits\GdTrait;

class Canvas
{
    use GdTrait;

    public $image;

    public $width;

    public $height;

    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
        $this->image = imagecreatetruecolor($width, $height);
    }

    public function background($color = [255, 255, 255, 127])
    {
        if (is_array($color)) {
            if (count($color) != 4) {
                throw new PosterException('The length of the color parameter is 4');
            }
            foreach ($color as $k => $value) {
                if (!is_int($color[$k])) {
                    throw new PosterException('The value must be an integer');
                } elseif ($k < 3 && ($color[$k] > 255 || $color[$k] < 0)) {
                    throw new PosterException('The color value is between 0-255');
                } elseif ($k == 3 && ($color[$k] > 127 || $color[$k] < 0)) {
                    throw new PosterException('The alpha value is between 0-127');
                }
            }
            $rgba = $color;
        } elseif (strpos($color, '#') !== false) {
            $rgba = sscanf($color, "#%02x%02x%02x%02x");
        } elseif (strpos($color, 'rgb') !== false) {
            $rgba = sscanf($color, "#%02x%02x%02x%02x");
        } else {
            throw new PosterException('The background color format error');
        }


        if (isset($rgba[3]) && !is_null($rgba[3])) {
            $bgColor = imagecolorallocatealpha($this->image, $rgba[0], $rgba[1], $rgba[2], $rgba[3]);
        } else {
            $bgColor = imagecolorallocate($this->image, $rgba[0], $rgba[1], $rgba[2]);
        }
        imagefill($this->image, 0, 0, $bgColor);
        return $this;
    }

    public function alpha()
    {
        imagesavealpha($this->image, true);
        return $this;
    }

    public function addImage($image, $x, $y)
    {
        imagecopy($this->image, $image->getImage(), $x, $y, 0, 0, $image->getWidth(), $image->getHeight());
        return $this;
    }

    public function addText($text, $x, $y)
    {
        $text->draw($this->image, $x, $y);
        return $this;
    }

    public function output($filename = null)
    {
        if ($filename) {
            imagepng($this->image, $filename);
        } else {
            header('Content-Type: image/png');
            imagepng($this->image);
        }
        imagedestroy($this->image);
    }
}