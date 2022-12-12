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
        'im_height'     => 316,
        'bg_width'      => 256,
        'bg_height'     => 256,
        'type'          => 'text', // text 汉字 number 数字 alpha_num 字母和数字
        'font_family'   => __DIR__ . '/../style/zhankukuheiti.ttf', // 感谢站酷提供免费商用站酷库黑体、可自定义炫酷字体文件（绝对路径）
        'font_size'     => 42, // 字体大小
        'font_count'    => 0, // 字体大小
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
            $this->configs['bg_width'] = isset($param['bg_width']) ? $param['bg_width'] : $this->configs['bg_width'];
            $this->configs['bg_height'] = isset($param['bg_height']) ? $param['bg_height'] : $this->configs['bg_height'];
            $this->configs['font_family'] = isset($param['font_family']) ? $param['font_family'] : $this->configs['font_family'];
            $this->configs['font_size'] = isset($param['font_size']) ? $param['font_size'] : $this->configs['font_size'];
            $this->configs['font_count'] = isset($param['font_count']) ? $param['font_count'] : $this->configs['font_count'];
            $this->configs['line_count'] = isset($param['line_count']) ? $param['line_count'] : $this->configs['line_count'];
            $this->configs['char_count'] = isset($param['char_count']) ? $param['line_count'] : $this->configs['char_count'];
        } else {
            $this->configs['src'] = $param['src'] ?? $this->configs['src'];
            $this->configs['im_width'] = $param['im_width'] ?? $this->configs['im_width'];
            $this->configs['im_height'] = $param['im_height'] ?? $this->configs['im_height'];
            $this->configs['bg_width'] = $param['bg_width'] ?? $this->configs['bg_width'];
            $this->configs['bg_height'] = $param['bg_height'] ?? $this->configs['bg_height'];
            $this->configs['font_family'] = $param['font_family'] ?? $this->configs['font_family'];
            $this->configs['font_size'] = $param['font_size'] ?? $this->configs['font_size'];
            $this->configs['font_count'] = $param['font_count'] ?? $this->configs['font_count'];
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
        // imagejpeg($this->im, __DIR__.'/../../tests/poster/click.jpg',20);

        $baseData = $this->baseData($this->im, 'jpg');

        $key = uniqid('input:'.$this->configs['type'].mt_rand(0, 9999), true);

        if(class_exists(Cache::class)){
            Cache::put($key , json_encode($data['contents']), $expire ?: $this->expire);
        }

        // print_r($data);

        return [
            'img' => $baseData,
        ];
    }

    public function draw()
    {

        $im_width = $this->configs['im_width'];
        $im_height = $this->configs['im_height'];
        $bg_width = $this->configs['bg_width'];
        $bg_height = $this->configs['bg_height'];


        $this->im = $this->PosterBase->createIm($bg_width, $bg_height, [], true);
        $bg = $this->PosterBase->createIm($im_width, $im_height, [], true);

        $this->drawImage($this->configs['src'], true);

        imagecopy($bg, $this->im, 0, 0, 0, 0, $bg_width, $bg_height);

        $this->im = $bg;

        $this->drawLine(); // 干扰线

        $this->drawChar(); // 干扰字符

        $data = $this->drawText(); // 字

        return $data;
    }

    public function getContents($contentsLen){

        $str = $this->getChar('text');

        $contents = [];

        for ($i=0; $i < $contentsLen; $i++) {
            $contents[$i]['contents'] = mb_substr($str, mt_rand(0, 299), 1);
        }

        return $contents;
    }

    public function getSpace($contentsLen){

        $font = $this->configs['font_size'] + 15;
        $bg_width = $this->configs['bg_width'];
        $bg_height = $this->configs['bg_width'];

        switch ($contentsLen) {
            case 2:
                $space[] = [
                    mt_rand($font, $bg_width/2 - $font),
                    mt_rand($font, $bg_height),
                ];
                $space[] = [
                    mt_rand($bg_width/2, $bg_width - $font),
                    mt_rand($font, $bg_height),
                ];
                break;
            case 3:
                $space[] = [
                    mt_rand($font, $bg_width/2 - $font),
                    mt_rand($font, $bg_height/2),
                ];
                $space[] = [
                    mt_rand($bg_width/2, $bg_width - $font),
                    mt_rand($font, $bg_height/2),
                ];
                $space[] = [
                    mt_rand($font, $bg_width - $font),
                    mt_rand($bg_height/2, $bg_height),
                ];
                break;
            default:
                $space[] = [
                    mt_rand($font, $bg_width/2 - $font),
                    mt_rand($font, $bg_height/2),
                ];
                $space[] = [
                    mt_rand($bg_width/2, $bg_width - $font),
                    mt_rand($font, $bg_height/2),
                ];
                $space[] = [
                    mt_rand($font, $bg_width/2- $font),
                    mt_rand($bg_height/2 + $font, $bg_height),
                ];
                $space[] = [
                    mt_rand($bg_width/2, $bg_width - $font),
                    mt_rand($bg_height/2 + $font, $bg_height),
                ];
                break;
        }

        return $space;
    }

    public function drawText(){
        $font_family = $this->configs['font_family'];
        $font = $this->configs['font_size'];
        $fontSmall = $this->configs['font_size'] - 2;

        $contentsLen = $this->configs['font_count'] ?: mt_rand(2, 4);
        $contentsLen = $contentsLen < 2 ? 2 : ($contentsLen > 4 ? 4 : $contentsLen);

        $contents = $this->getContents($contentsLen);

        $color = $this->PosterBase->createColorAlpha($this->im, [255, 255, 255, 1]);

        $spaces = $this->getSpace($contentsLen);

        $content = "";

        foreach ($contents as $k => $v){
            $content .= $v['contents'];
            $spaceKey =mt_rand(0, count($spaces) - 1);
            $space = array_splice($spaces, $spaceKey, 1);
            $x = $space[0][0];
            $y = $space[0][1];
            $angle = mt_rand(0, 45);
            $contents[$k]['point'] = [
                $x,
                $y,
                $angle
            ];
            imagettftext($this->im, $font, $angle, $x, $y, $color, $font_family, $v['contents']);
            $colorNew = $this->PosterBase->createColorAlpha($this->im, [mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), 1]);
            imagettftext($this->im, $fontSmall, $angle, $x + 1, $y - 1, $colorNew, $font_family, $v['contents']);
        }

        $color = $this->PosterBase->createColorAlpha($this->im, [0, 0, 0, 1]);

        imagettftext($this->im, 28, 0, 10, 296, $color, $font_family, $content);

        return [
            'content' => $content,
            'content_width' => 28 * ($contentsLen+1),
            'contents' => $contents,
        ];

    }

    protected function getImBg(){
        return __DIR__.'/../style/rotate_bg/rotate0'.mt_rand(1,5).'.jpg';
    }
}