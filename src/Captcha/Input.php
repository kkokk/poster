<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/12
 * Time: 11:47
 */

namespace Kkokk\Poster\Captcha;


use Kkokk\Poster\Abstracts\MyCaptcha;

class Input extends MyCaptcha
{

    protected $configs = [
        'src'         => '',
        'im_width'    => 256,
        'im_height'   => 64,
        'im_type'     => 'png', // png 默认 jpg quality 质量
        'quality'     => 80,    // jpg quality 质量
        'type'        => 'number', // type = number 数字 alpha_num 字母和数字 math 计算 text 文字
        'font_family' => __DIR__ . '/../style/simkai.ttf', // 感谢站酷提供免费商用站酷库黑体、可自定义炫酷字体文件
        'font_size'   => 32, // 字体大小
        'font_count'  => 4,  // 字体长度
        'line_count'  => 5,  // 干扰线数量
        'char_count'  => 10,  // 干扰字符数量
    ];

    public function config($param = [])
    {
        if (empty($param)) return $this;
        if (PHP_VERSION < 7) {
            $this->configs['src'] = isset($param['src']) ? $param['src'] : $this->configs['src'];
            $this->configs['im_width'] = isset($param['im_width']) ? $param['im_width'] : $this->configs['im_width'];
            $this->configs['im_height'] = isset($param['im_height']) ? $param['im_height'] : $this->configs['im_height'];
            $this->configs['im_type'] = isset($param['im_type']) ? $param['im_type'] : $this->configs['im_type'];
            $this->configs['quality'] = isset($param['quality']) ? $param['quality'] : $this->configs['quality'];
            $this->configs['type'] = isset($param['type']) ? $param['type'] : $this->configs['type'];
            $this->configs['font_family'] = isset($param['font_family']) ? $param['font_family'] : $this->configs['font_family'];
            $this->configs['font_size'] = isset($param['font_size']) ? $param['font_size'] : $this->configs['font_size'];
            $this->configs['font_count'] = isset($param['font_count']) ? $param['font_count'] : $this->configs['font_count'];
            $this->configs['line_count'] = isset($param['line_count']) ? $param['line_count'] : $this->configs['line_count'];
            $this->configs['char_count'] = isset($param['char_count']) ? $param['char_count'] : $this->configs['char_count'];
        } else {
            $this->configs['src'] = $param['src'] ?? $this->configs['src'];
            $this->configs['im_width'] = $param['im_width'] ?? $this->configs['im_width'];
            $this->configs['im_height'] = $param['im_height'] ?? $this->configs['im_height'];
            $this->configs['im_type'] = $param['im_type'] ?? $this->configs['im_type'];
            $this->configs['quality'] = $param['quality'] ?? $this->configs['quality'];
            $this->configs['type'] = $param['type'] ?? $this->configs['type'];
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
        if (class_exists(Cache::class)) {
            $x = Cache::pull($key);
        } else {
            return false;
        }

        if (empty($x)) return false;

        return $x == $value;
    }

    public function get($expire = 0)
    {
        $data = $this->draw();


        $this->imOutput(
            $this->im,
            __DIR__ . '/../../tests/poster/input' . $this->configs['type'] . '.' . $this->configs['im_type'],
            $this->configs['im_type'],
            $this->configs['quality']
        );

        $baseData = $this->baseData($this->im, $this->configs['im_type']);

        $key = uniqid('input:' . $this->configs['type'] . mt_rand(0, 9999), true);

        if (class_exists(Cache::class)) {
            Cache::put($key, $data['value'], $expire ?: $this->expire);
        }

        return [
            'img' => $baseData,
        ];
    }

    public function draw()
    {

        $im_width = $this->configs['im_width'];
        $im_height = $this->configs['im_height'];

        $this->im = $this->PosterBase->createIm($im_width, $im_height, [mt_rand(125, 255), 255, mt_rand(125, 255), 1], false);

        if ($this->configs['src']) { // 如果指定背景则用背景
            $this->drawImage($this->configs['src'], false, 0, 0, 0, 0, $im_width, $im_height);
        }

        $this->drawLine(); // 干扰线

        $this->drawChar(); // 干扰字

        $res = $this->getContents();

        $this->drawText($res['contents']); // 字

        return $res;
    }

    public function getContents()
    {
        // type = number 数字 alpha_num 字母和数字 math 计算 text 文字
        $fontCount = $this->configs['font_count'];
        $contents = '';
        switch ($this->configs['type']) {
            case 'math':
                $formula = '+-x';
                $a = mt_rand(0, 20);
                $b = mt_rand(0, 20);

                $formula = substr($formula, mt_rand(0, 2), 1);

                if ($formula == '+') $mul = $a + $b;
                if ($formula == '-') $mul = $a - $b;
                if ($formula == 'x') $mul = $a * $b;

                $res = [
                    'contents' => [
                        $a,
                        $formula,
                        $b,
                        '='
                    ],
                    'value' => $mul,
                ];

                break;
            case 'text':
                $str = $this->getChar($this->configs['type']);
                for ($i = 0; $i < $fontCount; $i++) {
                    $contents .= mb_substr($str, mt_rand(0, 499), 1);
                }
                $res = [
                    'contents' => $contents,
                    'value' => $contents,
                ];
                break;
            case 'alpha_num':
                $str = $this->getChar($this->configs['type']);
                for ($i = 0; $i < $fontCount; $i++) {
                    $contents .= substr($str, mt_rand(0, 61), 1);
                }
                $res = [
                    'contents' => $contents,
                    'value' => $contents,
                ];
                break;
            default:
                $str = $this->getChar($this->configs['type']);
                for ($i = 0; $i < $fontCount; $i++) {
                    $contents .= substr($str, mt_rand(0, 9), 1);
                }
                $res = [
                    'contents' => $contents,
                    'value' => $contents,
                ];
                break;
        }

        return $res;
    }

    public function drawText($contents)
    {
        $font_family = $this->configs['font_family'];
        $font = $this->configs['font_size'];
        $im_width = $this->configs['im_width'];
        $im_height = $this->configs['im_height'];

        if (is_array($contents)) {
            $equally = $im_width / count($contents);
            foreach ($contents as $k => $v) {
                $color = $this->PosterBase->createColorAlpha($this->im, [mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), 1]);
                $content = $v;
                $x = mt_rand($k * $equally + 10, ($k + 1) * $equally - $font);
                $y = mt_rand($font + 10, $im_height);
                $angle = $this->configs['type'] === 'math' ? 0 : mt_rand(0, 45);
                imagettftext($this->im, $font, $angle, $x, $y, $color, $font_family, $content);
            }

        } else {
            $equally = $im_width / mb_strlen($contents);

            for ($i = 0; $i < mb_strlen($contents); $i++) {
                $color = $this->PosterBase->createColorAlpha($this->im, [mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), 1]);
                $content = mb_substr($contents, $i, 1);
                $x = mt_rand($i * $equally + 10, ($i + 1) * $equally - $font);
                $y = mt_rand($font + 10, $im_height);
                $angle = mt_rand(0, 45);
                imagettftext($this->im, $font, $angle, $x, $y, $color, $font_family, $content);
            }
        }


    }
}