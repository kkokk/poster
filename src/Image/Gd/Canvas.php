<?php
/**
 * User: lang
 * Date: 2024/11/26
 * Time: 11:03
 */

namespace Kkokk\Poster\Image\Gd;

use Kkokk\Poster\Image\Graphics\GdImageGraphicsEngine;
use Kkokk\Poster\Image\Graphics\Interfaces\ImageGraphicsEngineInterface;

class Canvas extends GdImageGraphicsEngine
{
    public function __construct($width = null, $height = null, $background = [255, 255, 255])
    {
        if ($width && $height) {
            $this->newImage($width, $height, $background);
        }
    }

    public function newImage($width, $height, $background = [255, 255, 255])
    {
        $this->width = $width;
        $this->height = $height;
        $this->image = $this->createCanvas($width, $height, $background);
        return $this;
    }

    public function readImage($source, $width = null, $height = null, $bestFit = false)
    {
        $this->source = $source;

        list($image, $sourceWidth, $sourceHeight, $type) = $this->createImage($source);

        imagealphablending($image, true);

        $this->image = $image;
        $this->width = $sourceWidth;
        $this->height = $sourceHeight;
        $this->setType($type);

        if ($width && $height) {
            $this->scale($width, $height, $bestFit);
        }

        return $this;
    }

    public function background($color = [255, 255, 255, 127])
    {
        $bgColor = $this->createColor($color);
        imagefill($this->image, 0, 0, $bgColor);
        return $this;
    }

    public function linearGradient($rgbaColor, $to)
    {
        $rgbaCount = count($rgbaColor);
        $this->calcColorDirection($this->image, $rgbaColor, $rgbaCount, $to, $this->width, $this->height);
        return $this;
    }

    public function addImage(ImageGraphicsEngineInterface $image, $x = 0, $y = 0, $srcX = 0, $srcY = 0)
    {
        # 处理目标 x 轴
        $x = calc_dst_x($x, $this->width, $image->getWidth());
        # 处理目标 y 轴
        $y = calc_dst_Y($y, $this->height, $image->getHeight());
        imagecopy($this->image, $image->getImage(), intval($x), intval($y), intval($srcX), intval($srcY),
            intval($image->getWidth()), intval($image->getHeight()));
        return $this;
    }

    public function addText(Text $text, $x = 0, $y = 0)
    {
        $text->draw($this, $x, $y);
        return $this;
    }

    public function addImageText(ImageText $imageText, $x = 0, $y = 0)
    {
        $imageText->draw($this, $x, $y);
        return $this;
    }
}