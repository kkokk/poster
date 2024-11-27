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

    private $image;

    private $width;

    private $height;

    private $type = 'png';

    public function __construct($width, $height, $background = [255, 255, 255, 127])
    {
        $this->width = $width;
        $this->height = $height;
        $this->image = $this->createCanvas($width, $height, $background);
    }

    public function background($color = [255, 255, 255, 127])
    {
        $bgColor = $this->createColor($color);
        imagefill($this->image, 0, 0, $bgColor);
        return $this;
    }

    public function addImage(Image $image, $x, $y)
    {
        # 处理目标 x 轴
        $x = calc_dst_x($x, $this->width, $image->getWidth());
        # 处理目标 y 轴
        $y = calc_dst_Y($y, $this->height, $image->getHeight());
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