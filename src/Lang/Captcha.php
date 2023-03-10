<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/6
 * Time: 18:10
 */

namespace Kkokk\Poster\Lang;

use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Interfaces\MyCaptcha;

class Captcha implements MyCaptcha
{
    protected $channel = 'slider';
    protected $channels = [];

    public function config($param = [])
    {
        return $this->driver()->config($param);
    }

    public function check($key, $value, $leeway = 0, $secret = null)
    {
        return $this->driver()->check($key, $value, $leeway, $secret);
    }

    public function get($expire = 0)
    {
        return $this->driver()->get($expire);
    }

    public function type($channel = null)
    {
        return $this->driver($channel);
    }

    public function driver($channel = null)
    {
        return $this->instance($channel ?: $this->channel);
    }

    public function instance($channel)
    {

        $className = '\\Kkokk\\Poster\Captcha\\' . $channel;
        if (!class_exists($className)) throw new PosterException('class not found');

        return $this->channels[$channel] = new $className;
    }

    function __call($method, $arguments)
    {
        return $this->driver()->$method(...$arguments);
    }
}