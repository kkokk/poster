<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/6
 * Time: 18:10
 */

namespace Kkokk\Poster\Lang;

use Kkokk\Poster\Base\CaptchaBase;
use Kkokk\Poster\Interfaces\MyCaptcha;

class Captcha extends CaptchaBase implements MyCaptcha
{

    public function slider($src = '', $im_width = 340, $im_height = 251, $bg_width = 340, $bg_height = 191, $slider_width = 50, $slider_height = 50, $slider_border = 2)
    {
        return $this->buildSlider($src, $im_width, $im_height, $bg_width, $bg_height, $slider_width, $slider_height, $slider_border);
    }
}