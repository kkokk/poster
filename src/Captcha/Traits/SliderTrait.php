<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/29
 * Time: 14:52
 */

namespace Kkokk\Poster\Captcha\Traits;

trait SliderTrait
{
    public function draw()
    {
        $func = 'draw' . $this->configs['type'];
        return $this->$func();
    }

    // 实现图片绘制
    public function draw3()
    {

        $im_width = $this->configs['im_width'];
        $im_height = $this->configs['im_height'];

        $this->im = $this->PosterDriver->createIm($im_width, $im_height, [], true);

        $this->drawImage($this->configs['src']); // 添加bg图片

        $bg_width = $this->configs['bg_width'];
        $bg_height = $this->configs['bg_height'];

        $slider_width = $this->configs['slider_width'];
        $slider_height = $this->configs['slider_height'];
        $border = $this->configs['slider_border'];

        $w = $slider_width;
        $h = $slider_height;

        $bgColor = $this->PosterDriver->createColorAlpha($this->im, [0, 0, 0, 60]);

        $ims = $this->PosterDriver->createIm($slider_width, $slider_height, [], true); // 创建抠图背景

        $x1 = mt_rand($slider_width * 2, $bg_width - $w - 10);
        $x2 = $x1 + $w;

        $y1 = mt_rand(0, $bg_height - $h);
        $y2 = $y1 + $h;

        $halfBorder = $border / 2;

        $borderColor = $this->PosterDriver->createColor($this->im, [255, 255, 255, 1]);

        $points = [
            $x1 + $w / 2, $y1,
            $x2, $y2,
            $x1, $y2,
        ];

        // 三角形
        $p1 = [$points[0], $points[1]];
        $p2 = [$points[2], $points[3]];
        $p3 = [$points[4], $points[5]];

        for ($i = 0; $i < $bg_width; $i++) {
            for ($j = 0; $j < $bg_height; $j++) {
                // 利用叉积抠图 p1 p2 p3 可以抠多边形
                // 任意坐标点
                $p = [$i, $j];

                $cross1 = $this->getCross($p1, $p2, $p);
                $cross2 = $this->getCross($p2, $p3, $p);
                $cross3 = $this->getCross($p3, $p1, $p);

                $isCross = $cross1 > 0 && $cross2 > 0 && $cross3 > 0;

                if ($isCross) {
                    $rgbColor = imagecolorat($this->im, $i, $j);
                    imagesetpixel($ims, $i - $x1, $j - $y1, $rgbColor); // 抠图
                }
                // $isCross1 = $cross1 * $cross2 * $cross3 == 0;
                // if($isCross1) {
                //     imagesetpixel($ims, $i - $x1, $j - $y1, $rgbColor); // 边框
                // }
            }
        }

        $this->drawImageFilledPolygon($this->im, $points, count($points) / 2, $bgColor);

        $borderPoints = [
            $w / 2, 0,
            $w, $h - $halfBorder / 2,
            0, $h - $halfBorder / 2,
        ];
        imagesetthickness($ims, $halfBorder); // 划线的线宽加粗
        imagepolygon($ims, $borderPoints, count($borderPoints) / 2, $borderColor);

        $bgCount = 1;
        $maxCount = min($this->configs['slider_bg'], 4);
        $maxCount = max($maxCount, 1);
        while ($bgCount < $maxCount) {
            // 额外滑块背景
            $x = mt_rand($slider_width * 2, $bg_width - $w);
            $y = mt_rand(0, $bg_height - $h);
            $points = [
                $x + $w / 2, $y,
                $x + $w, $y + $h,
                $x, $y + $h,
            ];
            $this->drawImageFilledPolygon($this->im, $points, count($points) / 2, $bgColor);
            $bgCount++;
        }

        imagecopy($this->im, $ims, 5, 196, 0, 0, imagesx($ims), imagesy($ims));

        $this->destroyImage($ims);
        return [
            'x' => $x1,
            'y' => $y1,
        ];
    }

