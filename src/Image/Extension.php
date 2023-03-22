<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/22
 * Time: 18:01
 */

namespace Kkokk\Poster\Image;


class Extension implements ExtensionInterface
{

    public function config($params = [])
    {
        // TODO: Implement config() method.
    }

    public function buildIm($w, $h, $rgba = [], $alpha = false)
    {
        // TODO: Implement buildIm() method.
    }

    public function buildImDst($src, $w = 0, $h = 0)
    {
        // TODO: Implement buildImDst() method.
    }

    public function buildBg($w, $h, $rgba = [], $alpha = false, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $func = '')
    {
        // TODO: Implement buildBg() method.
    }
}