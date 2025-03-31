<?php
/**
 * User: lang
 * Date: 2025/3/31
 * Time: 11:29
 */

namespace Kkokk\Poster\Image\Enums;

/**
 * User: lang
 * 兼容低版本php的写法
 * @package Kkokk\Poster\Image\Enums
 * @class   ImageType
 */
class ImageType
{
    const JPEG = 'jpeg';

    const JPG = 'jpg';

    const PNG = 'png';

    const GIF = 'gif';

    const WBMP = 'wbmp';

    const WEBP = 'webp';

    public static function types()
    {
        return [
            self::JPEG,
            self::JPG,
            self::PNG,
            self::GIF,
            self::WBMP,
            self::WEBP,
        ];
    }

    public static function gdImageSaveFunctions()
    {
        return [
            self::JPEG => 'imagejpeg',
            self::JPG  => 'imagejpeg',
            self::PNG  => 'imagepng',
            self::GIF  => 'imagegif',
            self::WBMP => 'imagewbmp',
            self::WEBP => 'imagewebp',
        ];
    }

    public static function setQuantityTypes()
    {
        return [
            self::JPEG,
            self::JPG,
            self::WEBP,
        ];
    }
}