    public function draw4()
    {

        $im_width = $this->configs['im_width'];
        $im_height = $this->configs['im_height'];

        $this->im = $this->PosterDriver->createIm($im_width, $im_height, [], true);

        $this->drawImage($this->configs['src']); // 添加bg图片

        $bg_width = $this->configs['bg_width'];
        $bg_height = $this->configs['bg_height'];

        $slider_width = $this->configs['slider_width'];
        $slider_height = $this->configs['slider_height'];
        $border = $this->configs['slider_border'];

        $w = $slider_width - $border;
        $h = $slider_height - $border;

        $bgColor = $this->PosterDriver->createColorAlpha($this->im, [0, 0, 0, 60]);

        $ims = $this->PosterDriver->createIm($slider_width, $slider_height, [], false); // 创建抠图背景

        $x1 = mt_rand($slider_width * 2, $bg_width - $w - 10);
        $x2 = $x1 + $w;

        $y1 = mt_rand(0, $bg_height - $h);
        $y2 = $y1 + $h;

        $halfBorder = $border / 2;

        // 矩形
        $p1 = [$x1 + $halfBorder - 1, $y2 + $halfBorder]; // 左下
        $p2 = [$x2 + $halfBorder, $y2 + $halfBorder]; // 右下
        $p3 = [$x2 + $halfBorder, $y1 + $halfBorder - 1]; // 右上
        $p4 = [$x1 + $halfBorder - 1, $y1 + $halfBorder - 1]; // 左上

        for ($i = 0; $i < $bg_width; $i++) {
            for ($j = 0; $j < $bg_height; $j++) {
                // 矩形抠图
                // if (($i < $x2 && $i >= $x1) && ($j < $y2 && $j >= $y1)) {
                //     $rgbColor = imagecolorat($this->im, $i, $j);
                //     imagesetpixel($ims, $i - $x1 + $border / 2, $j - $y1 + $border / 2, $rgbColor); // 抠图
                // }


                // 利用叉积抠图 p1 p2 p3 可以抠多边形
                // 任意坐标点
                $p = [$i, $j];

                // 叉积计算 点在四条平行线内部则是在矩形内 p1->p2 p1->p3 参考点 p1  叉积大于0点p3在p2逆时针方向 等于0 三点一线 小于0 点p3在p2顺时针防线
                $isCross = $this->getCross($p1, $p2, $p) * $this->getCross($p3, $p4, $p) > 0 && $this->getCross($p2, $p3, $p) * $this->getCross($p4, $p1, $p) > 0;
                if ($isCross) {
                    $rgbColor = imagecolorat($this->im, $i, $j);
                    imagesetpixel($ims, $i - $x1, $j - $y1, $rgbColor); // 抠图
                }
            }
        }

        imagefilledrectangle($this->im, $x1, $y1, $x1 + $slider_width, $y1 + $slider_height, $bgColor);

        $bgCount = 1;
        $maxCount = min($this->configs['slider_bg'], 4);
        $maxCount = max($maxCount, 1);
        while ($bgCount < $maxCount) {
            // 额外滑块背景
            $x = mt_rand(30, $bg_width - $w);
            $y = mt_rand(0, $bg_height - $h);
            imagefilledrectangle($this->im, $x, $y, $x + $slider_width, $y + $slider_height, $bgColor);
            $bgCount++;
        }

        imagecopy($this->im, $ims, 5, 196, 0, 0, $slider_width, $slider_width);

        $this->destroyImage($ims);

        return [
            'x' => $x1,
            'y' => $y1,
        ];
    }

