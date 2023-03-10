<?php

namespace Kkokk\Poster\Lang;

use Kkokk\Poster\Abstracts\PosterAbstract;

/**
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 11:49:51
 * @Last Modified by:   lang
 * @Last Modified time: 2021-09-09 10:35:19
 */

/**
 *
 */
class AbstractTest extends PosterAbstract
{

    public function config($params = [])
    {

        $this->setConfig($params);
        return $this;
    }

    /**
     * [buildIm description] 创建画布
     * @Author   lang
     * @DateTime 2020-08-14T20:52:41+0800
     * @param number $w [description] 画布宽
     * @param number $h [description] 画布高
     * @param array $rgba [description] 颜色rbga
     * @param boolean $alpha [description] 是否透明
     */
    public function buildIm($w, $h, $rgba = [], $alpha = false)
    {

        $this->Im($w, $h, $rgba, $alpha);
        return $this;
    }

    /**
     * [buildImDst description] 创建指定图片为画布
     * @Author   lang
     * @DateTime 2020-08-15T11:14:48+0800
     * @param    [src]                    $src   [description] 图像资源
     * @param integer $w [description]
     * @param integer $h [description]
     * @param array $rgba [description]
     * @param boolean $alpha [description]
     * @return   [type]                          [description]
     */
    public function buildImDst($src, $w = 0, $h = 0)
    {
        $this->ImDst($src, $w, $h);
        return $this;
    }

    /**
     * 添加背景
     * @Author lang
     * @Email: 732853989@qq.com
     * Date: 2022/9/17
     * Time: 下午11:00
     * @param $w
     * @param $h
     * @param array $rgba
     * @param false $alpha
     * @return $this
     */
    public function buildBg($w, $h, $rgba = [], $alpha = false, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $func = '')
    {

        $this->Bg($w, $h, $rgba, $alpha, $dst_x, $dst_y, $src_x, $src_y, $func);
        return $this;
    }

    /**
     * [buildImage description] 合成图片
     * @Author   lang
     * @DateTime 2020-08-14T20:56:54+0800
     * @param    [type]                   $src   [description]
     * @param integer $dst_x [description]
     * @param integer $dst_y [description]
     * @param integer $src_x [description]
     * @param integer $src_y [description]
     * @param integer $src_w [description]
     * @param integer $src_h [description]
     * @param string $type [description]
     * @param boolean $alpha [description] 透明
     * @return   [type]                          [description]
     */
    public function buildImage($src, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $src_w = 0, $src_h = 0, $alpha = false, $type = 'normal')
    {

        $this->CopyImage($src, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $alpha, $type);
        return $this;
    }

    /**
     * buildImageArr 批量合成图片
     * @Author lang
     * @Date   2021-09-06T14:37:26+0800
     * @param array $arr 单个合成图片的所有参数组合成数组，
     * @return [type]                        [description]
     */
    public function buildImageMany($arr = [])
    {
        if (PHP_VERSION <= 7) {

            foreach ($arr as $value) {
                $value['dst_x'] = isset($value['dst_x']) ? $value['dst_x'] : 0;
                $value['dst_y'] = isset($value['dst_y']) ? $value['dst_y'] : 0;
                $value['src_x'] = isset($value['src_x']) ? $value['src_x'] : 0;
                $value['src_y'] = isset($value['src_y']) ? $value['src_y'] : 0;
                $value['src_w'] = isset($value['src_w']) ? $value['src_w'] : 0;
                $value['src_h'] = isset($value['src_h']) ? $value['src_h'] : 0;
                $value['alpha'] = isset($value['alpha']) ?: false;
                $value['type'] = isset($value['type']) ? $value['type'] : 'normal';
                $this->CopyImage($value['src'], $value['dst_x'], $value['dst_y'], $value['src_x'], $value['src_y'], $value['src_w'], $value['src_h'], $value['alpha'], $value['type']);
            }

        } else {

            foreach ($arr as $value) {
                $value['dst_x'] = $value['dst_x'] ?? 0;
                $value['dst_y'] = $value['dst_y'] ?? 0;
                $value['src_x'] = $value['src_x'] ?? 0;
                $value['src_y'] = $value['src_y'] ?? 0;
                $value['src_w'] = $value['src_w'] ?? 0;
                $value['src_h'] = $value['src_h'] ?? 0;
                $value['alpha'] = $value['alpha'] ?? false;
                $value['type'] = $value['type'] ?? 'normal';
                $this->CopyImage($value['src'], $value['dst_x'], $value['dst_y'], $value['src_x'], $value['src_y'], $value['src_w'], $value['src_h'], $value['alpha'], $value['type']);
            }
        }

        return $this;
    }

