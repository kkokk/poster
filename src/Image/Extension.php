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

    public function Qr($text, $outfile = false, $level = 'L', $size = 4, $margin = 1, $saveAndPrint = 0)
    {
        // TODO: Implement Qr() method.
    }

    public function getPoster($path = '')
    {
        // TODO: Implement getPoster() method.
    }

    public function setPoster()
    {
        // TODO: Implement setPoster() method.
    }

    public function stream()
    {
        // TODO: Implement stream() method.
    }

    public function baseData()
    {
        // TODO: Implement baseData() method.
    }

    protected function run()
    {

    }

    public function getExtension()
    {
        return $this->getDriver();
    }
}