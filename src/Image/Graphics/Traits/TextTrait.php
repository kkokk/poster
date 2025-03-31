<?php
/**
 * @author  : lang
 * @email   : 732853989@qq.com
 * @date    : 2024/12/3
 * @time    : 22:12
 * @fileName: TextTrait.php
 */

namespace Kkokk\Poster\Image\Graphics\Traits;

use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Image\Graphics\Interfaces\ImageGraphicsEngineInterface;
use Kkokk\Poster\Image\Graphics\Interfaces\TextGraphicsEngineInterface;
use Kkokk\Poster\Image\Imagick\Canvas;

trait TextTrait
{
    protected $springRate = 0.3548;

    protected function singleImageTextSplit($graphics, $textColor = null)
    {
        $singleGraphics = [];

        if ($graphics instanceof TextGraphicsEngineInterface) {
            if (is_null($textColor)) {
                throw new PosterException('TextGraphicsEngineInterface must set textColor');
            }
            $textFont = $graphics->getFont();
            $textFontSize = $graphics->resolveFontSize();
            $textOriginFontSize = $graphics->getFontSize();
            $textFontAngle = $graphics->getFontAngle();
            $textFontSpace = $graphics->getFontSpace();
            $textFontWeight = $graphics->getFontWeight();
            $textLineHeight = $graphics->getLineHeight();
            $textTextAlign = $graphics->getFontAlign();
            $content = $graphics->getText();
            for ($i = 0; $i < mb_strlen($content); $i++) {
                $singleGraphics[] = [
                    'type'        => 'text',
                    'text'        => mb_substr($content, $i, 1),
                    'color'       => $textColor,
                    'font'        => $textFont,
                    'size'        => $textFontSize,
                    'origin_size' => $textOriginFontSize,
                    'angle'       => $textFontAngle,
                    'space'       => $textFontSpace,
                    'weight'      => $textFontWeight,
                    'lineHeight'  => $textLineHeight,
                    'align'       => $textTextAlign,
                ];
            }
        } elseif ($graphics instanceof ImageGraphicsEngineInterface) {
            $singleGraphics[] = [
                'type'       => 'image',
                'image'      => $graphics,
                'width'      => $graphics->getWidth(),
                'height'     => $graphics->getHeight(),
                'lineHeight' => $graphics->getHeight(),
            ];
        }

        return $singleGraphics;
    }

    protected function autoWrap($characters, $maxWidth, $isSpring = true, $space = true, $calcOriginSize = false)
    {
        $lines = [];
        $line = 0;
        $textWidth = 0;
        $textWidths = [0];
        $textHeights = [0];
        foreach ($characters as $char) {
            if ($char['type'] == 'text') {
                $text = $char['text'];
                $spring = $isSpring ? ($char['size'] * $this->springRate) : 0;
                $fontSize = $calcOriginSize ? $char['origin_size'] : $char['size'];
                $char['width'] = $this->textWidth($text, $fontSize, $char['font'],
                        $char['angle']) + $spring;
                $char['height'] = $this->textHeight($text, $fontSize, $char['font'], $char['angle']);
                $charWidth = ($space ? ($char['width'] + $char['space']) : $char['width']) + ($char['weight'] > 1 ? $char['weight'] : 0) * 0.1;
            } else {
                $charWidth = $char['width'];
            }

            $textWidth += $charWidth;
            $textWidths[$line] = $textWidth;
            $textHeights[$line] = max($textHeights[$line], $char['height']);

            if ($textWidth > $maxWidth) {
                $textWidths[$line] -= $charWidth;
                $line += 1;
                $textWidth = $charWidth;
                $textWidths[$line] = $charWidth;
                $textHeights[$line] = 0;
            }
            $lines = $this->addLineCharacters($lines, $line, $char);
        }

        return [
            $lines,
            max(array_values($textWidths)),
            array_sum($textHeights) - (($line + 1) * $this->getLineHeight()),
            $textWidths
        ];
    }

    protected function addLineCharacters($lines, $line, $char)
    {
        if (!isset($lines[$line])) {
            $lines[$line] = [];
        }
        $lines[$line][] = $char;
        return $lines;
    }
}