    /**
     * 合成直线、斜线、矩形
     * @Author lang
     * @Email: 732853989@qq.com
     * Date: 2023/2/12
     * Time: 下午9:30
     * @param $x1 int 起点x坐标
     * @param $y1 int 起点y坐标
     * @param $x2 int 终点x坐标
     * @param $y2 int 终点y坐标
     * @param array $rgba 颜色
     * @param int $weight 线粗细
     * @return $this
     */
    public function buildLine($x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0, $rgba = [], $type = '', $weight = 1)
    {
        $this->CopyLine($x1, $y1, $x2, $y2, $rgba, $type, $weight);
        return $this;
    }

    public function buildArc($cx = 0, $cy = 0, $w = 0, $h = 0, $s = 0, $e = 0, $rgba = [], $type = '', $style = '', $weight = 1)
    {
        $this->CopyArc($cx, $cy, $w, $h, $s, $e, $rgba, $type, $style, $weight);
        return $this;
    }

    /**
     * [buildText description] 合成文字
     * @Author   lang
     * @DateTime 2020-08-14T22:09:20+0800
     * @param    [type]                   $content     [description]
     * @param integer $dst_x [description]
     * @param integer $dst_y [description]
     * @param integer $font [description]
     * @param array $rgba [description]
     * @param integer $max_w [description]
     * @param string $font_family [description]
     * @return   [type]                                [description]
     */
    public function buildText($content, $dst_x = 0, $dst_y = 0, $font = 16, $rgba = [], $max_w = 0, $font_family = '', $weight = 1, $space = 0)
    {

        $this->CopyText($content, $dst_x, $dst_y, $font, $rgba, $max_w, $font_family, $weight, $space);
        return $this;
    }

    /**
     * [buildTextArr 批量合成文字
     * @Author lang
     * @Date   2021-09-06T14:35:54+0800
     * @param array $arr 单个合成的所有字段，键值对
     * @return [type]                        [description]
     */
    public function buildTextMany($arr = [])
    {
        if (PHP_VERSION <= 7) {
            foreach ($arr as $value) {
                $value['dst_x'] = isset($value['dst_x']) ? $value['dst_x'] : 0;
                $value['dst_y'] = isset($value['dst_y']) ? $value['dst_y'] : 0;
                $value['font'] = isset($value['font']) ? $value['font'] : 0;
                $value['rgba'] = isset($value['rgba']) ? $value['rgba'] : [];
                $value['max_w'] = isset($value['max_w']) ? $value['max_w'] : 0;
                $value['font_family'] = isset($value['font_family']) ? $value['font_family'] : '';
                $value['weight'] = isset($value['weight']) ? $value['weight'] : 1;
                $value['space'] = isset($value['space']) ? $value['space'] : 0;
                $this->CopyText($value['content'], $value['dst_x'], $value['dst_y'], $value['font'], $value['rgba'], $value['max_w'], $value['font_family'], $value['weight'], $value['space']);
            }
        } else {
            foreach ($arr as $value) {
                $value['dst_x'] = $value['dst_x'] ?? 0;
                $value['dst_y'] = $value['dst_y'] ?? 0;
                $value['font'] = $value['font'] ?? 0;
                $value['rgba'] = $value['rgba'] ?? [];
                $value['max_w'] = $value['max_w'] ?? 0;
                $value['font_family'] = $value['font_family'] ?? '';
                $value['weight'] = $value['weight'] ?? 1;
                $value['space'] = $value['space'] ?? 0;
                $this->CopyText($value['content'], $value['dst_x'], $value['dst_y'], $value['font'], $value['rgba'], $value['max_w'], $value['font_family'], $value['weight'], $value['space']);
            }
        }
        return $this;
    }

