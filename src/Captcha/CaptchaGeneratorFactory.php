<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/29
 * Time: 14:14
 */

namespace Kkokk\Poster\Captcha;

use Kkokk\Poster\Captcha\Generators;
use Kkokk\Poster\Exception\PosterException;

class CaptchaGeneratorFactory
{
    public function make($name)
    {
        return $this->createGenerator($name);
    }

    protected function createGenerator($name)
    {
        switch ($name) {
            case 'input':
                return new Generators\InputGenerator(); // 输入类验证
            case 'click':
                return new Generators\ClickGenerator(); // 点击验证
            case 'rotate':
                return new Generators\RotateGenerator(); // 旋转验证
            case 'slider':
                return new Generators\SliderGenerator(); // 滑块验证
        }

        throw new PosterException("Unsupported Captcha Generator [{$name}].");
    }
}