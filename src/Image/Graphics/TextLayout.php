<?php

namespace Kkokk\Poster\Image\Graphics;

/**
 * 共享布局器只计算位置；字体度量和像素绘制由各驱动适配。
 * 所有边界均相对于第一行基线，不能用行高代替字形边界。
 */
class TextLayout
{
    public function build($characters, $maxWidth, $measure, $wrapMode = 'character')
    {
        $lines = [[]];
        $line = 0;
        $cursor = 0;
        $previousSpace = 0;
        $cache = [];
        foreach ($characters as $characterIndex => $char) {
            if ($char['type'] === 'text') {
                if ($char['text'] === "\r") {
                    continue;
                }
                if ($char['text'] === "\n") {
                    $lines[$line]['height'] = max(isset($lines[$line]['height']) ? $lines[$line]['height'] : 0, $char['lineHeight']);
                    $line++;
                    $lines[$line] = ['height' => $char['lineHeight']];
                    $cursor = $previousSpace = 0;
                    continue;
                }
                $key = serialize([$char['text'], $char['font'], $char['size'], $char['weight']]);
                if (!isset($cache[$key])) {
                    $cache[$key] = call_user_func($measure, $char);
                }
                $char['metrics'] = $cache[$key];
                $advance = $char['metrics']['advance'];
            } else {
                $advance = $char['width'];
                $char['metrics'] = ['left' => 0, 'top' => -$char['height'], 'right' => $advance, 'bottom' => 0, 'advance' => $advance];
            }
            // 可选ASCII单词边界；超宽单词仍交给字符换行，避免溢出整行。
            if ($wrapMode === 'word' && $maxWidth > 0 && $char['type'] === 'text'
                && preg_match('/^[A-Za-z0-9_]$/D', $char['text'])
                && ($characterIndex === 0 || $characters[$characterIndex-1]['type'] !== 'text'
                    || !preg_match('/^[A-Za-z0-9_]$/D', $characters[$characterIndex-1]['text']))) {
                $word = [];
                for ($next=$characterIndex; $next<count($characters); $next++) {
                    $candidate = $characters[$next];
                    if ($candidate['type'] !== 'text' || !preg_match('/^[A-Za-z0-9_]$/D', $candidate['text'])) break;
                    $word[] = $candidate;
                }
                $wordLayout = $this->build($word, 0, $measure);
                if (!empty($lines[$line]['items']) && $wordLayout['width'] <= $maxWidth
                    && $cursor + $previousSpace + $wordLayout['width'] > $maxWidth) {
                    $line++;
                    $lines[$line] = [];
                    $cursor = $previousSpace = 0;
                }
            }
            // 先换行再记录字符，避免超宽首字产生空行或丢失新行高度。
            if (!empty($lines[$line]['items']) && $maxWidth > 0 && $cursor + $previousSpace + $advance > $maxWidth) {
                $line++;
                $lines[$line] = [];
                $cursor = $previousSpace = 0;
            }
            $cursor += $previousSpace;
            $char['offset'] = $cursor;
            $lines[$line]['items'][] = $char;
            $lines[$line]['height'] = max(isset($lines[$line]['height']) ? $lines[$line]['height'] : 0, $char['lineHeight']);
            $cursor += $advance;
            $lines[$line]['width'] = $cursor;
            $previousSpace = $char['type'] === 'text' ? $char['space'] : 0;
        }

        $width = 0;
        foreach ($lines as $entry) {
            $width = max($width, isset($entry['width']) ? $entry['width'] : 0);
        }
        return ['lines' => $lines, 'width' => $width];
    }

    public function position($layout, $align)
    {
        $items = [];
        $baseline = 0;
        $previousHeight = 0;
        $bounds = null;
        foreach ($layout['lines'] as $index => $line) {
            $height = isset($line['height']) ? $line['height'] : 0;
            if ($index > 0) {
                $baseline += max($previousHeight, $height);
            }
            $previousHeight = $height;
            $layout['lines'][$index]['baseline'] = $baseline;
            $lineWidth = isset($line['width']) ? $line['width'] : 0;
            $offset = $align === 'center' ? ($layout['width'] - $lineWidth) / 2 : ($align === 'right' ? $layout['width'] - $lineWidth : 0);
            $penX = $offset;
            $penY = $baseline;
            $previousOffset = 0;
            $previousAngle = 0;
            // 空白行也占用排版空间，不能在居中时悄悄丢掉用户的换行。
            if (empty($line['items']) && $height > 0) {
                $blank = [$offset, $baseline - $height, $offset, $baseline];
                $bounds = $bounds === null ? $blank : [min($bounds[0], $blank[0]), min($bounds[1], $blank[1]), max($bounds[2], $blank[2]), max($bounds[3], $blank[3])];
            }
            foreach (isset($line['items']) ? $line['items'] : [] as $char) {
                $angle = $char['type'] === 'text' ? $char['angle'] : 0;
                $radians = deg2rad($angle);
                $cos = cos($radians);
                $sin = sin($radians);
                // 沿前一段的基线前进，混排不同角度时保留每段自己的锚点。
                $distance = $char['offset'] - $previousOffset;
                $penX += $distance * cos(deg2rad($previousAngle));
                $penY -= $distance * sin(deg2rad($previousAngle));
                $char['x'] = $penX;
                $char['y'] = $penY;
                $previousOffset = $char['offset'];
                $previousAngle = $angle;
                $m = $char['metrics'];
                $charBounds = null;
                foreach ([[$m['left'], $m['top']], [$m['right'], $m['top']], [$m['right'], $m['bottom']], [$m['left'], $m['bottom']]] as $point) {
                    $x = $char['x'] + $point[0] * $cos + $point[1] * $sin;
                    $y = $char['y'] - $point[0] * $sin + $point[1] * $cos;
                    $charBounds = $charBounds === null ? [$x, $y, $x, $y] : [min($charBounds[0], $x), min($charBounds[1], $y), max($charBounds[2], $x), max($charBounds[3], $y)];
                }
                if ($char['type'] === 'text') {
                    // 模拟加粗发生在画布坐标中，不能跟随字形边界重复旋转。
                    $extra = max(0, ceil($char['weight']) - 1);
                    $charBounds[$char['weight'] % 2 == 0 ? 2 : 3] += $extra;
                }
                $bounds = $bounds === null ? $charBounds : [min($bounds[0], $charBounds[0]), min($bounds[1], $charBounds[1]), max($bounds[2], $charBounds[2]), max($bounds[3], $charBounds[3])];
                // 保留已用于汇总的几何信息，公开查询无需另算边界。
                $char['bounds'] = $charBounds;
                $char['line'] = $index;
                $items[] = $char;
            }
        }
        $layout['items'] = $items;
        $layout['bounds'] = $bounds === null ? [0, 0, 0, 0] : $bounds;
        return $layout;
    }
}
