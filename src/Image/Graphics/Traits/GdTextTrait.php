<?php
/**
 * @author  : lang
 * @email   : 732853989@qq.com
 * @date    : 2024/12/3
 * @time    : 22:10
 * @fileName: TextTrait.php
 */

namespace Kkokk\Poster\Image\Graphics\Traits;

trait GdTextTrait
{
    use TextTrait;

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
                $charWidth = $char['width'] + $char['space'];
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