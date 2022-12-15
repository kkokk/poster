<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/9
 * Time: 15:44
 */

namespace Kkokk\Poster\Captcha;

use Kkokk\Poster\Abstracts\MyCaptcha;
use Kkokk\Poster\Facades\Cache;

class Rotate extends MyCaptcha
{
    protected $configs = [
        'src' => '',
        'im_width' => 350,
        'im_height' => 350,
        'im_type' => 'png', // png 默认 jpg quality 质量
        'quality' => 80,    // jpg quality 质量
    ];  // 验证码图片配置

    public function config($param = [])
    {
        if (empty($param)) return $this;
        if (PHP_VERSION < 7) {
            $this->configs['src'] = isset($param['src']) ? $param['src'] : $this->configs['src'];
            $this->configs['im_width'] = isset($param['im_width']) ? $param['im_width'] : $this->configs['im_width'];
            $this->configs['im_height'] = isset($param['im_height']) ? $param['im_height'] : $this->configs['im_height'];
            $this->configs['im_type'] = isset($param['im_type']) ? $param['im_type'] : $this->configs['im_type'];
            $this->configs['quality'] = isset($param['quality']) ? $param['quality'] : $this->configs['quality'];
        } else {
            $this->configs['src'] = $param['src'] ?? $this->configs['src'];
            $this->configs['im_width'] = $param['im_width'] ?? $this->configs['im_width'];
            $this->configs['im_height'] = $param['im_height'] ?? $this->configs['im_height'];
            $this->configs['im_type'] = $param['im_type'] ?? $this->configs['im_type'];
            $this->configs['quality'] = $param['quality'] ?? $this->configs['quality'];
        }

        return $this;
    }

    public function check($key, $value, $leeway = 3)
    {
        $x = Cache::pull($key);

        if (empty($x)) return false;

        $leeway = $leeway ?: $this->leeway;

        return $x >= ($value - $leeway) && $x <= ($value + $leeway);
    }

    public function get($expire = 0)
    {
        $data = $this->draw();

        $this->imOutput(
            $this->im,
            __DIR__ . '/../../tests/poster/rotate.' . $this->configs['im_type'],
            $this->configs['im_type'],
            $this->configs['quality']
        );

        $baseData = $this->baseData($this->im, $this->configs['im_type']);

        $key = uniqid('rotate' . mt_rand(0, 9999), true);

        Cache::put($key, $data['angle'], $expire ?: $this->expire);

        return [
            'img' => $baseData,
            'key' => $key,
        ];
    }

    public function draw()
    {

        $im_width = $this->configs['im_width'];
        $im_height = $this->configs['im_height'];

        $this->im = $this->PosterBase->createIm($im_width, $im_height, [], true);

        $this->drawImage($this->configs['src']); // 背景图

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

        $circle = $this->PosterBase->createIm($bgWidth, $bgHeight, [255, 255, 255, 127], $alpha = false);

        $this->im = $this->PosterBase->createIm($bgWidth, $bgHeight, [255, 255, 255, 127], $alpha = true); // 最后输出的jpg

        $surplusR = ($Width - $bgWidth) / 2;

        $r = ($bgWidth / 2) - 2; //圆半径
        for ($x = 0; $x < $bgWidth; $x++) {
            for ($y = 0; $y < $bgHeight; $y++) {
                $rgbColor = imagecolorat($rotateBg, $x + 2 + $surplusR, $y + 2 + $surplusR);
                if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
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
        return __DIR__ . '/../style/rotate_bg/rotate0' . mt_rand(1, 5) . '.jpg';
    }
}
