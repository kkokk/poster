<?php
/**
 * @Author lang
 * @Email: 732853989@qq.com
 * Date: 2022/12/11
 * Time: 下午9:40
 */

namespace Kkokk\Poster\Captcha;

use Kkokk\Poster\Abstracts\MyCaptcha;
use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Facades\Cache;

class Click extends MyCaptcha
{

    protected $configs = [
        'src'         => '',
        'im_width'    => 256,
        'im_height'   => 306,
        'im_type'     => 'png', // png 默认 jpg quality 质量
        'quality'     => 80,    // jpg quality 质量
        'bg_width'    => 256,
        'bg_height'   => 256,
        'type'        => 'text', // text 汉字 number 数字 alpha_num 字母和数字
        'font_family' => __DIR__ . '/../style/zhankukuheiti.ttf', // 感谢站酷提供免费商用站酷库黑体、可自定义炫酷字体文件（绝对路径）
        'contents'    => '',   // 自定义文字
        'font_size'   => 42,  // 字体大小
        'font_count'  => 0,  // 字体大小
        'line_count'  => 0,  // 干扰线数量
        'char_count'  => 0,  // 干扰字符数量
    ];  // 验证码图片配置

    public function config($param = [])
    {
        if (empty($param)) return $this;
        if (PHP_VERSION < 7) {
            $this->configs['src'] = isset($param['src']) ? $param['src'] : $this->configs['src'];
            $this->configs['im_type'] = isset($param['im_type']) ? $param['im_type'] : $this->configs['im_type'];
            $this->configs['quality'] = isset($param['quality']) ? $param['quality'] : $this->configs['quality'];
            $this->configs['contents'] = isset($param['contents']) ? $param['contents'] : $this->configs['contents'];
            $this->configs['font_family'] = isset($param['font_family']) ? $param['font_family'] : $this->configs['font_family'];
            $this->configs['font_size'] = isset($param['font_size']) ? $param['font_size'] : $this->configs['font_size'];
            $this->configs['font_count'] = isset($param['font_count']) ? $param['font_count'] : $this->configs['font_count'];
            $this->configs['line_count'] = isset($param['line_count']) ? $param['line_count'] : $this->configs['line_count'];
            $this->configs['char_count'] = isset($param['char_count']) ? $param['line_count'] : $this->configs['char_count'];
        } else {
            $this->configs['src'] = $param['src'] ?? $this->configs['src'];
            $this->configs['im_type'] = $param['im_type'] ?? $this->configs['im_type'];
            $this->configs['quality'] = $param['quality'] ?? $this->configs['quality'];
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

    // 计算 三个点的叉乘 |p1 p2| X |p1 p|
    public function getCross($p1, $p2, $p)
    {
        // (p2.x - p1.x) * (p.y - p1.y) -(p.x - p1.x) * (p2.y - p1.y);
        return ($p1[0] - $p[0]) * ($p2[1] - $p[1]) - ($p2[0] - $p[0]) * ($p1[1] - $p[1]);
    }

    public function check($key, $value, $leeway = 0)
    {
        if (!is_array($value)) throw new PosterException('array format required');

        $contents = Cache::pull($key);

        if (empty($contents)) return false;

        $points = json_decode($contents, true);

        if (count($points) != count($value)) return false;

        foreach ($points as $k => $v) {
            $point = $v['point'];

            // 任意坐标点
            $p = [$value[$k]['x'], $value[$k]['y']];
            $p1 = [$point[0], $point[1]]; // 左下
            $p2 = [$point[2], $point[3]]; // 右下
            $p3 = [$point[4], $point[5]]; // 右上
            $p4 = [$point[6], $point[7]]; // 左上

            // 叉积计算 点在四条平行线内部则是在矩形内 p1->p2 p1->p3 参考点 p1  叉积大于0点p3在p2逆时针方向 等于0 三点一线 小于0 点p3在p2顺时针防线
            $isCross = $this->getCross($p1, $p2, $p) * $this->getCross($p3, $p4, $p) >= 0 && $this->getCross($p2, $p3, $p) * $this->getCross($p4, $p1, $p) >= 0;
            if ($isCross) {
                continue;
            } else {
                return false;
            }
        }

        return true;
    }

    public function get($expire = 0)
    {

        $data = $this->draw();

        $this->imOutput(
            $this->im,
            __DIR__ . '/../../tests/poster/click.' . $this->configs['im_type'],
            $this->configs['im_type'],
            $this->configs['quality']
        );

        $baseData = $this->baseData($this->im, $this->configs['im_type']);

        $key = uniqid('click' . mt_rand(0, 9999), true);

        Cache::put($key, json_encode($data['contents']), $expire ?: $this->expire);
        
        return [
            'key' => $key,
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

        $this->drawLine($bg_width, $bg_height); // 干扰线

        $this->drawChar($bg_width, $bg_height); // 干扰字符

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
            // 随机获取位置
            $spaceKey = mt_rand(0, count($spaces) - 1);
            $space = array_splice($spaces, $spaceKey, 1);
            $angle = mt_rand(0, 80); // 旋转角度
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
                $ttfColor = $this->PosterBase->createColorAlpha($this->im, [mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), 1]);
                imagettftext($this->im, $font - ($j * 2), $angle, $x + $j, $y - $j, $ttfColor, $font_family, $v['contents']);
            }
        }

        // 显示字体为黑色
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
