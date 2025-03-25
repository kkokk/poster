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
            $textFontAngle = $graphics->getFontAngle();
            $textFontSpace = $graphics->getFontSpace();
            $textFontWeight = $graphics->getFontWeight();
            $textLineHeight = $graphics->getLineHeight();
            $textTextAlign = $graphics->getFontAlign();
            $content = $graphics->getText();
            for ($i = 0; $i < mb_strlen($content); $i++) {
                $singleGraphics[] = [
                    'type'       => 'text',
                    'text'       => mb_substr($content, $i, 1),
                    'color'      => $textColor,
                    'font'       => $textFont,
                    'size'       => $textFontSize,
                    'angle'      => $textFontAngle,
                    'space'      => $textFontSpace,
                    'weight'     => $textFontWeight,
                    'lineHeight' => $textLineHeight,
                    'align'      => $textTextAlign,
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

    protected function autoWrap($characters, $maxWidth, $isSpring = true)
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
                $char['width'] = $this->textWidth($text, $char['size'], $char['font'],
                        $char['angle']) + $spring;
                $char['height'] = $this->textHeight($text, $char['size'], $char['font'], $char['angle']);
                $charWidth = $char['width'] + $char['space'] + $char['weight'] * 0.1;
            } else {
                $charWidth = $char['width'];
            }

            $textWidth += $charWidth;
            $textWidths[$line] = $textWidth;
            $textHeights[$line] = max($textHeights[$line], $char['height']);

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
            $lines[$line] = [];
        }
        $lines[$line][] = $char;
        return $lines;
    }
}