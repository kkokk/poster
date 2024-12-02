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
    public function draw($canvas, $x, $y)
    {
        $lines = $this->autoWrap($this->fontMaxWidth, $this->content);
        $color = $this->createColor($canvas->getImage(), $this->fontColor);
        foreach ($lines as $lineIndex => $line) {
            $lineY = $y + $lineIndex * $this->fontSize * $this->lineHeight;
            // 计算对齐偏移量
            $textWidth = $this->textWidth($line);
            switch ($this->textAlign) {
                case 'center':
                    $offsetX = ($canvas->getWidth() - $textWidth) / 2;
                    break;
                case 'right':
                    $offsetX = $canvas->getWidth() - $textWidth - $x;
                    break;
                default:
                    $offsetX = $x;
            }

            imagettftext($canvas->getImage(), $this->resolveFontSize(), 0, intval($offsetX), intval($lineY), $color,
                $this->font,
                $line);
        }
    }

    private function autoWrap($maxWidth, $text)
    {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $testLine = $currentLine ? "$currentLine $word" : $word;
            $textWidth = $this->textWidth($testLine);
            if ($textWidth > $maxWidth) {
                // 当前行超出宽度，保存当前行并开始新行
                $lines[] = $currentLine;
                $currentLine = $word;
            } else {
                $currentLine = $testLine;
            }
        }

        // 添加最后一行
        if ($currentLine) {
            $lines[] = $currentLine;
        }

        return $lines;
    }
}