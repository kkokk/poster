<?php
/**
 * @author  : lang
 * @email   : 732853989@qq.com
 * @date    : 2024/11/30
 * @time    : 17:01
 * @fileName: Texts.php
 */

namespace Kkokk\Poster\Image\Gd;

use Kkokk\Poster\Image\Graphics\GdTextGraphicsEngine;
use Kkokk\Poster\Image\Graphics\Interfaces\ImageGraphicsEngineInterface;
use Kkokk\Poster\Image\Graphics\Interfaces\TextGraphicsEngineInterface;
use Kkokk\Poster\Image\Graphics\Interfaces\TextsInterface;

class Texts extends GdTextGraphicsEngine implements TextsInterface
{
    /** @var array|Text[] */
    protected $contents = [];

    public function addText(TextGraphicsEngineInterface $text)
    {
        $this->contents[] = $text;
        return $this;
    }

    public function draw(ImageGraphicsEngineInterface $image, $x = 0, $y = 0)
    {
        $characters = [];
        $maxWidth = $this->getMaxWidth() ?: $image->getWidth();
        foreach ($this->contents as $text) {
            $textColor = $this->createColor($image->getImage(), $text->getFontColor());
            $textFont = $text->getFont();
            $textFontSize = $text->resolveFontSize();
            $textFontAngle = $text->getFontAngle();
            $textFontSpace = $text->getFontSpace();
            $textFontWeight = $text->getFontWeight();
            $textLineHeight = $text->getLineHeight();
            $textTextAlign = $text->getFontAlign();

            $content = $text->getText();
            // 将字符串拆分成一个个单字 保存到数组 $characters 中
            for ($i = 0; $i < mb_strlen($content); $i++) {
                $characters[] = [
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
        }

        list($lines, $maxTextWidth, $maxTextHeight) = $this->autoWrap($characters, $maxWidth);
        $x = calc_text_dst_x($x, ['max_width' => $maxTextWidth], $image->getWidth());
        $y = calc_text_dst_y($y, ['max_height' => $maxTextHeight], $image->getHeight());

        ll($lines, $x, $maxTextWidth, $maxTextHeight);
        foreach ($lines as $lineIndex => $line) {
            $textWidth = $line['width'];
            $lineCharacters = $line['characters'];
            switch ($this->textAlign) {
                case 'center':
                    $offsetX = $x + ($maxWidth - $textWidth) / 2;
                    break;
                case 'right':
                    $offsetX = $maxWidth - $textWidth + $x;
                    break;
                default:
                    $offsetX = $x;
            }
            $maxSize = max(array_column($lineCharacters, 'size'));
            foreach ($lineCharacters as $key => $char) {
                $lineY = $y + ($lineIndex + 1) * $maxSize * $char['lineHeight'];
                // 计算对齐偏移量
                imagettftext($image->getImage(), $char['size'], $char['angle'], intval($offsetX), intval($lineY),
                    $char['color'],
                    $char['font'],
                    $char['text']);

                $offsetX += $char['width'] + $char['space'];
            }
        }
    }

    protected function autoWrap($characters, $maxWidth)
    {
        $lines = [];
        $line = 0;
        $textWidth = 0;
        $textWidths = [0];
        $textHeights = [0];
        foreach ($characters as $char) {
            $text = $char['text'];
            $char['width'] = $this->textWidth($text, $char['size'], $char['font'],
                    $char['angle']) + ($char['size'] * 0.3548);
            $char['height'] = $this->textHeight($text, $char['size'], $char['font'], $char['angle']);

            $textWidth += $char['width'] + $char['space'];
            $textWidths[$line] = $textWidth;
            $textHeights[$line] = max($textHeights[$line], $char['height']);
            $lineTextWidth = $textWidth;

            if ($textWidth > $maxWidth) {
                $line += 1;
                $textWidth = 0;
                $textWidths[$line] = 0;
                $textHeights[$line] = 0;
            }
            $lines = $this->addLineCharacters($lines, $line, $char, $lineTextWidth);
        }

        return [$lines, max(array_values($textWidths)), array_sum($textHeights)];
    }

    protected function addLineCharacters($lines, $line, $charConfig, $textWidth)
    {
        if (!isset($lines[$line])) {
            $lines[$line] = [];
            $lines[$line]['characters'] = [];
        }
        $lines[$line]['width'] = $textWidth;
        $lines[$line]['characters'][] = $charConfig;
        return $lines;
    }
}