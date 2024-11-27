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

    private $type = 'png';

    public function __construct($width, $height, $background = null, $alpha = false)
    {
        $this->width = $width;
        $this->height = $height;
        $this->image = $this->createCanvas($width, $height, $background, $alpha);
    }

    public function background($color = [255, 255, 255, 127])
    {
        $bgColor = $this->createColor($color);
        imagefill($this->image, 0, 0, $bgColor);
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

    public function getData($path = '')
    {
        if ($path) {
            $this->setFilePath($path);
        }
        return $this->returnImage($this->type);
    }
}