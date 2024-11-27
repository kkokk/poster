<?php
/**
 * User: lang
 * Date: 2024/11/27
 * Time: 13:29
 */

namespace Kkokk\Poster\Image\Imagick;

use Kkokk\Poster\Image\Traits\ImagickTrait;

class Canvas
{
    use ImagickTrait;

    private $image;

    private $width;

    private $height;

    private $type = 'png';

    public function __construct($width, $height, $background = [255, 255, 255, 127])
    {
        $this->image = $this->createCanvas($width, $height, $background);
        $this->width = $this->image->getImageWidth();
        $this->height = $this->image->getImageHeight();
    }

    public function background($background)
    {
        $this->image->setImageBackgroundColor($this->createColor($background));
    }

    public function addImage(Image $image, $x, $y)
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
    }

    public function addText(Text $text, $x, $y)
    {
        $text->draw($this->image, $x, $y);
    }

    public function output($filename = null)
    {
        if ($filename) {
            $this->image->writeImage($filename);
        } else {
            header('Content-Type: image/png');
            echo $this->image;
        }
    }

    public function __clone()
    {
        $this->image = clone $this->image;
    }
}