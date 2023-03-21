<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/10
 * Time: 10:58
 */

namespace Kkokk\Poster\Facades;

use Kkokk\Poster\Interfaces\MyPoster;

use Kkokk\Poster\Lang\Poster as PosterManager;
/**
 * @method static PosterManager channel($channel = "");
 * @method static MyPoster config($params = []);
 * @method static MyPoster buildIm($w, $h, $rgba = [], $alpha = false);
 * @method static MyPoster buildImDst($w, $h, $rgba = [], $alpha = false);
 * @method static MyPoster buildBg($w, $h, $rgba = [], $alpha = false, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $func = '');
 *
 * @see PosterManager;
 */
class Poster extends Facade
{
    protected static function getFacadeModel(){
        return 'poster';
    }
}