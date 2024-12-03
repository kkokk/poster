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
    public function draw($image, $x, $y)
    {
        $maxWidth = $this->getMaxWidth() ?: $image->getWidth();
        $color = $this->createColor($image->getImage(), $this->fontColor);
        $characters = $this->singleImageTextSplit($this, $color);
        list($lines, $maxTextWidth, $maxTextHeight) = $this->autoWrap($characters, $maxWidth, false);

        $distX = calc_text_dst_x($x, ['max_width' => $maxTextWidth], $image->getWidth());
        $distY = calc_text_dst_y($y, ['max_height' => $maxTextHeight], $image->getHeight());

        foreach ($lines as $lineIndex => $line) {
            $lineY = $distY + $lineIndex * $this->lineHeight;
            // 计算对齐偏移量
            $textWidth = $this->textWidth($line, $this->resolveFontSize(), $this->getFont(), $this->getFontAngle());
            switch ($this->textAlign) {
                case 'center':
                    $offsetX = $distX + ($maxWidth - $textWidth) / 2;
                    break;
                case 'right':
                    $offsetX = $maxWidth - $textWidth + $distX;
                    break;
                default:
                    $offsetX = $distX;
            }

            imagettftext($image->getImage(), $this->resolveFontSize(), 0, intval($offsetX), intval($lineY), $color,
                $this->getFont(),
                $line);
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