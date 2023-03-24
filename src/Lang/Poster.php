<?php

namespace Kkokk\Poster\Lang;

use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Image\Gd;
use Kkokk\Poster\Image\Imagick;

/**
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 11:18:03
 * @Last Modified by:   lang
 * @Last Modified time: 2021-09-09 10:33:59
 * 接口模式
 *
 */
class Poster
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