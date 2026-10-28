<?php
namespace Kkokk\Poster\Image\Graphics;

/** 双驱动共用RGB插值；色标等距，包含首尾像素，单像素取起始色。 */
class Gradient
{
    public static function color($colors, $position)
    {
        $last = count($colors) - 1;
        if ($last <= 0) return $colors[0];
        $position = max(0, min(1, $position)) * $last;
        $index = min($last - 1, (int) floor($position));
        $fraction = $position - $index;
        $rgb = [];
        for ($channel = 0; $channel < 3; $channel++) {
            $rgb[] = (int) round($colors[$index][$channel] + ($colors[$index + 1][$channel] - $colors[$index][$channel]) * $fraction);
        }
        return $rgb;
    }

    public static function position($x, $y, $width, $height, $to)
    {
        $to = preg_replace('~\s+~', ' ', trim($to));
        $horizontal = strpos($to, 'left') !== false || strpos($to, 'right') !== false;
        $vertical = strpos($to, 'top') !== false || strpos($to, 'bottom') !== false || !$horizontal;
        $distance = 0;
        $length = 0;
        // 对角线采用像素距离投影，矩形两端角准确命中色标。
        if ($horizontal && $width > 1) {
            $distance += strpos($to, 'left') !== false ? $width - 1 - $x : $x;
            $length += $width - 1;
        }
        if ($vertical && $height > 1) {
            $distance += strpos($to, 'top') !== false ? $height - 1 - $y : $y;
            $length += $height - 1;
        }
        return $length ? $distance / $length : 0;
    }
}
