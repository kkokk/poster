<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/10
 * Time: 10:58
 */

namespace Kkokk\Poster\Facades;

use Kkokk\Poster\Image\ExtensionInterface;
use Kkokk\Poster\Image\PosterManager;
use Kkokk\Poster\Image\Builder;

/**
 * @method static ExtensionInterface extension($name = "");
 * @method static Builder config($params = []);
 * @method static Builder buildIm($w, $h, $rgba = [], $alpha = false);
 * @method static Builder buildImDst($src, $w = 0, $h = 0);
 * @method static Builder buildBg($w, $h, $rgba = [], $alpha = false, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $func = '');
 * @method static array Qr($text, $outfile = false, $level = 'L', $size = 4, $margin = 1, $saveAndPrint = 0);
 *
 * @see \Kkokk\Poster\Image\PosterManager
 * @see \Kkokk\Poster\Image\Extension
 */
class Poster extends Facade
{
    protected static function getFacadeModel()
    {
        return 'poster';
    }
}