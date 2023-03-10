<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/10
 * Time: 10:58
 */

namespace Kkokk\Poster\Facades;

use Kkokk\Poster\Lang\Poster as gdBuilder;
/**
 * @method static gdBuilder config($params = []);
 * @method static gdBuilder buildIm($w, $h, $rgba = [], $alpha = false);
 * @method static gdBuilder buildImDst($w, $h, $rgba = [], $alpha = false);
 * @method static gdBuilder buildBg($w, $h, $rgba = [], $alpha = false, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $func = '');
 *
 * @see gdBuilder;
 */
class Poster extends Facade
{
    protected static function getFacadeModel(){
        return 'poster';
    }
}