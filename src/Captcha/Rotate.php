<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/9
 * Time: 15:44
 */

namespace Kkokk\Poster\Captcha;

use Kkokk\Poster\Base\PosterBase;
use Kkokk\Poster\Abstracts\MyCaptcha;
use Kkokk\Poster\Exception\PosterException;
use Illuminate\Support\Facades\Cache;

class Rotate extends \Kkokk\Poster\Abstracts\MyCaptcha
{
    protected $configs = [
        'src'           => '',
        'im_width'      => 350,
        'im_height'     => 350,
    ];  // 验证码图片配置

    public function config($param = [])
    {
        if(empty($param)) return $this;
        if(PHP_VERSION < 7) {
            $this->configs['src'] = isset($param['src']) ? $param['src'] : $this->configs['src'];
            $this->configs['im_width'] = isset($param['im_width']) ? $param['im_width'] : $this->configs['im_width'];
            $this->configs['im_height'] = isset($param['im_height']) ? $param['im_height'] : $this->configs['im_height'];
        } else {
            $this->configs['src'] = $param['src'] ?? $this->configs['src'];
            $this->configs['im_width'] = $param['im_width'] ?? $this->configs['im_width'];
            $this->configs['im_height'] = $param['im_height'] ?? $this->configs['im_height'];
        }

        return $this;
    }

    public function check($key, $value, $leeway = 0)
    {
        if(class_exists(Cache::class)){
            $x = Cache::pull($key);
        } else {
            return false;
        }

        if(empty($x)) return false;

        $leeway = $leeway ?: $this->leeway;

        return $x >= ($value - $leeway) && $x <= ($value + $leeway);
    }

    public function get()
    {
        $data = $this->draw();

        imagejpeg($this->im, __DIR__.'/../../tests/poster/rotate.jpg');

        $baseData = $this->baseData($this->im, 'jpg');

        $key = uniqid('rotate'.mt_rand(0, 9999), true);

        if(class_exists(Cache::class)){
            Cache::put($key , $data['angle'], $this->expire);
        }

        return [
            'img' => $baseData,
            'key' => $key,
        ];
    }

    public function draw()
    {
        $draw = new PosterBase;

        $im_width = $this->configs['im_width'];
        $im_height = $this->configs['im_height'];

        $this->im = $draw->createIm($im_width, $im_height, [], true);

        $this->drawImage(); // 背景图

        // 旋转角度
        $angle = mt_rand(45, 315);

        $this->im = imagerotate($this->im , $angle, 0);

        $this->drawRotate($draw);

        return [
            'angle' => $angle
        ];
    }

    protected function drawImage($src = ''){

        $src = $src ?: $this->getSliderBg();

        list($Width, $Hight, $bgType) = @getimagesize($src);

        $bgType = image_type_to_extension($bgType, false);

        if (empty($bgType)) throw new PosterException('image resources cannot be empty (' . $src . ')');

        if ($bgType == 'gif') {
            $pic = imagecreatefromstring(file_get_contents($src));
        } else {

            $fun = 'imagecreatefrom' . $bgType;
            $pic = @$fun($src);
        }

        imagecopyresized($this->im, $pic, 0, 0, 0, 0, $this->configs['im_width'], $this->configs['im_height'], $Width, $Hight);
        imagedestroy($pic);
    }

    protected function drawRotate($draw){
        $Width = imagesx($this->im);

        $rotateBg = $this->im; // 旋转后的背景

        $bgWidth = $this->configs['im_width'];
        $bgHeight = $this->configs['im_height'];

        $circle = $draw->createIm($bgWidth, $bgHeight, [255, 255, 255, 127], $alpha = false);

        $this->im = $draw->createIm($bgWidth, $bgHeight, [255, 255, 255, 127], $alpha = true); // 最后输出的jpg

        $surplusR = ($Width - $bgWidth)/2;

        $r = ($bgWidth / 2) - 5; //圆半径
        for ($x = 0; $x < $bgWidth; $x++) {
            for ($y = 0; $y < $bgHeight; $y++) {
                $rgbColor = imagecolorat($rotateBg, $x + 5 + $surplusR, $y + 5 + $surplusR);
                if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                    imagesetpixel($circle, $x + 5, $y + 5, $rgbColor);
                }
            }
        }
        imagecopyresampled($this->im, $circle, 0, 0, 5, 5, $bgWidth, $bgHeight, $bgWidth-10, $bgHeight-10);

        imagedestroy($rotateBg);
        imagedestroy($circle);
    }

    protected function getSliderBg(){
        return __DIR__.'/../style/rotate_bg/rotate0'.mt_rand(1,5).'.jpg';
    }
}