<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/7
 * Time: 10:55
 */

namespace Kkokk\Poster\Captcha;

use Kkokk\Poster\Interfaces\CaptchaInterface;

class Slider extends CaptchaManager implements CaptchaInterface
{

    protected $configs = [
        'src' => '',
        'im_width' => 340,
        'im_height' => 251,
        'im_type' => 'png', // png 默认 jpg quality 质量
        'quality' => 80,    // jpg quality 质量
        'type' => '4', // 默认四边形 3 三角形 5 五边形 6 六边形
        'bg_width' => 340,
        'bg_height' => 191,
        'slider_width' => 50,
        'slider_height' => 50,
        'slider_border' => 2,
        'slider_bg' => 1,
    ];  // 验证码图片配置

    public function config($params = [])
    {
        if (empty($params)) return $this;
        if (PHP_VERSION < 7) {
            $this->configs['src'] = isset($params['src']) ? $params['src'] : $this->configs['src'];
            $this->configs['im_width'] = isset($params['im_width']) ? $params['im_width'] : $this->configs['im_width'];
            $this->configs['im_height'] = isset($params['im_height']) ? $params['im_height'] : $this->configs['im_height'];
            $this->configs['im_type'] = isset($params['im_type']) ? $params['im_type'] : $this->configs['im_type'];
            $this->configs['quality'] = isset($params['quality']) ? $params['quality'] : $this->configs['quality'];
            $this->configs['bg_width'] = isset($params['bg_width']) ? $params['bg_width'] : $this->configs['bg_width'];
            $this->configs['bg_height'] = isset($params['bg_height']) ? $params['bg_height'] : $this->configs['bg_height'];
            $this->configs['slider_width'] = isset($params['slider_width']) ? $params['slider_width'] : $this->configs['slider_width'];
            $this->configs['slider_height'] = isset($params['slider_height']) ? $params['slider_height'] : $this->configs['slider_height'];
            $this->configs['slider_border'] = isset($params['slider_border']) ? $params['slider_border'] : $this->configs['slider_border'];
            $this->configs['slider_bg'] = isset($params['slider_bg']) ? $params['slider_bg'] : $this->configs['slider_bg'];
            $this->configs['type'] = isset($params['type']) ? $params['type'] : $this->configs['type'];
        } else {
            $this->configs['src'] = $params['src'] ?? $this->configs['src'];
            $this->configs['im_width'] = $params['im_width'] ?? $this->configs['im_width'];
            $this->configs['im_height'] = $params['im_height'] ?? $this->configs['im_height'];
            $this->configs['im_type'] = $params['im_type'] ?? $this->configs['im_type'];
            $this->configs['quality'] = $params['quality'] ?? $this->configs['quality'];
            $this->configs['bg_width'] = $params['bg_width'] ?? $this->configs['bg_width'];
            $this->configs['bg_height'] = $params['bg_height'] ?? $this->configs['bg_height'];
            $this->configs['slider_width'] = $params['slider_width'] ?? $this->configs['slider_width'];
            $this->configs['slider_height'] = $params['slider_height'] ?? $this->configs['slider_height'];
            $this->configs['slider_border'] = $params['slider_border'] ?? $this->configs['slider_border'];
            $this->configs['slider_bg'] = $params['slider_bg'] ?? $this->configs['slider_bg'];
            $this->configs['type'] = $params['type'] ?? $this->configs['type'];
        }

        return $this;
    }

    public function get($expire = 0)
    {

        $data = $this->draw();

        $this->imOutput(
            $this->im,
            __DIR__ . '/../../tests/poster/im.' . $this->configs['im_type'],
            $this->configs['im_type'],
            $this->configs['quality']
        );

        $baseData = $this->baseData($this->im, $this->configs['im_type']);

        $key = uniqid('slider' . mt_rand(0, 9999), true);

        $res = [
            'img' => $baseData,
            'key' => $key,
            'y' => $data['y'],
        ];

        $setCache = $this->setCache($key, $data['x'], $expire);
        if(!$setCache) $res['secret'] = $data['x'];

        return $res;
    }

    /**
     * 判断是否正确
     * 目前使用的是 laravel 的 cache
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/12/7
     * Time: 11:44
     * @param $key
     * @param $value
     * @param int $leeway
     * @return bool
     */
    public function check($key, $value, $leeway = 0, $secret = null)
    {
        $x = $this->getCache($key) ? : $secret;

        if (empty($x) ) return false;

        $leeway = $leeway ?: $this->leeway;

        return $x >= ($value - $leeway) && $x <= ($value + $leeway);
    }

    public function draw(){
        $func = 'draw' . $this->configs['type'];
        return $this->$func();
    }

