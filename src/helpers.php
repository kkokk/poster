<?php
/**
 * User: lang
 * Date: 2024/11/26
 * Time: 17:24
 */

use Kkokk\Poster\Exception\PosterException;

if (!function_exists('parse_color')) {
    function parse_color($color)
    {
        if (is_array($color)) {
            $colorLength = count($color);
            if ($colorLength != 3 && $colorLength != 4) {
                throw new PosterException('The length of the color parameter is 3 or 4');
            }

            foreach ($color as $k => $value) {
                if (!is_int($value)) {
                    throw new PosterException('The value must be an integer');
                } elseif ($k < 3 && ($value > 255 || $value < 1)) {
                    throw new PosterException('The color value is between 1-255');
                } elseif ($k ==3) {

                }

            }
            return $colorLength == 4 ? $color : array_merge($color, [null]);
        }


        if (strpos($color, '#') !== false) {
            return sscanf($color, "#%02x%02x%02x%02x");
        }

        if (strpos($color, 'rgb') !== false) {
            $rgbPattern = '/rgb\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)/i';
            $rgbaPattern = '/rgba\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*([\d.]+)\s*\)/i';
            if (preg_match($rgbPattern, $color, $matches)) {
                return [$matches[1], $matches[2], $matches[3], null];
            }
            if (preg_match($rgbaPattern, $color, $matches)) {
                return [$matches[1], $matches[2], $matches[3], round($matches[4] * 255)];
            }
        }

        throw new PosterException('The color format error');
    }
}
