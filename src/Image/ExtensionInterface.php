<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/22
 * Time: 18:10
 */

namespace Kkokk\Poster\Image;


interface ExtensionInterface
{
    public function buildIm($w, $h, $rgba = [], $alpha = false);

    public function buildImDst($src, $w = 0, $h = 0);

    public function buildBg($w, $h, $rgba = [], $alpha = false, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $func = '');

    public function Qr($text, $outfile = false, $level = 'L', $size = 4, $margin = 1, $saveAndPrint = 0);

    public function getPoster($path = '');

    public function setPoster();

    public function stream();

    public function baseData();
}