    // 实现图片绘制
    public function draw3()
    {

        $im_width = $this->configs['im_width'];
        $im_height = $this->configs['im_height'];

        $this->im = $this->PosterBase->createIm($im_width, $im_height, [], true);

        $this->drawImage($this->configs['src']); // 添加bg图片

        $bg_width = $this->configs['bg_width'];
        $bg_height = $this->configs['bg_height'];

        $slider_width = $this->configs['slider_width'];
        $slider_height = $this->configs['slider_height'];
        $border = $this->configs['slider_border'];

        $w = $slider_width;
        $h = $slider_height;

        $bgColor = $this->PosterBase->createColorAlpha($this->im, [0, 0, 0, 60]);

        $ims = $this->PosterBase->createIm($slider_width, $slider_height, [], true); // 创建抠图背景

        $x1 = mt_rand($slider_width * 2, $bg_width - $w - 10);
        $x2 = $x1 + $w;

        $y1 = mt_rand(0, $bg_height - $h);
        $y2 = $y1 + $h;

        $halfBorder = $border / 2;

        $borderColor = $this->PosterBase->createColor($this->im, [255, 255, 255, 1]);

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

                if($isCross){
                    $rgbColor = imagecolorat($this->im, $i, $j);
                    imagesetpixel($ims, $i - $x1, $j - $y1, $rgbColor); // 抠图
                }
                // $isCross1 = $cross1 * $cross2 * $cross3 == 0;
                // if($isCross1) {
                //     imagesetpixel($ims, $i - $x1, $j - $y1, $rgbColor); // 边框
                // }
            }
        }

        imagefilledpolygon($this->im, $points, count($points)/2, $bgColor);

        $borderPoints = [
            $w / 2, 0,
            $w, $h - $halfBorder/2,
            0, $h - $halfBorder/2,
        ];
        imagesetthickness($ims, $halfBorder); // 划线的线宽加粗
        imagepolygon($ims, $borderPoints, count($borderPoints)/2, $borderColor);

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
            imagefilledpolygon($this->im, $points, count($points)/2, $bgColor);
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

        $this->im = $this->PosterBase->createIm($im_width, $im_height, [], true);

        $this->drawImage($this->configs['src']); // 添加bg图片

        $bg_width = $this->configs['bg_width'];
        $bg_height = $this->configs['bg_height'];

        $slider_width = $this->configs['slider_width'];
        $slider_height = $this->configs['slider_height'];
        $border = $this->configs['slider_border'];

        $w = $slider_width - $border;
        $h = $slider_height - $border;

        $bgColor = $this->PosterBase->createColorAlpha($this->im, [0, 0, 0, 60]);

        $ims = $this->PosterBase->createIm($slider_width, $slider_height, [], false); // 创建抠图背景

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
                if($isCross){
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

        $this->im = $this->PosterBase->createIm($im_width, $im_height, [], true);

        $this->drawImage($this->configs['src']); // 添加bg图片

        $bg_width = $this->configs['bg_width'];
        $bg_height = $this->configs['bg_height'];

        $slider_width = $this->configs['slider_width'];
        $slider_height = $this->configs['slider_height'];
        $border = $this->configs['slider_border'];

        $w = $slider_width;
        $h = $slider_height;

        $bgColor = $this->PosterBase->createColorAlpha($this->im, [0, 0, 0, 60]);

        $ims = $this->PosterBase->createIm($slider_width, $slider_height, [], true); // 创建抠图背景

        $x1 = mt_rand($slider_width * 2, $bg_width - $w - 10);
        $x2 = $x1 + $w;

        $y1 = mt_rand(0, $bg_height - $h);
        $y2 = $y1 + $h;

        $halfBorder = $border / 2;

        $borderColor = $this->PosterBase->createColor($this->im, [255, 255, 255, 1]);

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

                if($isCross){
                    $rgbColor = imagecolorat($this->im, $i, $j);
                    imagesetpixel($ims, $i - $x1, $j - $y1, $rgbColor); // 抠图
                }
            }
        }

        imagefilledpolygon($this->im, $points, count($points)/2, $bgColor);

        $borderPoints = [
            $w / 2, 0,
            $w, $h / 2,
            $w * 3 / 4, $h - $halfBorder/2,
            $w * 1 / 4, $h - $halfBorder/2,
            0, $h / 2,
        ];
        imagesetthickness($ims, $halfBorder); // 划线的线宽加粗
        imagepolygon($ims, $borderPoints, count($borderPoints)/2, $borderColor);

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
            imagefilledpolygon($this->im, $points, count($points)/2, $bgColor);
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

        $this->im = $this->PosterBase->createIm($im_width, $im_height, [], true);

        $this->drawImage($this->configs['src']); // 添加bg图片

        $bg_width = $this->configs['bg_width'];
        $bg_height = $this->configs['bg_height'];

        $slider_width = $this->configs['slider_width'];
        $slider_height = $this->configs['slider_height'];
        $border = $this->configs['slider_border'];

        $w = $slider_width;
        $h = $slider_height;

        $bgColor = $this->PosterBase->createColorAlpha($this->im, [0, 0, 0, 60]);

        $ims = $this->PosterBase->createIm($slider_width, $slider_height, [], true); // 创建抠图背景

        $x1 = mt_rand($slider_width * 2, $bg_width - $w - 10);
        $x2 = $x1 + $w;

        $y1 = mt_rand(0, $bg_height - $h);
        $y2 = $y1 + $h;

        $halfBorder = $border / 2;

        $borderColor = $this->PosterBase->createColor($this->im, [255, 255, 255, 1]);

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

                if($isCross){
                    $rgbColor = imagecolorat($this->im, $i, $j);
                    imagesetpixel($ims, $i - $x1, $j - $y1, $rgbColor); // 抠图
                }
            }
        }

        imagefilledpolygon($this->im, $points, count($points)/2, $bgColor);

        $borderPoints = [
            $w / 4, 0,
            $w * 3 / 4, 0,
            $w, $h / 2,
            $w * 3 / 4, $h - $halfBorder/2,
            $w * 1 / 4, $h - $halfBorder/2,
            0, $h / 2,
        ];
        imagesetthickness($ims, $halfBorder); // 划线的线宽加粗
        imagepolygon($ims, $borderPoints, count($borderPoints)/2, $borderColor);

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
            imagefilledpolygon($this->im, $points, count($points)/2, $bgColor);
            $bgCount++;
        }

        imagecopy($this->im, $ims, 5, 196, 0, 0, imagesx($ims), imagesy($ims));

        $this->destroyImage($ims);
        return [
            'x' => $x1,
            'y' => $y1,
        ];
    }

    protected function getImBg()
    {
        return __DIR__ . '/../style/slider_bg/layer0' . mt_rand(1, 3) . '.jpg';
    }

}
