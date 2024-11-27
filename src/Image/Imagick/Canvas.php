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

    private $type = 'png';

    public function __construct($width, $height, $background = null, $alpha = false)
    {
        $this->image = $this->createCanvas($width, $height, $background, $alpha);
    }

    public function background($background)
    {
        $this->image->setImageBackgroundColor($this->createColor($background));
    }

    public function addImage(Image $image, $x, $y)
    {
        // 合并图片
        if ($this->type == 'gif') {
            // 每帧长宽不一致问题, 水印会不一致
            foreach ($this->image as $frame) {
                $frame->compositeImage($image->getImage(), ($this->image)::COMPOSITE_DEFAULT, $x, $y);
            }
        } else {
            $this->image->compositeImage($image->getImage(), ($this->image)::COMPOSITE_DEFAULT, $x, $y);
        }
        $image->destroyImage();
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
}