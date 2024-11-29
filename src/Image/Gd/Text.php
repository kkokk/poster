<?php
/**
 * User: lang
 * Date: 2024/11/26
 * Time: 11:04
 */

namespace Kkokk\Poster\Image\Gd;

use Kkokk\Poster\Image\Graphics\GdGraphicsEngine;

class Text extends GdGraphicsEngine
{
    public function draw($canvas, $x, $y)
    {
        $lines = $this->autoWrap($canvas->getWidth() - $x * 2, $this->content);
        $color = $this->createColor($canvas->getImage(), $this->fontColor);
        foreach ($lines as $lineIndex => $line) {
            $lineY = $y + $lineIndex * $this->fontSize * $this->lineHeight;
            $textBox = imagettfbbox($this->fontSize, 0, $this->font, $line);

            // 计算对齐偏移量
            $textWidth = $textBox[2] - $textBox[0];
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


            var_dump($offsetX);
            var_dump($lineY);
            var_dump($color);
            var_dump($this->font);
            var_dump($line);
            exit;
            imagettftext($canvas->getImage(), $this->fontSize, 0, $offsetX, $lineY, $color, $this->font, $line);
        }
    }

    private function autoWrap($maxWidth, $text)
    {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $testLine = $currentLine ? "$currentLine $word" : $word;
            $textBox = imagettfbbox($this->fontSize, 0, $this->font, $testLine);

            if ($textBox[2] - $textBox[0] > $maxWidth) {
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