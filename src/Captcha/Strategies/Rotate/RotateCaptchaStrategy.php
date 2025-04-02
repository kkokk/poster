<?php
/**
 * User: lang
 * Date: 2025/4/2
 * Time: 13:48
 */

namespace Kkokk\Poster\Captcha\Strategies\Rotate;

use Kkokk\Poster\Captcha\Strategies\CaptchaStrategy;

class RotateCaptchaStrategy extends CaptchaStrategy
{
    protected $configs = [
        'src' => '',
        'im_width' => 350,
        'im_height' => 350,
        'im_type' => 'png', // png 默认 jpg quality 质量
        'quality' => 80,    // jpg quality 质量
    ];  // 验证码图片配置
}