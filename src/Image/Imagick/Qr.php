<?php
/**
 * User: lang
 * Date: 2024/11/29
 * Time: 10:55
 */

namespace Kkokk\Poster\Image\Imagick;

use Kkokk\Poster\Image\Graphics\ImagickImageGraphicsEngine;

class Qr extends ImagickImageGraphicsEngine
{
    public function __construct(
        $text,
        $level = 'L',
        $size = 4,
        $margin = 1
    ) {
        $qr = new \Kkokk\Poster\Image\Gd\Qr($text, $level, $size, $margin);
        // TODO windows 宽高108 生成二维码后添加到 canvas 会导致出现类似椭圆点的问题
        $this->image = $this->createImagick($qr->blob());
        $this->width = $this->image->getImageWidth();
        $this->height = $this->image->getImageHeight();
        $qr->destroyImage();
    }
}