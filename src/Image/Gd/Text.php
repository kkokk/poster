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
        list($lines, $maxTextWidth, $maxTextHeight) = $this->autoWrap($maxWidth);
        $distX = calc_text_dst_x($x, ['max_width' => $maxTextWidth], $image->getWidth());
        $distY = calc_text_dst_y($y, ['max_height' => $maxTextHeight], $image->getHeight());

        $color = $this->createColor($image->getImage(), $this->fontColor);
        foreach ($lines as $lineIndex => $line) {
            $lineY = $distY + $lineIndex * $this->lineHeight;
            // 计算对齐偏移量
            $textWidth = $this->textWidth($line);
            switch ($this->textAlign) {
                case 'center':
                    $offsetX = $distX + ($image->getWidth() - $textWidth) / 2;
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

    private function autoWrap($maxWidth)
    {
        $characters = [];
        for ($i = 0; $i < mb_strlen($this->content); $i++) {
            $characters[] = mb_substr($this->content, $i, 1);
        }
        $lines = [];
        $line = 0;
        $textWidth = 0;
        $textWidths = [0];
        $textHeights = [0];
        foreach ($characters as $char) {
            $spring = ($this->getFontSize() * 0.3548);
            $width = $this->textWidth($char, $this->getFontSize(), $this->getFont(),
                    $this->getFontAngle()) + $spring;
            $height = $this->textHeight($char, $this->getFontSize(), $this->getFont(), $this->getFontAngle());
            $charWidth = $width + $this->getFontSpace();

            $textWidth += $charWidth;
            $textWidths[$line] = $textWidth;
            $textHeights[$line] = max($textHeights[$line], $height);

            if ($textWidth > $maxWidth) {
                $textWidths[$line] -= $charWidth;
                $line += 1;
                $textWidth = 0;
                $textWidths[$line] = 0;
                $textHeights[$line] = 0;
            }
            $lines = $this->addLineCharacters($lines, $line, $char);
        }

        return [$lines, max(array_values($textWidths)), array_sum($textHeights)];
    }

    protected function addLineCharacters($lines, $line, $char)
    {
        if (!isset($lines[$line])) {
            $lines[$line] = '';
        }
        $lines[$line] .= $char;
        return $lines;
    }
}