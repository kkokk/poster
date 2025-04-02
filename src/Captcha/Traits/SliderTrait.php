<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/29
 * Time: 14:52
 */

namespace Kkokk\Poster\Captcha\Traits;

use Kkokk\Poster\Image\Drivers\GdDriver;
use Kkokk\Poster\Image\Drivers\ImagickDriver;

trait SliderTrait
{
    protected function draw()
    {
        $imageWidth = $this->configs['im_width'];
        $imageHeight = $this->configs['im_height'];
        $this->driver->Im($imageWidth, $imageHeight, [], true);
        $bgImage = $this->configs['src'] ?: $this->getBackgroundImage();
        $this->driver->CopyImage($bgImage);
        $func = 'draw' . $this->configs['type'];
        return $this->$func();
    }

    // 实现图片绘制
    protected function draw3()
    {
        $bgWidth = $this->configs['bg_width'];
        $bgHeight = $this->configs['bg_height'];

        $sliderWidth = $this->configs['slider_width'];
        $sliderHeight = $this->configs['slider_height'];
        $border = $this->configs['slider_border'];

        $w = $sliderWidth;
        $h = $sliderHeight;

        $x1 = mt_rand($sliderWidth * 2, $bgWidth - $w - 10);
        $x2 = $x1 + $w;

        $y1 = mt_rand(0, $bgHeight - $h);
        $y2 = $y1 + $h;

        $halfBorder = $border / 2;
        $points = [
            $x1 + $w / 2,
            $y1,
            $x2,
            $y2,
            $x1,
            $y2,
        ];

        // 三角形
        $p1 = [$points[0], $points[1]];
        $p2 = [$points[2], $points[3]];
        $p3 = [$points[4], $points[5]];

        // 创建抠图背景
        $cutoutCanvas = $this->driver->getCanvas()->cutout($x1, $y1, $sliderWidth, $sliderHeight,
            function ($p) use ($p1, $p2, $p3) {
                $cross1 = cross_product($p1, $p2, $p);
                $cross2 = cross_product($p2, $p3, $p);
                $cross3 = cross_product($p3, $p1, $p);
                return $cross1 > 0 && $cross2 > 0 && $cross3 > 0;
            });

        $this->driver->getCanvas()->drawImageFilledPolygon($points, [0, 0, 0, 60]);

        $borderPoints = [
            $w / 2,
            0,
            $w,
            $h - $halfBorder / 2,
            0,
            $h - $halfBorder / 2,
        ];
        $cutoutCanvas->drawImagePolygon($borderPoints, [255, 255, 255], $halfBorder);

        $bgCount = 1;
        $maxCount = min($this->configs['slider_bg'], 4);
        $maxCount = max($maxCount, 1);
        while ($bgCount < $maxCount) {
            // 额外滑块背景
            $x = mt_rand($sliderWidth * 2, $bgWidth - $w);
            $y = mt_rand(0, $bgHeight - $h);
            $points = [
                $x + $w / 2,
                $y,
                $x + $w,
                $y + $h,
                $x,
                $y + $h,
            ];
            $this->driver->getCanvas()->drawImageFilledPolygon($points, [0, 0, 0, 60]);
            $bgCount++;
        }

        $this->driver->getCanvas()->addImage($cutoutCanvas, 5, 196);
        $cutoutCanvas->destroyImage();
        return [
            'x' => $x1,
            'y' => $y1,
        ];
    }

