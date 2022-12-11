<?php
/**
 * @Author lang
 * @Email: 732853989@qq.com
 * Date: 2022/12/11
 * Time: 下午9:40
 */

namespace Kkokk\Poster\Captcha;


use Kkokk\Poster\Base\PosterBase;

class Click extends \Kkokk\Poster\Abstracts\MyCaptcha
{

    protected $configs = [
        'src'           => '',
        'im_width'      => 256,
        'im_height'     => 256,
        'font_family'   => __DIR__ . '/../style/simkai.ttf',
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
        // TODO: Implement check() method.
    }

    public function get()
    {
        $data = $this->draw();

        imagepng($this->im, __DIR__.'/../../tests/poster/click.png');
    }

    public function draw()
    {
        $draw = new PosterBase;

        $im_width = $this->configs['im_width'];
        $im_height = $this->configs['im_height'];

        $this->im = $draw->createIm($im_width, $im_height, [mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), 1], false);

        $this->drawText($draw); // 字
    }

    public function drawText($draw){
        $font_family = $this->configs['font_family'];
        $font = 32;
        $contents = '浪迹天涯';
        $rgba = [];
        $color = $draw->createColorAlpha($this->im, [52, 52, 52, 1]);

        for ($i=0; $i < mb_strlen($contents); $i++) {
            $content = mb_substr($contents, $i, 1);
            imagettftext($this->im, $font, mt_rand(0, 180), mt_rand(30, 236), mt_rand(30, 236), $color, $font_family, $content);
        }

    }
}