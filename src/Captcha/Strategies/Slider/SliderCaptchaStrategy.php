<?php
/**
 * User: lang
 * Date: 2025/4/1
 * Time: 17:31
 */

namespace Kkokk\Poster\Captcha\Strategies\Slider;

use Kkokk\Poster\Captcha\Strategies\CaptchaStrategy;

class SliderCaptchaStrategy extends CaptchaStrategy
{
    protected $configs = [
        'src'           => '',
        'im_width'      => 340,
        'im_height'     => 251,
        'im_type'       => 'png', // png 默认 jpg quality 质量
        'quality'       => 80,    // jpg quality 质量
        'type'          => '4',   // 默认四边形 3 三角形 5 五边形 6 六边形
        'bg_width'      => 340,
        'bg_height'     => 191,
        'slider_width'  => 50,
        'slider_height' => 50,
        'slider_border' => 2,
        'slider_bg'     => 1,
    ];  // 验证码图片配置
}