    /**
     * [buildQr description] 合成二维码
     * @Author lang
     * @Date   2020-10-14T12:14:06+0800
     * @param  [type]                   $text   [内容]
     * @param integer $dst_x [目标位置x]
     * @param integer $dst_y [目标位置y]
     * @param integer $src_x [自身位置x]
     * @param integer $src_y [自身位置y]
     * @param integer $size [大小]
     * @param integer $margin [百变大小]
     * @return [type]                           [description]
     */
    public function buildQr($text, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $src_w = 0, $src_h = 0, $size = 4, $margin = 1)
    {
        $this->CopyQr($text, $size, $margin, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
        return $this;
    }

    public function buildQrMany($arr = [])
    {
        if (PHP_VERSION <= 7) {
            foreach ($arr as $value) {
                $value['dst_x'] = isset($value['dst_x']) ? $value['dst_x'] : 0;
                $value['dst_y'] = isset($value['dst_y']) ? $value['dst_y'] : 0;
                $value['src_x'] = isset($value['src_x']) ? $value['src_x'] : 0;
                $value['src_y'] = isset($value['src_y']) ? $value['src_y'] : 0;
                $value['src_w'] = isset($value['src_w']) ? $value['src_w'] : 0;
                $value['src_h'] = isset($value['src_h']) ? $value['src_h'] : 0;
                $value['size'] = isset($value['size']) ? $value['size'] : 4;
                $value['margin'] = isset($value['margin']) ? $value['margin'] : 1;
                $this->CopyQr($value['text'], $value['size'], $value['margin'], $value['dst_x'], $value['dst_y'], $value['src_x'], $value['src_y'], $value['src_w'], $value['src_h']);
            }
        } else {
            foreach ($arr as $value) {
                $value['dst_x'] = $value['dst_x'] ?? 0;
                $value['dst_y'] = $value['dst_y'] ?? 0;
                $value['src_x'] = $value['src_x'] ?? 0;
                $value['src_y'] = $value['src_y'] ?? 0;
                $value['src_w'] = $value['src_w'] ?? 0;
                $value['src_h'] = $value['src_h'] ?? 0;
                $value['size'] = $value['size'] ?? 4;
                $value['margin'] = $value['margin'] ?? 1;
                $this->CopyQr($value['text'], $value['size'], $value['margin'], $value['dst_x'], $value['dst_y'], $value['src_x'], $value['src_y'], $value['src_w'], $value['src_h']);
            }
        }

        return $this;
    }

    /**
     * [Qr description]
     * @Author lang
     * @Date   2020-10-14T10:59:28+0800
     * @param  [type]                   $text         [二维码包含的内容，可以是链接、文字、json字符串等等]
     * @param  [type]                   $outfile      [默认为false，不生成文件，只将二维码图片返回输出；否则需要给出存放生成二维码图片的文件名及路径]
     * @param  [type]                   $level        [容错级别，默认为L]
     *      可传递的值分别是L(QR_ECLEVEL_L，7%)、M(QR_ECLEVEL_M，15%)、Q(QR_ECLEVEL_Q，25%)、H(QR_ECLEVEL_H，30%)。
     *      这个参数控制二维码容错率，不同的参数表示二维码可被覆盖的区域百分比，也就是被覆盖的区域还能识别
     * @param  [type]                   $size         [控制生成图片的大小，默认为4]
     * @param  [type]                   $margin       [控制生成二维码的空白区域大小]
     * @param  [type]                   $saveAndPrint [保存二维码图片并显示出来，$outfile必须传递图片路径]
     * @return []                                     [description]
     */
    public function Qr($text, $outfile = false, $level = 'L', $size = 4, $margin = 1, $saveAndPrint = 0)
    {
        return $this->createQr($text, $outfile, $level, $size, $margin, $saveAndPrint);
    }

    /**
     * [path description] 设置路径
     * @Author   lang
     * @DateTime 2022-09-16T15:45:57+0800
     * @return   [type]   [description]
     */
    public function path($path)
    {

        $this->setFilePath($path);
        return $this;

    }

    /**
     * [getPoster description] 获取合成后图片地址
     * @Author   lang
     * @DateTime 2020-08-16T15:45:57+0800
     * @return   [type]   [description]
     */
    public function getPoster($path = '')
    {

        return $this->getData($path);
    }

    /**
     * [getPoster description] 返回文件流
     * @Author   lang
     * @DateTime 2020-08-16T15:45:57+0800
     * @return   [type]   [description]
     */
    public function stream()
    {

        return $this->getStream();
    }

    /**
     * [setPoster description] 处理当前图片
     * @Author   lang
     * @DateTime 2020-08-16T15:46:01+0800
     */
    public function setPoster()
    {

        return $this->setData();
    }

    public function baseData()
    {
        return $this->getBaseData();
    }
}