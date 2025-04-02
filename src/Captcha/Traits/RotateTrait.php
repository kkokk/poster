<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/29
 * Time: 14:52
 */

namespace Kkokk\Poster\Captcha\Traits;


trait RotateTrait
{
    public function draw()
    {

        $imageWidth = $this->configs['im_width'];
        $imageHeight = $this->configs['im_height'];

        $this->driver->Im($imageWidth, $imageHeight, [], true);
        $bgImage = $this->configs['src'] ?: $this->getBackgroundImage();
        // 旋转角度
        $angle = mt_rand(45, 315);
        $this->driver->CopyImage([
            'src'   => $bgImage,
            'angle' => $angle
        ], 0, 0, 0, 0, $imageWidth, $imageHeight, false, 'circle');

        return [
            'angle' => $angle
        ];
    }

    protected function getBackgroundImage()
    {
        return POSTER_BASE_PATH . DIRECTORY_SEPARATOR . 'style' .
            DIRECTORY_SEPARATOR . 'rotate_bg' .
            DIRECTORY_SEPARATOR . 'rotate0' . mt_rand(1, 5) . '.jpg';
    }
}