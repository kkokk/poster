<?php
/**
 * User: lang
 * Date: 2025/4/2
 * Time: 14:10
 */

namespace Kkokk\Poster\Captcha\Strategies\Click;

use Kkokk\Poster\Captcha\Strategies\CaptchaStrategy;

class ClickCaptchaStrategy extends CaptchaStrategy
{
    protected $configs = [
        'src'         => '',
        'im_width'    => 256,
        'im_height'   => 306,
        'im_type'     => 'png',
        // png 默认 jpg quality 质量
        'quality'     => 80,
        // jpg quality 质量
        'bg_width'    => 256,
        'bg_height'   => 256,
        'type'        => 'text',
        // text 汉字 number 数字 alpha_num 字母和数字
        'font_family' => POSTER_BASE_PATH . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . 'zhankukuheiti.ttf',
        // 感谢站酷提供免费商用站酷库黑体、可自定义炫酷字体文件（绝对路径）
        'contents'    => '',
        // 自定义文字
        'font_size'   => 42,
        // 字体大小
        'font_count'  => 0,
        // 字体大小
        'line_count'  => 0,
        // 干扰线数量
        'char_count'  => 0,
        // 干扰字符数量
    ];  // 验证码图片配置
}