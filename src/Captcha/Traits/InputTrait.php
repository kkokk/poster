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
    protected function draw()
    {
        $imageWidth = $this->configs['im_width'];
        $imageHeight = $this->configs['im_height'];
        if ($this->configs['src']) {
            // 如果指定背景则用背景
            $this->driver->ImDst($this->configs['src'], $imageWidth, $imageHeight);
        } else {
            $this->driver->Im($imageWidth, $imageHeight, [mt_rand(125, 255), 255, mt_rand(125, 255), 1]);
        }

        $this->drawLine(); // 干扰线

        $this->drawChar(); // 干扰字

        $res = $this->getContents();

        $this->drawText($res['contents']); // 字

        return $res;
    }

    protected function drawText($contents)
    {
        $font = $this->configs['font_family'];
        $fontSize = $this->configs['font_size'];
        $imageWidth = $this->configs['im_width'];
        $imageHeight = $this->configs['im_height'];

        if (is_array($contents)) {
            $equally = $imageWidth / count($contents);
            foreach ($contents as $k => $v) {
                $color = [mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), 1];
                $content = $v;
                $x = mt_rand($k * $equally + 10, ($k + 1) * $equally - $fontSize);
                $y = mt_rand($fontSize, $imageHeight - 10);
                $angle = $this->configs['type'] === 'math' ? 0 : mt_rand(0, 45);
                $this->driver->CopyText($content, $x, $y, $fontSize, $color, null, $font, null, null, $angle);
            }

        } else {
            $equally = $imageWidth / mb_strlen($contents);
            for ($i = 0; $i < mb_strlen($contents); $i++) {
                $color = [mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), 1];
                $content = mb_substr($contents, $i, 1);
                $x = mt_rand($i * $equally + 10, ($i + 1) * $equally - $fontSize);
                $y = mt_rand($fontSize, $imageHeight - 10);
                $angle = mt_rand(0, 45);
                $this->driver->CopyText($content, $x, $y, $fontSize, $color, null, $font, null, null, $angle);
            }
        }
    }

    protected function getContents()
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

                if ($formula == '+') {
                    $mul = $a + $b;
                }
                if ($formula == '-') {
                    $mul = $a - $b;
                }
                if ($formula == 'x') {
                    $mul = $a * $b;
                }

                $res = [
                    'contents' => [
                        $a,
                        $formula,
                        $b,
                        '='
                    ],
                    'value'    => $mul,
                ];

                break;
            case 'text':
                $str = $this->getChar($this->configs['type']);
                for ($i = 0; $i < $fontCount; $i++) {
                    $contents .= mb_substr($str, mt_rand(0, 499), 1);
                }
                $res = [
                    'contents' => $contents,
                    'value'    => $contents,
                ];
                break;
            case 'alpha_num':
                $str = $this->getChar($this->configs['type']);
                for ($i = 0; $i < $fontCount; $i++) {
                    $contents .= substr($str, mt_rand(0, 61), 1);
                }
                $res = [
                    'contents' => $contents,
                    'value'    => $contents,
                ];
                break;
            default:
                $str = $this->getChar($this->configs['type']);
                for ($i = 0; $i < $fontCount; $i++) {
                    $contents .= substr($str, mt_rand(0, 9), 1);
                }
                $res = [
                    'contents' => $contents,
                    'value'    => $contents,
                ];
                break;
        }
        return $res;
    }
}