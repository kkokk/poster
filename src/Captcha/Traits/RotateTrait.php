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

        $im_width = $this->configs['im_width'];
        $im_height = $this->configs['im_height'];

        $this->im = $this->PosterDriver->createIm($im_width, $im_height, [], true);

        $this->drawImage($this->configs['src'], true); // 背景图

        // 旋转角度
        $angle = mt_rand(45, 315);

        $this->im = imagerotate($this->im, $angle, 0);

        $this->drawRotate();

        return [
            'angle' => $angle
        ];
    }

    protected function drawRotate()
    {
        $Width = imagesx($this->im);

        $rotateBg = $this->im; // 旋转后的背景

        $bgWidth = $this->configs['im_width'];
        $bgHeight = $this->configs['im_height'];

        $circle = $this->PosterDriver->createIm($bgWidth, $bgHeight, [255, 255, 255, 127], $alpha = false);

        $this->im = $this->PosterDriver->createIm($bgWidth, $bgHeight, [255, 255, 255, 127], $alpha = true); // 最后输出的jpg

        $surplusR = ($Width - $bgWidth) / 2;

        $r = ($bgWidth / 2) - 2; //圆半径
        for ($x = 0; $x < $bgWidth; $x++) {
            for ($y = 0; $y < $bgHeight; $y++) {
                if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                    $rgbColor = imagecolorat($rotateBg, $x + 2 + $surplusR, $y + 2 + $surplusR);
                    imagesetpixel($circle, $x + 2, $y + 2, $rgbColor);
                }
            }
        }
        imagecopyresampled($this->im, $circle, 0, 0, 2, 2, $bgWidth, $bgHeight, $bgWidth - 3, $bgHeight - 3);

        $this->destroyImage($rotateBg);
        $this->destroyImage($circle);
    }

    protected function getImBg()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . 'rotate_bg' . DIRECTORY_SEPARATOR . 'rotate0' . mt_rand(1, 5) . '.jpg';
    }
}