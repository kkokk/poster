<?php
/**
 * @Author lang
 * @Email: 732853989@qq.com
 * Date: 2022/12/11
 * Time: 下午9:40
 */

namespace Kkokk\Poster\Captcha;

use Kkokk\Poster\Abstracts\MyCaptcha;

class Click extends MyCaptcha
{

    protected $configs = [
        'src'           => '',
        'im_width'      => 256,
        'im_height'     => 256,
        'font_family'   => __DIR__ . '/../style/zhankukuheiti.ttf', // 感谢站酷提供免费商用站酷库黑体、可自定义炫酷字体文件（绝对路径）
        'font_size'     => 32, // 字体大小
        'line_count'    => 0, // 干扰线数量
        'char_count'    => 0, // 干扰字符数量
    ];  // 验证码图片配置

    public function config($param = [])
    {
        if(empty($param)) return $this;
        if(PHP_VERSION < 7) {
            $this->configs['src'] = isset($param['src']) ? $param['src'] : $this->configs['src'];
            $this->configs['im_width'] = isset($param['im_width']) ? $param['im_width'] : $this->configs['im_width'];
            $this->configs['im_height'] = isset($param['im_height']) ? $param['im_height'] : $this->configs['im_height'];
            $this->configs['font_family'] = isset($param['font_family']) ? $param['font_family'] : $this->configs['font_family'];
            $this->configs['font_size'] = isset($param['font_size']) ? $param['font_size'] : $this->configs['font_size'];
            $this->configs['line_count'] = isset($param['line_count']) ? $param['line_count'] : $this->configs['line_count'];
            $this->configs['char_count'] = isset($param['char_count']) ? $param['line_count'] : $this->configs['char_count'];
        } else {
            $this->configs['src'] = $param['src'] ?? $this->configs['src'];
            $this->configs['im_width'] = $param['im_width'] ?? $this->configs['im_width'];
            $this->configs['im_height'] = $param['im_height'] ?? $this->configs['im_height'];
            $this->configs['font_family'] = $param['font_family'] ?? $this->configs['font_family'];
            $this->configs['font_size'] = $param['font_size'] ?? $this->configs['font_size'];
            $this->configs['line_count'] = $param['line_count'] ?? $this->configs['line_count'];
            $this->configs['char_count'] = $param['char_count'] ?? $this->configs['char_count'];
        }

        return $this;
    }

    public function check($key, $value, $leeway = 0)
    {
        // TODO: Implement check() method.
    }

    public function get($expire = 0)
    {
        $data = $this->draw();

        imagepng($this->im, __DIR__.'/../../tests/poster/click.png');
    }

    public function draw()
    {

        $im_width = $this->configs['im_width'];
        $im_height = $this->configs['im_height'];

        $this->im = $this->PosterBase->createIm($im_width, $im_height, [mt_rand(125, 255), 255, mt_rand(125, 255), 1], false);

        if($this->configs['src']) { // 如果指定背景则用背景
            $this->drawImage($this->configs['src'], true);
        }

        $this->drawText(); // 字
    }

    public function drawText(){
        $font_family = $this->configs['font_family'];
        $font = $this->configs['font_size'];
        $fontSmall = $this->configs['font_size'] - 2;
        $contents = '浪迹天涯';

        $color = $this->PosterBase->createColorAlpha($this->im, [255, 255, 255, 1]);

        for ($i=0; $i < mb_strlen($contents); $i++) {
            $content = mb_substr($contents, $i, 1);
            $x = mt_rand(40, 216);
            $y = mt_rand(40, 216);
            $angle = mt_rand(0, 180);
            imagettftext($this->im, $font, $angle, $x, $y, $color, $font_family, $content);
            $colorNew = $this->PosterBase->createColorAlpha($this->im, [mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), 1]);
            imagettftext($this->im, $fontSmall, $angle, $x + 1, $y - 1, $colorNew, $font_family, $content);
        }
    }
}