    protected function draw4()
    {
        $bgWidth = $this->configs['bg_width'];
        $bgHeight = $this->configs['bg_height'];

        $sliderWidth = $this->configs['slider_width'];
        $sliderHeight = $this->configs['slider_height'];
        $border = $this->configs['slider_border'];

        $w = $sliderWidth - $border;
        $h = $sliderHeight - $border;

        $x1 = mt_rand($sliderWidth * 2, $bgWidth - $w - 10);
        $x2 = $x1 + $w;

        $y1 = mt_rand(0, $bgHeight - $h);
        $y2 = $y1 + $h;

        $halfBorder = $border / 2;

        // 矩形
        $p1 = [$x1 + $halfBorder - 1, $y2 + $halfBorder];     // 左下
        $p2 = [$x2 + $halfBorder, $y2 + $halfBorder];         // 右下
        $p3 = [$x2 + $halfBorder, $y1 + $halfBorder - 1];     // 右上
        $p4 = [$x1 + $halfBorder - 1, $y1 + $halfBorder - 1]; // 左上

        // 创建抠图背景
        $cutoutCanvas = $this->driver->getCanvas()->cutout($x1, $y1, $sliderWidth, $sliderHeight,
            function ($p) use ($p1, $p2, $p3, $p4) {
                return cross_product($p1, $p2, $p) * cross_product($p3, $p4, $p) > 0 && cross_product($p2,
                        $p3, $p) * cross_product($p4, $p1, $p) > 0;
            });

        $this->driver->CopyLine($x1, $y1, $x1 + $sliderWidth, $y1 + $sliderHeight, [0, 0, 0, 60],
            'onlyFilledRectangle');

        $bgCount = 1;
        $maxCount = min($this->configs['slider_bg'], 4);
        $maxCount = max($maxCount, 1);
        while ($bgCount < $maxCount) {
            // 额外滑块背景
            $x = mt_rand(30, $bgWidth - $w);
            $y = mt_rand(0, $bgHeight - $h);
            $this->driver->CopyLine($x, $y, $x + $sliderWidth, $y + $sliderHeight, [0, 0, 0, 60],
                'onlyFilledRectangle');
            $bgCount++;
        }

        $this->driver->getCanvas()->addImage($cutoutCanvas, 5, 196);
        $cutoutCanvas->destroyImage();

        return [
            'x' => $x1,
            'y' => $y1,
        ];
    }

    protected function draw5()
    {
        $bgWidth = $this->configs['bg_width'];
        $bgHeight = $this->configs['bg_height'];

        $sliderWidth = $this->configs['slider_width'];
        $sliderHeight = $this->configs['slider_height'];
        $border = $this->configs['slider_border'];

        $w = $sliderWidth;
        $h = $sliderHeight;

        $x1 = mt_rand($sliderWidth * 2, $bgWidth - $w - 10);
        $x2 = $x1 + $w;

        $y1 = mt_rand(0, $bgHeight - $h);
        $y2 = $y1 + $h;

        $halfBorder = $border / 2;

        $points = [
            $x1 + $w / 2,
            $y1,
            $x2,
            $y1 + $h / 2,
            $x1 + $w * 3 / 4,
            $y2,
            $x1 + $w / 4,
            $y2,
            $x1,
            $y1 + $h / 2,
        ];

        // 五边形
        $p1 = [$points[0], $points[1]];
        $p2 = [$points[2], $points[3]];
        $p3 = [$points[4], $points[5]];
        $p4 = [$points[6], $points[7]];
        $p5 = [$points[8], $points[9]];

        // 创建抠图背景
        $cutoutCanvas = $this->driver->getCanvas()->cutout($x1, $y1, $sliderWidth, $sliderHeight,
            function ($p) use ($p1, $p2, $p3, $p4, $p5) {
                $cross1 = cross_product($p1, $p2, $p);
                $cross2 = cross_product($p2, $p3, $p);
                $cross3 = cross_product($p3, $p4, $p);
                $cross4 = cross_product($p4, $p5, $p);
                $cross5 = cross_product($p5, $p1, $p);

                return $cross1 > 0 && $cross2 > 0 && $cross3 > 0 && $cross4 > 0 && $cross5 > 0;
            });

        $this->driver->getCanvas()->drawImageFilledPolygon($points, [0, 0, 0, 60]);

        $borderPoints = [
            $w / 2,
            0,
            $w,
            $h / 2,
            $w * 3 / 4,
            $h - $halfBorder / 2,
            $w * 1 / 4,
            $h - $halfBorder / 2,
            0,
            $h / 2,
        ];

        $cutoutCanvas->drawImagePolygon($borderPoints, [255, 255, 255], $halfBorder);

        $bgCount = 1;
        $maxCount = min($this->configs['slider_bg'], 4);
        $maxCount = max($maxCount, 1);
        while ($bgCount < $maxCount) {
            // 额外滑块背景
            $x = mt_rand($sliderWidth * 2, $bgWidth - $w);
            $y = mt_rand(0, $bgHeight - $h);
            $points = [
                $x + $w / 2,
                $y,
                $x + $w,
                $y + $h / 2,
                $x + $w * 3 / 4,
                $y + $h,
                $x + $w / 4,
                $y + $h,
                $x,
                $y + $h / 2,
            ];
            $this->driver->getCanvas()->drawImageFilledPolygon($points, [0, 0, 0, 60]);
            $bgCount++;
        }

        $this->driver->getCanvas()->addImage($cutoutCanvas, 5, 196);
        $cutoutCanvas->destroyImage();

        return [
            'x' => $x1,
            'y' => $y1,
        ];
    }

