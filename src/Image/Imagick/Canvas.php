<?php
/**
 * User: lang
 * Date: 2024/11/27
 * Time: 13:29
 */

namespace Kkokk\Poster\Image\Imagick;

use Kkokk\Poster\Image\Graphics\ImagickImageGraphicsEngine;
use Kkokk\Poster\Image\Graphics\Interfaces\ImageGraphicsEngineInterface;

class Canvas extends ImagickImageGraphicsEngine
{
    public function __construct($width = null, $height = null, $background = [255, 255, 255])
    {
        if ($width && $height) {
            $this->newImage($width, $height, $background);
        }
    }

    public function newImage($width, $height, $background = [255, 255, 255])
    {
        $this->image = $this->createCanvas($width, $height, $background);
        $this->width = $this->image->getImageWidth();
        $this->height = $this->image->getImageHeight();
        return $this;
    }

    public function readImage($source, $width = null, $height = null, $bestFit = false)
    {
        $this->source = $source;

        $image = $this->createImagick($source);
        $sourceWidth = $image->getImageWidth();
        $sourceHeight = $image->getImageHeight();

        $this->image = $image;
        $this->type = strtolower($image->getImageFormat());

        if ($width && $height) {
            $this->scale($width, $height, $bestFit);
        } else {
            $this->width = $sourceWidth;
            $this->height = $sourceHeight;
        }
        return $this;
    }

    public function background($background)
    {
        $this->image->setImageBackgroundColor($this->createColor($background));
    }

    public function addImage(ImageGraphicsEngineInterface $image, $x = 0, $y = 0)
    {
        # 处理目标 x 轴
        $x = calc_dst_x($x, $this->width, $image->getWidth());
        # 处理目标 y 轴
        $y = calc_dst_Y($y, $this->height, $image->getHeight());
        // 合并图片
        if ($this->type == 'gif') {
            // 每帧长宽不一致问题, 水印会不一致
            foreach ($this->image as $frame) {
                $frame->compositeImage($image->getImage(), ($this->image)::COMPOSITE_DEFAULT, $x, $y);
            }
        } else {
            $this->image->compositeImage($image->getImage(), ($this->image)::COMPOSITE_DEFAULT, $x, $y);
        }
        return $this;
    }

    public function addText(Text $text, $x = 0, $y = 0)
    {
        $text->draw($this->image, $x, $y);
        return $this;
    }
}