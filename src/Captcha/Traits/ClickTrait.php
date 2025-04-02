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
    protected function draw()
    {
        $imageWidth = $this->configs['im_width'];
        $imageHeight = $this->configs['im_height'];
        $bgWidth = $this->configs['bg_width'];
        $bgHeight = $this->configs['bg_height'];

        $this->driver->Im($imageWidth, $imageHeight, [], true);
        $bgImage = $this->configs['src'] ?: $this->getBackgroundImage();
        $this->driver->CopyImage($bgImage, 0, 0, 0, 0, $bgWidth, $bgHeight);

        $this->drawLine($bgWidth, $bgHeight); // 干扰线

        $this->drawChar($bgWidth, $bgHeight); // 干扰字符

        return $this->drawText(); // 字
    }

    protected function getContents($contentsLen)
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

    protected function getSpace($contentsLen)
    {
        $fontSize = $this->configs['font_size'] + 15;
        $bgWidth = $this->configs['bg_width'];
        $bgHeight = $this->configs['bg_width'];
        switch ($contentsLen) {
            case 2:
                $space[] = [
                    mt_rand($fontSize, round($bgWidth / 2 - $fontSize)),
                    mt_rand($fontSize, round($bgHeight - $fontSize / 2 - 12)),
                ];
                $space[] = [
                    mt_rand(round($bgWidth / 2), $bgWidth - $fontSize),
                    mt_rand($fontSize, round($bgHeight - $fontSize / 2 - 12)),
                ];
                break;
            case 3:
                $space[] = [
                    mt_rand($fontSize, round($bgWidth / 2 - $fontSize)),
                    mt_rand($fontSize, round($bgHeight / 2)),
                ];
                $space[] = [
                    mt_rand(round($bgWidth / 2), $bgWidth - $fontSize),
                    mt_rand($fontSize, round($bgHeight / 2)),
                ];
                $space[] = [
                    mt_rand($fontSize, $bgWidth - $fontSize),
                    mt_rand(round($bgHeight / 2 + $fontSize), round($bgHeight - $fontSize / 2 - 12)),
                ];
                break;
            default:
                $space[] = [
                    mt_rand($fontSize, round($bgWidth / 2 - $fontSize)),
                    mt_rand($fontSize, round($bgHeight / 2)),
                ];
                $space[] = [
                    mt_rand(round($bgWidth / 2), $bgWidth - $fontSize),
                    mt_rand($fontSize, round($bgHeight / 2)),
                ];
                $space[] = [
                    mt_rand($fontSize, round($bgWidth / 2 - $fontSize)),
                    mt_rand(round($bgHeight / 2 + $fontSize), round($bgHeight - $fontSize / 2 - 12)),
                ];
                $space[] = [
                    mt_rand(round($bgWidth / 2), $bgWidth - $fontSize),
                    mt_rand(round($bgHeight / 2 + $fontSize), round($bgHeight - $fontSize / 2 - 12)),
                ];
                break;
        }

        return $space;
    }

    public function drawText()
    {
        $font = $this->configs['font_family'];
        $fontSize = $this->configs['font_size'];

        $contentsLen = $this->configs['font_count'] ?: mt_rand(2, 4);
        $contentsLen = $contentsLen < 2 ? 2 : ($contentsLen > 4 ? 4 : $contentsLen);

        $contents = $this->getContents($contentsLen);

        $spaces = $this->getSpace($contentsLen);

        $content = "";

        foreach ($contents as $k => $v) {
            $content .= $v['contents'];
            // 随机获取位置
            $spaceKey = mt_rand(0, count($spaces) - 1);
            $space = array_splice($spaces, $spaceKey, 1);
            $angle = mt_rand(-80, 80);                                            // 旋转角度
            $fontBox = $this->driver->getCanvas()->imageFontBox(
                $v['contents'],
                $fontSize,
                $font,
                $angle
            );                                                                    // 计算文字方框坐标
            $x = $space[0][0];                                                    // 起始x坐标
            $y = $space[0][1];                                                    // 起始y坐标
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
            $this->driver->CopyText($v['contents'], $x, $y, $fontSize, [255, 255, 255, 1], null, $font, null, null,
                $angle);
            // 加粗字体
            $ttfCount = 6;
            for ($j = 1; $j <= $ttfCount; $j++) {
                // 随机颜色
                $ttfColor = [mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), 1];
                $this->driver->CopyText($v['contents'], $x + $j, $y - $j, $fontSize - ($j * 2), $ttfColor, null, $font,
                    null, null, $angle);
            }
        }

        // 显示字体大小
        $viewFont = 22;
        // 计算文字长宽
        $fontBox = $this->driver->getCanvas()->imageFontBox(
            $content,
            $viewFont,
            $font
        );
        // 显示字体y坐标
        $viewHeight = 296;

        $text = $this->driver->newText();
        $text->setText($content)->setFont($font)->setFontColor([0, 0, 0, 1])->setFontSize($viewFont);
        $this->driver->getCanvas()->addText($text, 10, $viewHeight);

        $content_height = abs($fontBox[7]) + 1;
        return [
            'content'        => $content,
            'content_width'  => $fontBox[2],
            'content_height' => $content_height,
            'x'              => 10,
            'y'              => $viewHeight - $content_height,
            'contents'       => $contents,
        ];

    }

    protected function getBackgroundImage()
    {
        return POSTER_BASE_PATH . DIRECTORY_SEPARATOR . 'style' .
            DIRECTORY_SEPARATOR . 'rotate_bg' .
            DIRECTORY_SEPARATOR . 'rotate0' . mt_rand(1, 5) . '.jpg';
    }
}