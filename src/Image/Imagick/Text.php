<?php
/**
 * User: lang
 * Date: 2024/11/27
 * Time: 13:29
 */

namespace Kkokk\Poster\Image\Imagick;

use Kkokk\Poster\Image\Graphics\ImagickTextGraphicsEngine;
use Kkokk\Poster\Image\Graphics\Interfaces\TextGraphicsEngineInterface;

class Text extends ImagickTextGraphicsEngine implements TextGraphicsEngineInterface
{
    public function draw(Canvas $canvas, $x, $y)
    {
        $color = $this->createColor($this->fontColor);
        $characters = $this->singleImageTextSplit($this, $color);
        $this->drawTextLayout($canvas, $characters, $x, $y);
    }
}
