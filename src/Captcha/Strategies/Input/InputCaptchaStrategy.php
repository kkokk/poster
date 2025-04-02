<?php
/**
 * User: lang
 * Date: 2025/4/1
 * Time: 14:50
 */

namespace Kkokk\Poster\Captcha\Strategies\Input;

use Kkokk\Poster\Captcha\Strategies\CaptchaStrategy;
use Kkokk\Poster\Exception\PosterException;

class InputCaptchaStrategy extends CaptchaStrategy
{
    protected $configs = [
        'src'         => '',
        'im_width'    => 256,
        'im_height'   => 64,
        'im_type'     => 'png', // png 默认 jpg quality 质量
        'quality'     => 80,    // jpg quality 质量
        'type'        => 'number', // type = number 数字 alpha_num 字母和数字 math 计算 text 文字
        'font_family' => POSTER_BASE_PATH . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . 'simkai.ttf',
        'font_size'   => 32, // 字体大小
        'font_count'  => 4,  // 字体长度
        'line_count'  => 5,  // 干扰线数量
        'char_count'  => 10,  // 干扰字符数量
    ];

    protected function drawCharFontSize()
    {
        return round($this->configs['font_size'] / 1.5);
    }
}