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
        $maxWidth = $this->getMaxWidth() ?: $canvas->getWidth();
        $color = $this->createColor($canvas->getImage(), $this->fontColor);
        $characters = $this->singleImageTextSplit($this, $color);
        list($lines, $maxTextWidth, $maxTextHeight, $textWidths) = $this->autoWrap($characters, $maxWidth, false,
            false, true);
        $distX = calc_text_dst_x($x, ['max_width' => $maxTextWidth], $canvas->getWidth());
        $distY = calc_text_dst_y($y, ['max_height' => $maxTextHeight], $canvas->getHeight());

        foreach ($lines as $lineIndex => $line) {
            $lineY = $distY + $lineIndex * $this->lineHeight;
            $textWidth = $textWidths[$lineIndex];
            switch ($this->textAlign) {
                case 'center':
                    $offsetX = $distX + ($maxTextWidth - $textWidth) / 2;
                    break;
                case 'right':
                    $offsetX = $maxTextWidth - $textWidth + $distX;
                    break;
                default:
                    $offsetX = $distX;
            }

            for ($index = 0; $index < $this->getFontWeight(); $index++) {
                list($offsetX, $lineY) = calc_font_weight($index, $this->getFontWeight(), $this->getFontSize(),
                    $offsetX, $lineY);
                imagettftext($canvas->getImage(), $this->resolveFontSize(), $this->getFontAngle(), intval($offsetX),
                    intval($lineY), $color,
                    $this->getFont(),
                    $line);
            }
        }
    }

    protected function addLineCharacters($lines, $line, $char)
    {
        if (!isset($lines[$line])) {
            $lines[$line] = '';
        }
        $lines[$line] .= $char['text'];
        return $lines;
    }
}