<?php
/**
 * User: lang
 * Date: 2024/11/29
 * Time: 10:53
 */

namespace Kkokk\Poster\Image\Gd;

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'PHPQrcode' . DIRECTORY_SEPARATOR . 'phpqrcode.php');

use Kkokk\Poster\Image\Graphics\GdImageGraphicsEngine;

class Qr extends GdImageGraphicsEngine
{
    public function __construct(
        $text,
        $level = 'L',
        $size = 4,
        $margin = 1
    ) {
        $this->image = \QRcode::re_png($text, $level, $size, $margin);
        $this->width = imagesx($this->image);
        $this->height = imagesy($this->image);
    }

    public function circle()
    {
        $canvas = new Canvas();
        $canvas->newImage($this->width, $this->height);
        $canvas->addImage($this);
        $this->image = $canvas->getImage();
        $canvas->destroyImage();
        return parent::circle();
    }
}