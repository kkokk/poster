<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/6
 * Time: 18:10
 */

namespace Kkokk\Poster\Interfaces;


interface MyCaptcha
{
    /**
     * slider 生成滑块验证参数
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/12/6
     * Time: 18:11
     * @return mixed
     */
    public function slider($src = '', $im_width = 340, $im_height = 251, $bg_width = 340, $bg_height = 191, $slider_width = 50, $slider_height = 50, $slider_border = 2);
}