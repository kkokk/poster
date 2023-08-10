<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/29
 * Time: 14:50
 */

namespace Kkokk\Poster\Captcha\Traits;


trait ClickTrait
{
    public function draw()
    {

        $im_width = $this->configs['im_width'];
        $im_height = $this->configs['im_height'];
        $bg_width = $this->configs['bg_width'];
        $bg_height = $this->configs['bg_height'];

        $this->im = $this->PosterDriver->createIm($bg_width, $bg_height, [], true);
        $bg = $this->PosterDriver->createIm($im_width, $im_height, [], true);

        $this->drawImage($this->configs['src'], true);

        imagecopy($bg, $this->im, 0, 0, 0, 0, $bg_width, $bg_height);

        $this->im = $bg;

        $this->drawLine($bg_width, $bg_height); // 干扰线

        $this->drawChar($bg_width, $bg_height); // 干扰字符

        $data = $this->drawText(); // 字

        return $data;
    }

    // 计算 三个点的叉乘 |p1 p2| X |p1 p|
    public function getCross($p1, $p2, $p)
    {
        // (p2.x - p1.x) * (p.y - p1.y) -(p.x - p1.x) * (p2.y - p1.y);
        return ($p1[0] - $p[0]) * ($p2[1] - $p[1]) - ($p2[0] - $p[0]) * ($p1[1] - $p[1]);
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
                    mt_rand($font, $bg_height - $font / 2 - 12),
                ];
                $space[] = [
                    mt_rand($bg_width / 2, $bg_width - $font),
                    mt_rand($font, $bg_height - $font / 2 - 12),
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
                    mt_rand($bg_height / 2 + $font, $bg_height - $font / 2 - 12),
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
                    mt_rand($bg_height / 2 + $font, $bg_height - $font / 2 - 12),
                ];
                $space[] = [
                    mt_rand($bg_width / 2, $bg_width - $font),
                    mt_rand($bg_height / 2 + $font, $bg_height - $font / 2 - 12),
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

        $color = $this->PosterDriver->createColorAlpha($this->im, [255, 255, 255, 1]);

        $spaces = $this->getSpace($contentsLen);

        $content = "";

        foreach ($contents as $k => $v) {
            $content .= $v['contents'];
            // 随机获取位置
            $spaceKey = mt_rand(0, count($spaces) - 1);
            $space = array_splice($spaces, $spaceKey, 1);
            $angle = mt_rand(-80, 80); // 旋转角度
            $fontBox = imagettfbbox($font, $angle, $font_family, $v['contents']); // 计算文字方框坐标
            $x = $space[0][0]; // 起始x坐标
            $y = $space[0][1]; // 起始y坐标
            $contents[$k]['point'] = [
                $x + $fontBox[0], // 左下角,X 位置
                $y + $fontBox[1], // 左下角，Y 位置
                $x + $fontBox[2], // 右下角，X 位置
                $y + $fontBox[3], // 右下角，Y 位置
                $x + $fontBox[4], // 右上角，X 位置
                $y + $fontBox[5], // 右上角，Y 位置
                $x + $fontBox[6], // 左上角，X 位置
                $y + $fontBox[7], // 左上角，Y 位置
                $angle, // 旋转角度
            ];
            imagettftext($this->im, $font, $angle, $x, $y, $color, $font_family, $v['contents']);
            // 加粗字体
            $ttfCount = 6;
            for ($j = 1; $j <= $ttfCount; $j++) {
                // 随机颜色
                $ttfColor = $this->PosterDriver->createColorAlpha($this->im, [mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), 1]);
                imagettftext($this->im, $font - ($j * 2), $angle, $x + $j, $y - $j, $ttfColor, $font_family, $v['contents']);
            }
        }

        // 显示字体为黑色
        $color = $this->PosterDriver->createColorAlpha($this->im, [0, 0, 0, 1]);

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
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . 'rotate_bg' . DIRECTORY_SEPARATOR . 'rotate0' . mt_rand(1, 5) . '.jpg';
    }
}