<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/29
 * Time: 14:51
 */

namespace Kkokk\Poster\Captcha\Traits;


trait InputTrait
{
    public function draw()
    {

        $im_width = $this->configs['im_width'];
        $im_height = $this->configs['im_height'];

        $this->im = $this->PosterDriver->createIm($im_width, $im_height, [mt_rand(125, 255), 255, mt_rand(125, 255), 1], false);

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
                $color = $this->PosterDriver->createColorAlpha($this->im, [mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), 1]);
                $content = $v;
                $x = mt_rand($k * $equally + 10, ($k + 1) * $equally - $font);
                $y = mt_rand($font + 10, $im_height);
                $angle = $this->configs['type'] === 'math' ? 0 : mt_rand(0, 45);
                imagettftext($this->im, $font, $angle, $x, $y, $color, $font_family, $content);
            }

        } else {
            $equally = $im_width / mb_strlen($contents);

            for ($i = 0; $i < mb_strlen($contents); $i++) {
                $color = $this->PosterDriver->createColorAlpha($this->im, [mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), 1]);
                $content = mb_substr($contents, $i, 1);
                $x = mt_rand($i * $equally + 10, ($i + 1) * $equally - $font);
                $y = mt_rand($font + 10, $im_height);
                $angle = mt_rand(0, 45);
                imagettftext($this->im, $font, $angle, $x, $y, $color, $font_family, $content);
            }
        }


    }
}