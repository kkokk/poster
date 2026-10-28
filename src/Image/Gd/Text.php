<?php
/**
 * User: lang
 * Date: 2024/11/26
 * Time: 11:04
 */

namespace Kkokk\Poster\Image\Gd;

use Kkokk\Poster\Image\Graphics\GdTextGraphicsEngine;
use Kkokk\Poster\Image\Graphics\Interfaces\TextGraphicsEngineInterface;

class Text extends GdTextGraphicsEngine implements TextGraphicsEngineInterface
{
    public function draw(Canvas $canvas, $x, $y)
    {
        $color = $this->createColor($canvas->getImage(), $this->fontColor);
        $characters = $this->singleImageTextSplit($this, $color);
        $this->drawTextLayout($canvas, $characters, $x, $y);
    }
}
