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
    public function draw($canvas, $x, $y)
    {
        $lines = $this->autoWrap($canvas->getWidth() - $x * 2, $this->content);

        foreach ($lines as $lineIndex => $line) {
            $lineY = $y + $lineIndex * $this->fontSize * $this->lineHeight;
            $draw = $this->createImagickDraw();
            $draw->setFont($this->font);
            $draw->setFontSize($this->fontSize);
            $draw->setFillColor($this->createColor($this->fontColor));

            // 计算对齐偏移量
            $metrics = $canvas->getImage()->queryFontMetrics($draw, $line);
            $textWidth = abs($metrics['textWidth'] + $metrics['descender']);
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

            $canvas->getImage()->annotateImage($draw, $offsetX, $lineY, 0, $line);
        }
    }

    private function autoWrap($maxWidth, $text)
    {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $testLine = $currentLine ? "$currentLine $word" : $word;
            $draw = $this->createImagickDraw();
            $draw->setFont($this->font);
            $draw->setFontSize($this->fontSize);
            $metrics = $this->createImagick()->queryFontMetrics($draw, $testLine);

            if ($metrics['textWidth'] > $maxWidth) {
                $lines[] = $currentLine;
                $currentLine = $word;
            } else {
                $currentLine = $testLine;
            }
        }

        if ($currentLine) {
            $lines[] = $currentLine;
        }

        return $lines;
    }
}