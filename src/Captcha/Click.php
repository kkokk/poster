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
        'src' => '',
        'im_width' => 256,
        'im_height' => 306,
        'bg_width' => 256,
        'bg_height' => 256,
        'type' => 'text', // text 汉字 number 数字 alpha_num 字母和数字
        'font_family' => __DIR__ . '/../style/zhankukuheiti.ttf', // 感谢站酷提供免费商用站酷库黑体、可自定义炫酷字体文件（绝对路径）
        'contents' => '', // 自定义文字
        'font_size' => 42, // 字体大小
        'font_count' => 0, // 字体大小
        'line_count' => 0, // 干扰线数量
        'char_count' => 0, // 干扰字符数量
    ];  // 验证码图片配置

    public function config($param = [])
    {
        if (empty($param)) return $this;
        if (PHP_VERSION < 7) {
            $this->configs['src'] = isset($param['src']) ? $param['src'] : $this->configs['src'];
            $this->configs['contents'] = isset($param['contents']) ? $param['contents'] : $this->configs['contents'];
            $this->configs['font_family'] = isset($param['font_family']) ? $param['font_family'] : $this->configs['font_family'];
            $this->configs['font_size'] = isset($param['font_size']) ? $param['font_size'] : $this->configs['font_size'];
            $this->configs['font_count'] = isset($param['font_count']) ? $param['font_count'] : $this->configs['font_count'];
            $this->configs['line_count'] = isset($param['line_count']) ? $param['line_count'] : $this->configs['line_count'];
            $this->configs['char_count'] = isset($param['char_count']) ? $param['line_count'] : $this->configs['char_count'];
        } else {
            $this->configs['src'] = $param['src'] ?? $this->configs['src'];
            $this->configs['contents'] = $param['contents'] ?? $this->configs['contents'];
            $this->configs['font_family'] = $param['font_family'] ?? $this->configs['font_family'];
            $this->configs['font_size'] = $param['font_size'] ?? $this->configs['font_size'];
            $this->configs['font_count'] = $param['font_count'] ?? $this->configs['font_count'];
            $this->configs['line_count'] = $param['line_count'] ?? $this->configs['line_count'];
            $this->configs['char_count'] = $param['char_count'] ?? $this->configs['char_count'];
        }

        if ($this->configs['contents']) $this->configs['font_count'] = mb_strlen($this->configs['contents']);

        return $this;
    }

    public function check($key, $value, $leeway = 0)
    {
        // if (class_exists(Cache::class)) {
        //     $contents = Cache::pull($key);
        // } else {
        //     return false;
        // }

        // if (empty($contents)) return false;

        $value = json_decode($value, true);

        // print_r($value);exit;

        $contents = '{"content":"红烧猪蹄","content_width":117,"content_height":20,"x":10,"y":276,"contents":[{"contents":"红","point":[134,63,190,63,190,100,134,100,1]},{"contents":"烧","point":[165,209,221,209,221,246,165,246,4]},{"contents":"猪","point":[60,98,116,98,116,135,60,135,35]},{"contents":"蹄","point":[71,221,127,221,127,257,71,257,12]}]}';

        $points = json_decode($contents, true);

        if(count($points['contents']) != count($value)) return false;

        // 第四象限
        foreach ($points['contents'] as $k => $v) {
            $point = $v['point'];
            // 原点
            $x1 = $point[0];
            $y1 = -$point[1];

            // 任意坐标点
            $x2 = $value[$k]['x'];
            $y2 = -$value[$k]['y'];

            // 旋转角度 正 逆时针 负顺时针
            $angle = -$point[8];

            // 顺时针旋转后的点
            $x3 = ($x2 - $x1) * cos($angle) - ($y2 - $y1) * sin($angle) + $x1;
            $y3 = ($y2 - $y1) * cos($angle) + ($x2 - $x1) * sin($angle) + $y1;

            // 逆时针旋转后的点
            // $x3 = ($x2 - $x1) * cos($angle) - ($y2 - $y1) * sin($angle) + $x1;
            // $y3 = ($x2 - $x1) * sin($angle) + ($y2 - $y1) * cos($angle) + $y1;
            $y3_abs = abs($y3);
            if (($x3 >= $point[0] && $x3 <= $point[2]) && ($y3_abs >= $point[1] && $y3_abs <= $point[7])) {
                continue;
            } else {
                print_r($x3);
                print_r(PHP_EOL);
                print_r($point[0]);
                print_r(PHP_EOL);
                print_r($point[2]);
                print_r(PHP_EOL);
                print_r($y3_abs);
                print_r(PHP_EOL);
                print_r($point[1]);
                print_r(PHP_EOL);
                print_r($point[7]);
                print_r($v['contents']);
                return false;
            }
        }

        return true;
    }

    public function get($expire = 0)
    {

        $data = $this->draw();

        imagepng($this->im, __DIR__ . '/../../tests/poster/click.png');
        // imagejpeg($this->im, __DIR__.'/../../tests/poster/click.jpg',20);

        $baseData = $this->baseData($this->im, 'jpg');

        $key = uniqid('input:' . $this->configs['type'] . mt_rand(0, 9999), true);

        if (class_exists(Cache::class)) {
            Cache::put($key, json_encode($data['contents']), $expire ?: $this->expire);
        }

        // print_r(json_encode($data['contents']));
        print_r(json_encode($data));


        return [
            'img' => $baseData,
            'content_width' => $data['content_width'],
            'content_height' => $data['content_height'],
            'x' => $data['x'],
            'y' => $data['y'],
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

    public function getContents($contentsLen)
    {

        $contents = [];

        if ($this->configs['contents']) {

            for ($i = 0; $i < $contentsLen; $i++) {
                $contents[$i]['contents'] = mb_substr($this->configs['contents'], $i, 1);
            }

        } else {

            $str = $this->getChar('text');

            for ($i = 0; $i < $contentsLen; $i++) {
                $contents[$i]['contents'] = mb_substr($str, mt_rand(0, 299), 1);
            }

        }

        return $contents;
    }

    public function getSpace($contentsLen)
    {

        $font = $this->configs['font_size'] + 15;
        $bg_width = $this->configs['bg_width'];
        $bg_height = $this->configs['bg_width'];

        switch ($contentsLen) {
            case 2:
                $space[] = [
                    mt_rand($font, $bg_width / 2 - $font),
                    mt_rand($font, $bg_height),
                ];
                $space[] = [
                    mt_rand($bg_width / 2, $bg_width - $font),
                    mt_rand($font, $bg_height),
                ];
                break;
            case 3:
                $space[] = [
                    mt_rand($font, $bg_width / 2 - $font),
                    mt_rand($font, $bg_height / 2),
                ];
                $space[] = [
                    mt_rand($bg_width / 2, $bg_width - $font),
                    mt_rand($font, $bg_height / 2),
                ];
                $space[] = [
                    mt_rand($font, $bg_width - $font),
                    mt_rand($bg_height / 2, $bg_height),
                ];
                break;
            default:
                $space[] = [
                    mt_rand($font, $bg_width / 2 - $font),
                    mt_rand($font, $bg_height / 2),
                ];
                $space[] = [
                    mt_rand($bg_width / 2, $bg_width - $font),
                    mt_rand($font, $bg_height / 2),
                ];
                $space[] = [
                    mt_rand($font, $bg_width / 2 - $font),
                    mt_rand($bg_height / 2 + $font, $bg_height),
                ];
                $space[] = [
                    mt_rand($bg_width / 2, $bg_width - $font),
                    mt_rand($bg_height / 2 + $font, $bg_height),
                ];
                break;
        }

        return $space;
    }

    public function drawText()
    {
        $font_family = $this->configs['font_family'];
        $font = $this->configs['font_size'];

        $contentsLen = $this->configs['font_count'] ?: mt_rand(2, 4);
        $contentsLen = $contentsLen < 2 ? 2 : ($contentsLen > 4 ? 4 : $contentsLen);

        $contents = $this->getContents($contentsLen);

        $color = $this->PosterBase->createColorAlpha($this->im, [255, 255, 255, 1]);

        $spaces = $this->getSpace($contentsLen);

        $content = "";

        foreach ($contents as $k => $v) {
            $content .= $v['contents'];
            $spaceKey = mt_rand(0, count($spaces) - 1);
            $space = array_splice($spaces, $spaceKey, 1);
            $angle = mt_rand(0, 45);
            $fontBox = imagettfbbox($font, 0, $font_family, $v['contents']); // 计算文字长宽
            $font_width = $fontBox[2]; // 字体宽
            $font_height = abs($fontBox[7]);// 字体高
            $x = $space[0][0];
            $y = $space[0][1];
            $contents[$k]['point'] = [
                $x + $fontBox[0], // 左下角,X 位置
                $y + $fontBox[1], // 左下角，Y 位置
                $x + $fontBox[0] + $font_width, // 右下角，X 位置
                $y + $fontBox[1], // 右下角，Y 位置
                $x + $fontBox[0] + $font_width, // 右上角，X 位置
                $y + $fontBox[1] + $font_height, // 右上角，Y 位置
                $x + $fontBox[0], // 左上角，X 位置
                $y + $fontBox[1] + $font_height, // 左上角，Y 位置
                $angle, // 旋转角度
            ];
            imagettftext($this->im, $font, $angle, $x, $y, $color, $font_family, $v['contents']);
            // 加粗字体
            $ttfCount = 6;
            for ($j = 1; $j <= $ttfCount; $j++) {
                $ttfColor = $this->PosterBase->createColorAlpha($this->im, [mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), 1]);
                imagettftext($this->im, $font - ($j * 2), $angle, $x + $j, $y - $j, $ttfColor, $font_family, $v['contents']);
            }
        }

        $color = $this->PosterBase->createColorAlpha($this->im, [0, 0, 0, 1]);

        $viewFont = 22; // 显示字体大小
        $fontBox = imagettfbbox($viewFont, 0, $font_family, $content); // 计算文字长宽
        $viewHeight = 296;  // 显示字体y坐标
        imagettftext($this->im, $viewFont, 0, 10, $viewHeight, $color, $font_family, $content);

        $content_height = abs($fontBox[7]) + 1;
        return [
            'content' => $content,
            'content_width' => $fontBox[2],
            'content_height' => $content_height,
            'x' => 10,
            'y' => $viewHeight - $content_height,
            'contents' => $contents,
        ];

    }

    protected function getImBg()
    {
        return __DIR__ . '/../style/rotate_bg/rotate0' . mt_rand(1, 5) . '.jpg';
    }
}