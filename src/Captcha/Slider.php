<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/7
 * Time: 10:55
 */

namespace Kkokk\Poster\Captcha;

use Kkokk\Poster\Base\PosterBase;
use Kkokk\Poster\Abstracts\MyCaptcha;
use Kkokk\Poster\Exception\PosterException;
use Illuminate\Support\Facades\Cache;

class Slider extends MyCaptcha
{

    protected $configs = [
        'src'           => '',
        'im_width'      => 340,
        'im_height'     => 251,
        'bg_width'      => 340,
        'bg_height'     => 191,
        'slider_width'  => 50,
        'slider_height' => 50,
        'slider_border' => 2,
    ];  // 验证码图片配置

    public function config($param = [])
    {
        if(empty($param)) return $this;
        if(PHP_VERSION < 7) {
            $this->configs['src'] = isset($param['src']) ? $param['src'] : $this->configs['src'];
            $this->configs['im_width'] = isset($param['im_width']) ? $param['im_width'] : $this->configs['im_width'];
            $this->configs['im_height'] = isset($param['im_height']) ? $param['im_height'] : $this->configs['im_height'];
            $this->configs['bg_width'] = isset($param['bg_width']) ? $param['bg_width'] : $this->configs['bg_width'];
            $this->configs['bg_height'] = isset($param['bg_height']) ? $param['bg_height'] : $this->configs['bg_height'];
            $this->configs['slider_width'] = isset($param['slider_width']) ? $param['slider_width'] : $this->configs['slider_width'];
            $this->configs['slider_height'] = isset($param['slider_height']) ? $param['slider_height'] : $this->configs['slider_height'];
            $this->configs['slider_border'] = isset($param['slider_border']) ? $param['slider_border'] : $this->configs['slider_border'];
        } else {
            $this->configs['src'] = $param['src'] ?? $this->configs['src'];
            $this->configs['im_width'] = $param['im_width'] ?? $this->configs['im_width'];
            $this->configs['im_height'] = $param['im_height'] ?? $this->configs['im_height'];
            $this->configs['bg_width'] = $param['bg_width'] ?? $this->configs['bg_width'];
            $this->configs['bg_height'] = $param['bg_height'] ?? $this->configs['bg_height'];
            $this->configs['slider_width'] = $param['slider_width'] ?? $this->configs['slider_width'];
            $this->configs['slider_height'] = $param['slider_height'] ?? $this->configs['slider_height'];
            $this->configs['slider_border'] = $param['slider_border'] ?? $this->configs['slider_border'];
        }

        return $this;
    }

    public function get()
    {

        $data = $this->draw();

        // imagepng($this->im, __DIR__.'/../../tests/poster/im.png');

        $baseData = $this->baseData($this->im);

        $key = uniqid('slider'.mt_rand(0, 9999), true);

        if(class_exists(Cache::class)){
            Cache::put($key , $data['x'], $this->expire);
        }

        return [
            'img' => $baseData,
            'key' => $key,
            'y'   => $data['y'],
        ];
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
    public function check($key, $value, $leeway = 0)
    {
        $x = Cache::pull($key);
        if(empty($x)) return false;

        $leeway = $leeway ?: $this->leeway;

        return $x >= ($value - $leeway) && $x <= ($value + $leeway);
    }

    // 实现图片绘制
    public function draw(){
        $draw = new PosterBase;
        
        $im_width = $this->configs['im_width'];
        $im_height = $this->configs['im_height'];

        $this->im = $draw->createIm($im_width, $im_height, [], true);

        $this->drawImage($this->configs['src']); // 添加bg图片

        $bg_width = $this->configs['bg_width'];
        $bg_height = $this->configs['bg_height'];

        $slider_width = $this->configs['slider_width'];
        $slider_height = $this->configs['slider_height'];
        $border = $this->configs['slider_border'];

        $w = $slider_width - $border;
        $h = $slider_height - $border;

        $bg = $draw->createIm($slider_width, $slider_height, [0, 0, 0, 60], true);
        $ims = $draw->createIm($slider_width, $slider_height, [], false);

        $x1 = mt_rand(0, $bg_width - $w);
        $x2 = $x1 + $w;

        $y1 = mt_rand(0, $bg_height - $h);
        $y2 = $y1 + $h;

        $xx1 = mt_rand(0, $bg_width - $w);
        // $xx2 = $xx1 + $slider_width;

        $yy1 = mt_rand(0, $bg_height - $h);
        // $yy2 = $yy1 + $slider_height;

        for ($i=0; $i < $bg_width; $i++) {
            for ($j=0; $j < $bg_height; $j++) {
                if( ($i < $x2 && $i >= $x1) && ($j < $y2 && $j >= $y1) ){
                    $rgbColor = imagecolorat($this->im, $i, $j);
                    imagesetpixel($ims, $i-$x1 + $border/2, $j - $y1 + $border/2, $rgbColor);
                }
            }
        }

        imagecopy($this->im, $bg, $x1, $y1, 0, 0, $slider_width, $slider_height);
        imagecopy($this->im, $bg, $xx1, $yy1, 0, 0, $slider_width, $slider_height);
        imagecopy($this->im, $ims, 5, 196, 0, 0, $slider_width, $slider_width);

        imagedestroy($bg);
        imagedestroy($ims);

        return [
            'x' => $x1,
            'y' => $y1,
        ];
    }

    protected function drawImage($src = ''){

        $src = $src ?: $this->getSliderBg($src);

        list($Width, $Hight, $bgType) = @getimagesize($src);

        $bgType = image_type_to_extension($bgType, false);

        if (empty($bgType)) throw new PosterException('image resources cannot be empty (' . $src . ')');

        if ($bgType == 'gif') {
            $pic = imagecreatefromstring(file_get_contents($src));
        } else {

            $fun = 'imagecreatefrom' . $bgType;
            $pic = @$fun($src);
        }

        imagecopy($this->im, $pic, 0, 0, 0, 0, $Width, $Hight);
    }

    protected function getSliderBg(){
        return __DIR__.'/../style/slider_bg/layer0'.mt_rand(1,3).'.jpg';
    }

}