    public function draw5()
    {

        $im_width = $this->configs['im_width'];
        $im_height = $this->configs['im_height'];

        $this->im = $this->PosterDriver->createIm($im_width, $im_height, [], true);

        $this->drawImage($this->configs['src']); // 添加bg图片

        $bg_width = $this->configs['bg_width'];
        $bg_height = $this->configs['bg_height'];

        $slider_width = $this->configs['slider_width'];
        $slider_height = $this->configs['slider_height'];
        $border = $this->configs['slider_border'];

        $w = $slider_width;
        $h = $slider_height;

        $bgColor = $this->PosterDriver->createColorAlpha($this->im, [0, 0, 0, 60]);

        $ims = $this->PosterDriver->createIm($slider_width, $slider_height, [], true); // 创建抠图背景

        $x1 = mt_rand($slider_width * 2, $bg_width - $w - 10);
        $x2 = $x1 + $w;

        $y1 = mt_rand(0, $bg_height - $h);
        $y2 = $y1 + $h;

        $halfBorder = $border / 2;

        $borderColor = $this->PosterDriver->createColor($this->im, [255, 255, 255, 1]);

        $points = [
            $x1 + $w / 2, $y1,
            $x2, $y1 + $h / 2,
            $x1 + $w * 3 / 4, $y2,
            $x1 + $w / 4, $y2,
            $x1, $y1 + $h / 2,
        ];

        // 五边形
        $p1 = [$points[0], $points[1]];
        $p2 = [$points[2], $points[3]];
        $p3 = [$points[4], $points[5]];
        $p4 = [$points[6], $points[7]];
        $p5 = [$points[8], $points[9]];

        for ($i = 0; $i < $bg_width; $i++) {
            for ($j = 0; $j < $bg_height; $j++) {
                // 利用叉积抠图 p1 p2 p3 可以抠多边形
                // 任意坐标点
                $p = [$i, $j];

                $cross1 = $this->getCross($p1, $p2, $p);
                $cross2 = $this->getCross($p2, $p3, $p);
                $cross3 = $this->getCross($p3, $p4, $p);
                $cross4 = $this->getCross($p4, $p5, $p);
                $cross5 = $this->getCross($p5, $p1, $p);

                $isCross = $cross1 > 0 && $cross2 > 0 && $cross3 > 0 && $cross4 > 0 && $cross5 > 0;

                if ($isCross) {
                    $rgbColor = imagecolorat($this->im, $i, $j);
                    imagesetpixel($ims, $i - $x1, $j - $y1, $rgbColor); // 抠图
                }
            }
        }

        $this->drawImageFilledPolygon($this->im, $points, count($points) / 2, $bgColor);

        $borderPoints = [
            $w / 2, 0,
            $w, $h / 2,
            $w * 3 / 4, $h - $halfBorder / 2,
            $w * 1 / 4, $h - $halfBorder / 2,
            0, $h / 2,
        ];
        imagesetthickness($ims, $halfBorder); // 划线的线宽加粗
        imagepolygon($ims, $borderPoints, count($borderPoints) / 2, $borderColor);

        $bgCount = 1;
        $maxCount = min($this->configs['slider_bg'], 4);
        $maxCount = max($maxCount, 1);
        while ($bgCount < $maxCount) {
            // 额外滑块背景
            $x = mt_rand($slider_width * 2, $bg_width - $w);
            $y = mt_rand(0, $bg_height - $h);
            $points = [
                $x + $w / 2, $y,
                $x + $w, $y + $h / 2,
                $x + $w * 3 / 4, $y + $h,
                $x + $w / 4, $y + $h,
                $x, $y + $h / 2,
            ];
            $this->drawImageFilledPolygon($this->im, $points, count($points) / 2, $bgColor);
            $bgCount++;
        }

        imagecopy($this->im, $ims, 5, 196, 0, 0, imagesx($ims), imagesy($ims));

        $this->destroyImage($ims);
        return [
            'x' => $x1,
            'y' => $y1,
        ];
    }

