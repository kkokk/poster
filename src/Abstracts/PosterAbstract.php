<?php

namespace Kkokk\Poster\Abstracts;

use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Base\PosterBase;

/**
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 11:18:03
 * @Last Modified by:   lang
 * @Last Modified time: 2021-09-09 09:40:46
 * 抽象方法，只有继承所有定义的方法，才能被实例化
 */

/**
 * 执行
 */
abstract class PosterAbstract extends PosterBase
{
    abstract public function config($params = []);
    /**
     * buildIm 创建画布
     * buildImDst 以图片创建画布
     * buildBg 创建背景
     * buildImage 合成图片
     * buildImageMany 批量合成图片
     * buildText 合成文字
     * buildTextMany 批量合成文字
     * buildQr 合成二维码
     * buildQrMany 批量合成二维码
     * Qr 创建二维码
     * path 设置海报保存路径
     * getPoster 获取生成海报地址
     * setPoster 对海报进行设置
     * stream 输出视频流
     * @Author lang
     * @Date   2020-08-14T10:40:54+0800
     */
    abstract public function buildIm($w, $h, $rgba = [], $alpha = false);

    abstract public function buildImDst($src, $w = 0, $h = 0);

    abstract public function buildBg($w, $h, $rgba = [], $alpha = false, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $func = '');

    abstract public function buildImage($src, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $src_w = 0, $src_h = 0, $alpha = false, $type = 'normal');

    abstract public function buildImageMany($arr = []);

    abstract public function buildLine($x1, $y1, $x2, $y2, $rgba = [], $weight = 1);

    abstract public function buildText($content, $dst_x = 0, $dst_y = 0, $font = 16, $rgba = [], $max_w = 0, $font_family = '', $weight = 1, $space = 0);

    abstract public function buildTextMany($arr = []);

    abstract public function buildQr($text, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $src_w = 0, $src_h = 0, $size = 4, $margin = 1);

    abstract public function buildQrMany($arr = []);

    abstract public function Qr($text, $outfile = false, $level = 'L', $size = 4, $margin = 1, $saveAndPrint = 0);

    abstract public function path($path);

    abstract public function getPoster();

    abstract public function setPoster();

    abstract public function stream();

    abstract public function baseData();

    public function __construct($params = [])
    {
        parent::__construct($params);

    }

}