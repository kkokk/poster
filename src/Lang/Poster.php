<?php

namespace Kkokk\Poster\Lang;

use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Image\Gd;
use Kkokk\Poster\Image\Imagick;
use Kkokk\Poster\Interfaces\MyPoster;

/**
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 11:18:03
 * @Last Modified by:   lang
 * @Last Modified time: 2021-09-09 10:33:59
 * 接口模式
 *
 */
class Poster implements MyPoster
{
    protected $channel = 'gd';
    protected $store = [
        'gd' => Gd::class, // gd
        'imagick' => Imagick::class, // Imagick
    ];
    protected $channels = [];

    public function channel($channel = null)
    {
        $this->channel = $channel ?: $this->channel;
        return $this;
    }

    public function config($params = [])
    {
        $this->driver()->config($params);
        return $this;
    }

    public function buildIm($w, $h, $rgba = [], $alpha = false)
    {
        $this->driver()->buildIm($w, $h, $rgba, $alpha);
        return $this;
    }

    public function buildImDst($src, $w = 0, $h = 0)
    {
        $this->driver()->buildImDst($src, $w, $h);
        return $this;
    }

    public function buildBg($w, $h, $rgba = [], $alpha = false, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $func = '')
    {
        $this->driver()->buildBg($w, $h, $rgba, $alpha, $dst_x, $dst_y, $src_x, $src_y, $func);
        return $this;
    }

    public function buildImage($src, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $src_w = 0, $src_h = 0, $alpha = false, $type = 'normal')
    {
        // TODO: Implement buildImage() method.
    }

    public function buildImageMany($arr = [])
    {
        // TODO: Implement buildImageMany() method.
    }

    public function buildLine($x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0, $rgba = [], $type = '', $weight = 1)
    {
        // TODO: Implement buildLine() method.
    }

    public function buildArc($cx = 0, $cy = 0, $w = 0, $h = 0, $s = 0, $e = 0, $rgba = [], $type = '', $style = '', $weight = 1)
    {
        // TODO: Implement buildArc() method.
    }

    public function buildText($content, $dst_x = 0, $dst_y = 0, $font = 16, $rgba = [], $max_w = 0, $font_family = '', $weight = 1, $space = 0, $angle = 0)
    {
        // TODO: Implement buildText() method.
    }

    public function buildTextMany($arr = [])
    {
        // TODO: Implement buildTextMany() method.
    }

    public function buildQr($text, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $src_w = 0, $src_h = 0, $size = 4, $margin = 1)
    {
        // TODO: Implement buildQr() method.
    }

    public function buildQrMany($arr = [])
    {
        // TODO: Implement buildQrMany() method.
    }

    public function Qr($text, $outfile = false, $level = 'L', $size = 4, $margin = 1, $saveAndPrint = 0)
    {
        // TODO: Implement Qr() method.
    }

    public function path($path)
    {
        // TODO: Implement path() method.
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

    public function driver($channel = null)
    {
        return $this->instance($channel ?: $this->channel);
    }

    public function instance($channel)
    {
        if (!isset($this->store[$channel])) throw new PosterException('the ' . $channel . ' channel not found');
        $className = $this->store[$channel];

        return $this->channels[$channel] = new $className;
    }

    function __call($method, $arguments)
    {
        return $this->driver()->$method(...$arguments);
    }
}