    public function draw6()
    {

        $im_width = $this->configs['im_width'];
        $im_height = $this->configs['im_height'];

        $this->im = $this->PosterDriver->createIm($im_width, $im_height, [], true);

        $this->drawImage($this->configs['src']); // 添加bg图片

        $bg_width = $this->configs['bg_width'];
        $bg_height = $this->configs['bg_height'];

        $slider_width = $this->configs['slider_width'];
        $slider_height = $this->configs['slider_height'];
        $border = $this->configs['slider_border'];

        $w = $slider_width;
        $h = $slider_height;

        $bgColor = $this->PosterDriver->createColorAlpha($this->im, [0, 0, 0, 60]);

        $ims = $this->PosterDriver->createIm($slider_width, $slider_height, [], true); // 创建抠图背景

        $x1 = mt_rand($slider_width * 2, $bg_width - $w - 10);
        $x2 = $x1 + $w;

        $y1 = mt_rand(0, $bg_height - $h);
        $y2 = $y1 + $h;

        $halfBorder = $border / 2;

        $borderColor = $this->PosterDriver->createColor($this->im, [255, 255, 255, 1]);

        $points = [
            $x1 + $w / 4, $y1,
            $x1 + $w * 3 / 4, $y1,
            $x2, $y1 + $h / 2,
            $x1 + $w * 3 / 4, $y2,
            $x1 + $w / 4, $y2,
            $x1, $y1 + $h / 2,
        ];

        // 五边形
        $p1 = [$points[0], $points[1]];
        $p2 = [$points[2], $points[3]];
        $p3 = [$points[4], $points[5]];
        $p4 = [$points[6], $points[7]];
        $p5 = [$points[8], $points[9]];
        $p6 = [$points[10], $points[11]];

        for ($i = 0; $i < $bg_width; $i++) {
            for ($j = 0; $j < $bg_height; $j++) {
                // 利用叉积抠图 p1 p2 p3 可以抠多边形
                // 任意坐标点
                $p = [$i, $j];

                $cross1 = $this->getCross($p1, $p2, $p);
                $cross2 = $this->getCross($p2, $p3, $p);
                $cross3 = $this->getCross($p3, $p4, $p);
                $cross4 = $this->getCross($p4, $p5, $p);
                $cross5 = $this->getCross($p5, $p6, $p);
                $cross6 = $this->getCross($p6, $p1, $p);

                $isCross = $cross1 > 0 && $cross2 > 0 && $cross3 > 0 && $cross4 > 0 && $cross5 > 0 && $cross6 > 0;

                if ($isCross) {
                    $rgbColor = imagecolorat($this->im, $i, $j);
                    imagesetpixel($ims, $i - $x1, $j - $y1, $rgbColor); // 抠图
                }
            }
        }

        $this->drawImageFilledPolygon($this->im, $points, count($points) / 2, $bgColor);

        $borderPoints = [
            $w / 4, 0,
            $w * 3 / 4, 0,
            $w, $h / 2,
            $w * 3 / 4, $h - $halfBorder / 2,
            $w * 1 / 4, $h - $halfBorder / 2,
            0, $h / 2,
        ];
        imagesetthickness($ims, $halfBorder); // 划线的线宽加粗
        imagepolygon($ims, $borderPoints, count($borderPoints) / 2, $borderColor);

        $bgCount = 1;
        $maxCount = min($this->configs['slider_bg'], 4);
        $maxCount = max($maxCount, 1);
        while ($bgCount < $maxCount) {
            // 额外滑块背景
            $x = mt_rand($slider_width * 2, $bg_width - $w);
            $y = mt_rand(0, $bg_height - $h);
            $points = [
                $x + $w / 4, $y,
                $x + $w * 3 / 4, $y,
                $x + $w, $y + $h / 2,
                $x + $w * 3 / 4, $y + $h,
                $x + $w / 4, $y + $h,
                $x, $y + $h / 2,
            ];
            $this->drawImageFilledPolygon($this->im, $points, count($points) / 2, $bgColor);
            $bgCount++;
        }

        imagecopy($this->im, $ims, 5, 196, 0, 0, imagesx($ims), imagesy($ims));

        $this->destroyImage($ims);
        return [
            'x' => $x1,
            'y' => $y1,
        ];
    }

    /**
     * php 8.1 废弃参数 $points_count
     * User: lang
     * Date: 2023/7/17
     * Time: 10:45
     * @param $im
     * @param $points
     * @param $points_count
     * @param $color
     * @return void
     */
    protected function drawImageFilledPolygon($im, $points, $points_count, $color){
        if(PHP_VERSION < 8.1)
            imagefilledpolygon($im, $points, $points_count, $color);
        else
            imagefilledpolygon($im, $points, $color);
    }

    protected function getImBg()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . 'slider_bg' . DIRECTORY_SEPARATOR . 'layer0' . mt_rand(1, 3) . '.jpg';
    }
}