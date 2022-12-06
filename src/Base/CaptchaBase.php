<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/6
 * Time: 18:21
 */

namespace Kkokk\Poster\Base;

use Kkokk\Poster\Exception\PosterException;

class CaptchaBase
{
    protected $im;
    protected $bgw;
    protected $bgh;
    protected $w;
    protected $sliderConfig = [
        'src' => '',
        'im_width'      => 340,
        'im_height'     => 251,
        'bg_width'      => 340,
        'bg_height'     => 191,
        'slider_width'  => 50,
        'slider_height' => 50,
        'slider_border' => 2,
    ];

    public function __construct()
    {

    }

    protected function getSliderBg(){
        $slider_bg =  __DIR__ . '/../style/slider_bg/layer0'.mt_rand(1,3).'.png';
        return $slider_bg;
    }

    public function CopyImage($im){

        $src = $this->sliderConfig['src'];

        list($Width, $Hight, $bgType) = @getimagesize($src);

        $bgType = image_type_to_extension($bgType, false);

        if (empty($bgType)) throw new PosterException('image resources cannot be empty (' .  $src . ')');

        if ($bgType == 'gif') {
            $pic = imagecreatefromstring(file_get_contents($src));
        } else {

            $fun = 'imagecreatefrom' . $bgType;
            $pic = @$fun($src);
        }

        imagecopy($im, $pic, 0, 0, 0, 0, $Width, $Hight);

        return $im;
    }

    public function drawSliderIm(){
        $draw = new PosterBase();
        $this->im = $draw->createIm($this->sliderConfig['im_width'], $this->sliderConfig['im_height'], [], true);

        $this->im = $this->CopyImage($this->im);

        $w = $this->sliderConfig['bg_width'];
        $h = $this->sliderConfig['bg_height'];

        $ww = $this->sliderConfig['slider_width'];
        $hh = $this->sliderConfig['slider_height'];
        $border = $this->sliderConfig['slider_border'];

        $bg = $draw->createIm($ww, $hh, [], true);
        $white = imagecolorallocatealpha($bg, 0, 0, 0, 80);
        imagefill($bg, 0, 0, $white);

        $ims = $draw->createIm($ww + 2, $hh + 2, [], false);

        $x1 = mt_rand(0, $w - $ww);
        $x2 = $x1 + $ww;

        $y1 = mt_rand(0, $h - $hh);
        $y2 = $y1 + $hh;

        $xx1 = mt_rand(0, $w - $ww);
        // $xx2 = $xx1 + $ww;

        $yy1 = mt_rand(0, $h - $hh);
        // $yy2 = $yy1 + $hh;

        for ($i=0; $i < $w; $i++) {
            for ($j=0; $j < $h; $j++) {
                if( ($i < $x2 && $i > $x1) && ($j < $y2 && $j > $y1) ){
                    $rgbColor = imagecolorat($this->im, $i, $j);
                    imagesetpixel($ims, $i-$x1 + 1, $j - $y1 + 1, $rgbColor);
                }
            }
        }

        imagecopy($this->im, $bg, $x1, $y1, 0, 0, $ww, $hh);
        imagecopy($this->im, $bg, $xx1, $yy1, 0, 0, $ww, $hh);
        imagecopy($this->im, $ims, 5, 196, 0, 0, $ww+2, $hh+2);

        return [];
    }
    
    public function buildSlider($src, $im_width, $im_height, $bg_width, $bg_height, $slider_width, $slider_height, $slider_border){
        $this->sliderConfig['im_width'] = $im_width;
        $this->sliderConfig['im_height'] = $im_height;
        $this->sliderConfig['bg_width'] = $bg_width;
        $this->sliderConfig['bg_height'] = $bg_height;
        $this->sliderConfig['slider_width'] = $slider_width;
        $this->sliderConfig['slider_height'] = $slider_height;
        $this->sliderConfig['slider_border'] = $slider_border;

        $this->sliderConfig['src'] = $src ?: $this->getSliderBg();

        $res = $this->drawSliderIm();

        return $res;
    }
}