    protected function draw6()
    {
        $bgWidth = $this->configs['bg_width'];
        $bgHeight = $this->configs['bg_height'];

        $sliderWidth = $this->configs['slider_width'];
        $sliderHeight = $this->configs['slider_height'];
        $border = $this->configs['slider_border'];

        $w = $sliderWidth;
        $h = $sliderHeight;

        $x1 = mt_rand($sliderWidth * 2, $bgWidth - $w - 10);
        $x2 = $x1 + $w;

        $y1 = mt_rand(0, $bgHeight - $h);
        $y2 = $y1 + $h;

        $halfBorder = $border / 2;

        $points = [
            $x1 + $w / 4,
            $y1,
            $x1 + $w * 3 / 4,
            $y1,
            $x2,
            $y1 + $h / 2,
            $x1 + $w * 3 / 4,
            $y2,
            $x1 + $w / 4,
            $y2,
            $x1,
            $y1 + $h / 2,
        ];

        // 五边形
        $p1 = [$points[0], $points[1]];
        $p2 = [$points[2], $points[3]];
        $p3 = [$points[4], $points[5]];
        $p4 = [$points[6], $points[7]];
        $p5 = [$points[8], $points[9]];
        $p6 = [$points[10], $points[11]];

        // 创建抠图背景
        $cutoutCanvas = $this->driver->getCanvas()->cutout($x1, $y1, $sliderWidth, $sliderHeight,
            function ($p) use ($p1, $p2, $p3, $p4, $p5, $p6) {
                $cross1 = cross_product($p1, $p2, $p);
                $cross2 = cross_product($p2, $p3, $p);
                $cross3 = cross_product($p3, $p4, $p);
                $cross4 = cross_product($p4, $p5, $p);
                $cross5 = cross_product($p5, $p6, $p);
                $cross6 = cross_product($p6, $p1, $p);

                return $cross1 > 0 && $cross2 > 0 && $cross3 > 0 && $cross4 > 0 && $cross5 > 0 && $cross6 > 0;
            });

        $this->driver->getCanvas()->drawImageFilledPolygon($points, [0, 0, 0, 60]);

        $borderPoints = [
            $w / 4,
            0,
            $w * 3 / 4,
            0,
            $w,
            $h / 2,
            $w * 3 / 4,
            $h - $halfBorder / 2,
            $w * 1 / 4,
            $h - $halfBorder / 2,
            0,
            $h / 2,
        ];
        $cutoutCanvas->drawImagePolygon($borderPoints, [255, 255, 255], $halfBorder);

        $bgCount = 1;
        $maxCount = min($this->configs['slider_bg'], 4);
        $maxCount = max($maxCount, 1);
        while ($bgCount < $maxCount) {
            // 额外滑块背景
            $x = mt_rand($sliderWidth * 2, $bgWidth - $w);
            $y = mt_rand(0, $bgHeight - $h);
            $points = [
                $x + $w / 4,
                $y,
                $x + $w * 3 / 4,
                $y,
                $x + $w,
                $y + $h / 2,
                $x + $w * 3 / 4,
                $y + $h,
                $x + $w / 4,
                $y + $h,
                $x,
                $y + $h / 2,
            ];
            $this->driver->getCanvas()->drawImageFilledPolygon($points, [0, 0, 0, 60]);
            $bgCount++;
        }

        $this->driver->getCanvas()->addImage($cutoutCanvas, 5, 196);
        $cutoutCanvas->destroyImage();

        return [
            'x' => $x1,
            'y' => $y1,
        ];
    }

    protected function getBackgroundImage()
    {
        return POSTER_BASE_PATH . DIRECTORY_SEPARATOR . 'style' .
            DIRECTORY_SEPARATOR . 'slider_bg' .
            DIRECTORY_SEPARATOR . 'layer0' . mt_rand(1, 3) . '.jpg';
    }
}