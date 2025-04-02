<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/29
 * Time: 14:14
 */

namespace Kkokk\Poster\Captcha;

use Kkokk\Poster\Cache\CacheRepository;
use Kkokk\Poster\Captcha\Generators;
use Kkokk\Poster\Captcha\Strategies\Click\ClickCaptcha;
use Kkokk\Poster\Captcha\Strategies\Input\InputCaptcha;
use Kkokk\Poster\Captcha\Strategies\Rotate\RotateCaptcha;
use Kkokk\Poster\Captcha\Strategies\Slider\SliderCaptcha;
use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Image\Drivers\GdDriver;
use Kkokk\Poster\Image\Drivers\ImagickDriver;

class CaptchaGeneratorFactory
{
    public function make($name, $driver, $cacheAdapter)
    {
        return $this->createGenerator($name, $driver, $cacheAdapter);
    }

    protected function createGenerator($name, $driver, $cacheAdapter)
    {
        $resolveDriver = $this->createDriver($driver);
        $cacheRepository = new CacheRepository($cacheAdapter);
        switch ($name) {
            case 'input':
                return new InputCaptcha($resolveDriver, $cacheRepository); // 输入类验证
            case 'click':
                return new ClickCaptcha($resolveDriver, $cacheRepository); // 点击验证
            case 'rotate':
                return new RotateCaptcha($resolveDriver, $cacheRepository); // 旋转验证
            case 'slider':
                return new SliderCaptcha($resolveDriver, $cacheRepository); // 滑块验证
        }

        throw new PosterException("Unsupported Captcha Generator [{$name}].");
    }

    protected function createDriver($driver)
    {
        switch ($driver) {
            case 'gd':
                return new GdDriver();
            case 'imagick':
                return new ImagickDriver();
        }

        throw new PosterException("Unsupported Captcha Driver [{$driver}].");
    }
}