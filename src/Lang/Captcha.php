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
    protected $store = [
        'input' => \Kkokk\Poster\Captcha\Input::class, // 输入类验证
        'click' => \Kkokk\Poster\Captcha\Click::class, // 点击验证
        'rotate' => \Kkokk\Poster\Captcha\Rotate::class, // 旋转验证
        'slider' => \Kkokk\Poster\Captcha\Slider::class, // 滑块验证
    ];
    protected $channels = [];

    public function config($param = [])
    {
        $this->driver()->config($param);
        return $this;
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
        if(!isset($this->store[$channel])) throw new PosterException('the ' . $channel . ' type not found');
        $className = $this->store[$channel];

        return $this->channels[$channel] = new $className;
    }

    function __call($method, $arguments)
    {
        return $this->driver()->$method(...$arguments);
    }
}