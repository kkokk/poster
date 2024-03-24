<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/27
 * Time: 11:10
 */

namespace Kkokk\Poster\Image\Traits;

use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Html\Drivers\DriverInterface;

trait GdTrait
{

    /**
     * 返回图片流或者图片
     * User: lang
     * Date: 2023/8/4
     * Time: 17:53
     * @param $type
     * @param $outfile
     * @return false|string|string[]|void
     * @throws PosterException
     */
    protected function returnImage($type, $outfile = true)
    {
        if (!isset($this->im) || empty($this->im)) throw new PosterException('没有创建任何资源');
        if ($outfile) {
            $this->dirExists($this->pathname);
            if (strripos($this->filename, '.') === false) {
                $this->filename = $this->filename . '.' . $this->type;
            }

            $this->poster_type[$type]($this->im, $this->path . $this->pathname . DIRECTORY_SEPARATOR . $this->filename);

            return ['url' => $this->pathname . DIRECTORY_SEPARATOR . $this->filename];
        }
        if(PHP_SAPI === 'cli') {
            return $this->getBlob($type, $this->im);
        }

        header('Content-Type:Image/' . $this->type);
        $this->poster_type[$type]($this->im);
    }

    protected function getBlob($type, $im)
    {
        ob_start();
        $this->poster_type[$type]($im);
        return ob_get_clean();
    }

    protected function getTmp($type, $im){
        $output = tempnam(sys_get_temp_dir(), uniqid('gdImage'));
        $this->poster_type[$type]($im, $output);
        return $output;
    }

    protected function setImage($source)
    {

        if (strpos($source, 'http') === 0) {
            throw new PosterException("unable to set the remote source {$source}");
        }

        if (!empty($source)) {
            return $this->poster_type[$this->type]($this->im, $source);
        }

        throw new PosterException("source not found {$source}");
    }

    public function createImage($src = '')
    {
        if($src instanceof DriverInterface) {
            return $this->returnImageInfoByBlob($src->getImageBlob());
        } elseif(strpos($src, 'http') === 0 || file_exists($src)) {
            return $this->returnImageInfoBySrc($src);
        } else {
            return $this->returnImageInfoByBlob($src);
        }
    }

    public function returnImageInfoByBlob($blob)
    {
        list($width, $height) = @getimagesizefromstring($blob);
        $pic = imagecreatefromstring($blob);
        return [$pic, $width, $height];
    }

    public function returnImageInfoBySrc($src)
    {
        list($width, $height, $bgType) = @getimagesize($src);

        if (empty($bgType)) throw new PosterException('image resources cannot be empty (' . $src . ')');

        $bgType = image_type_to_extension($bgType, false);

        $getGdVersion = preg_match('~\((.*) ~', gd_info()['GD Version'], $matches);
        if ($getGdVersion && (float)$matches[1] < 2 && $bgType == 'gif') {
            $pic = imagecreatefromstring(file_get_contents($src));
        } else {
            $fun = 'imagecreatefrom' . $bgType;
            $pic = @$fun($src);
        }

        return [$pic, $width, $height];
    }

    /**
     * 创建画布
     */
    public function createIm($w, $h, $rgba, $alpha = false)
    {
        $cut = imagecreatetruecolor($w, $h);

        $color = $alpha ? $this->createColorAlpha($cut, $rgba) : $this->createColor($cut, $rgba);
        if ($alpha) {
            // imagecolortransparent($cut, $color);
            imagesavealpha($cut, true);
        }
        imagefill($cut, 0, 0, $color);

        return $cut;
    }

    /**
     * 获取颜色值，可设置透明度
     */
    public function createColorAlpha($cut, $rgba = [255, 255, 255, 127])
    {

        if (empty($rgba)) $rgba = [255, 255, 255, 127];
        if (count($rgba) != 4) throw new PosterException('The length of the rgba parameter is 4');
        foreach ($rgba as $k => $value) {
            if (!is_int($rgba[$k])) {
                throw new PosterException('The value must be an integer');
            } elseif ($k < 3 && ($rgba[$k] > 255 || $rgba[$k] < 0)) {
                throw new PosterException('The color value is between 0-255');
            } elseif ($k == 3 && ($rgba[$k] > 127 || $rgba[$k] < 0)) {
                throw new PosterException('The alpha value is between 0-127');
            }
        }

        return imagecolorallocatealpha($cut, $rgba[0], $rgba[1], $rgba[2], $rgba[3]);
    }

    /**
     * 获取颜色值，没有透明度
     */
    public function createColor($cut, $rgba = [255, 255, 255, 1])
    {

        if (empty($rgba)) $rgba = [255, 255, 255, 1];
        if (count($rgba) < 4) throw new PosterException('The length of the rgba parameter is 4');
        foreach ($rgba as $k => $value) {
            if (!is_int($rgba[$k])) {
                throw new PosterException('The text value must be an integer');
            } elseif ($k < 3 && ($rgba[$k] > 255 || $rgba[$k] < 0)) {
                throw new PosterException('The text color value is between 0-255');
            }
        }

        return imagecolorallocate($cut, $rgba[0], $rgba[1], $rgba[2]);
    }

    /**
     * 计算渐变颜色值
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/10/18
     * Time: 16:08
     * @param $h
     * @param $i
     * @param $c1
     * @param $c2
     * @return array
     */
    protected function calcColor($h, $i, $c1, $c2)
    {
        $res = [];
        $r = abs($c2[0] - $c1[0]);
        $rr = $c2[0] - $c1[0];
        $b = abs($c2[1] - $c1[1]);
        $bb = $c2[1] - $c1[1];
        $g = abs($c2[2] - $c1[2]);
        $gg = $c2[2] - $c1[2];

        if ($r == 0) {
            $res[] = $c2[0];
        } else {
            $res[] = $rr > 0 ? ($c1[0] + $r / $h * $i) : ($c1[0] - $r / $h * $i);
        }

        if ($b == 0) {
            $res[] = $c2[1];
        } else {
            $res[] = $bb > 0 ? ($c1[1] + $b / $h * $i) : ($c1[1] - $b / $h * $i);
        }

        if ($g == 0) {
            $res[] = $c2[2];
        } else {
            $res[] = $gg > 0 ? ($c1[2] + $g / $h * $i) : ($c1[2] - $g / $h * $i);
        }

        return $res;
    }

    /**
     * 计算颜色渐变区块->等比分配
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/10/18
     * Time: 10:56
     * @param $rgba
     * @param $h
     * @param $i
     * @return array
     */
    protected function calcColorArea($rgbaColor, $rgbaCount, $h, $i)
    {
        // 单色
        if ($rgbaCount == 1) {
            $colorRgb = $this->calcColor($h, $i, $rgbaColor[0], $rgbaColor[0]);
        } elseif ($rgbaCount == 2) {
            // 两种颜色
            $colorRgb = $this->calcColor($h, $i, $rgbaColor[0], $rgbaColor[1]);
        } else {
            // 多种颜色 计算距离并分配颜色
            $rgbaCount = $rgbaCount - 1;
            $d = ceil($h / $rgbaCount);
            $index1 = 0;
            $index2 = 1;
            $di = 0;
            for ($j = $rgbaCount; $j > 0; $j--) {

                if ($i >= ceil((($j - 1) * $d)) && $i <= ceil($j * $d)) {
                    $index1 = $j - 1;
                    $index2 = $j;
                    $di = $i - (($j - 1) * $d);
                    break;
                }
            }
            $colorRgb = $this->calcColor($d, $di, $rgbaColor[$index1], $rgbaColor[$index2]);
        }

        return $colorRgb;
    }

    /**
     * 计算颜色渐变方向
     * @Author lang
     * @Email: 732853989@qq.com
     * Date: 2022/10/20
     * Time: 上午12:17
     * @param $im
     * @param $rgbaColor
     * @param $rgbaCount
     * @param $to
     * @param $w
     * @param $h
     * @return mixed
     */
    public function calcColorDirection($im, $rgbaColor, $rgbaCount, $to, $w, $h)
    {
        $to = preg_replace('~\s+~', ' ', trim($to, ' '));

        switch ($to) {
            case '':
            case 'bottom':
                $toi = $h;
                $toj = $w;
                $im = $this->linearGradient($im, $toi, $toj, $rgbaColor, $rgbaCount);
                break;
            case 'top':
                $toi = $h;
                $toj = $w;
                $rgbaColor = array_reverse($rgbaColor);
                $im = $this->linearGradient($im, $toi, $toj, $rgbaColor, $rgbaCount);
                break;
            case 'left':
                $toi = $w;
                $toj = $h;
                $rgbaColor = array_reverse($rgbaColor);
                $im = $this->linearGradient($im, $toi, $toj, $rgbaColor, $rgbaCount, 'j', 'i');
                break;
            case 'right':
                $toi = $w;
                $toj = $h;
                $im = $this->linearGradient($im, $toi, $toj, $rgbaColor, $rgbaCount, 'j', 'i');
                break;
            case 'right bottom':
            case 'bottom right':
                $toi = $w;
                $toj = $h;
                $rgbaColor = array_reverse($rgbaColor);
                $im = $this->linearGradientLeftTopRightBottomDiagonal($im, $toi, $toj, $rgbaColor, $rgbaCount);
                break;
            case 'right top':
            case 'top right':
                $toi = $w;
                $toj = $h;
                $rgbaColor = array_reverse($rgbaColor);
                $im = $this->linearGradientLeftTopRightBottomDiagonal($im, $toi, $toj, $rgbaColor, $rgbaCount, 0, $toj);
                break;
            case 'left bottom':
            case 'bottom left':
                $toi = $w;
                $toj = $h;
                $im = $this->linearGradientLeftTopRightBottomDiagonal($im, $toi, $toj, $rgbaColor, $rgbaCount, 0, $toj);
                break;
            case 'left top':
            case 'top left':
                $toi = $w;
                $toj = $h;
                $im = $this->linearGradientLeftTopRightBottomDiagonal($im, $toi, $toj, $rgbaColor, $rgbaCount);
                break;
            default:
                // code...
                break;
        }

        return $im;
    }

    /**
     * 获取渐变颜色值
     * @Author lang
     * @Email: 732853989@qq.com
     * Date: 2022/10/20
     * Time: 上午12:15
     * @param $im
     * @param $rgbaColor
     * @param $rgbaCount
     * @param $length
     * @param $i
     * @return false|int
     */
    public function getColor($im, $rgbaColor, $rgbaCount, $length, $i)
    {
        $colorRgb = $this->calcColorArea($rgbaColor, $rgbaCount, $length, $i);
        $color = imagecolorallocate($im, $colorRgb[0], $colorRgb[1], $colorRgb[2]);
        return $color;
    }

    /**
     * 获取透明颜色值
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/10/20
     * Time: 9:37
     * @param $im
     * @param $colorRgb
     * @param $alphas
     * @return false|int
     */
    public function getAlphasColor($im, $colorRgb, $alphas)
    {
        $color = imagecolorallocatealpha($im, $colorRgb[0], $colorRgb[1], $colorRgb[2], $alphas);
        return $color;
    }

    /**
     * 渐变处理方法
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/10/20
     * Time: 10:04
     * @param $im resource 画布资源
     * @param $toi double 宽或高
     * @param $toj double 高或宽
     * @param $rgbaColor array 渐变色值
     * @param $rgbaCount int 渐变色数量
     * @param int $radius int 圆角
     * @param string $ii string x,y 变量取值替换
     * @param string $jj string x,y 变量取值替换
     * @return mixed|resource
     */
    protected function linearGradient($im, $toi, $toj, $rgbaColor, $rgbaCount, $ii = 'i', $jj = 'j')
    {

        for ($i = $toi; $i >= 0; $i--) {
            // 获取颜色
            $color = $this->getColor($im, $rgbaColor, $rgbaCount, $toi, $i);
            // imagefilledrectangle($this->im, 0, $i, $w, 0, $color); // 填充颜色
            // $color = ($colorRgb[0] << 16) + ($colorRgb[1] << 8) + $colorRgb[2];  // 获取颜色参数
            for ($j = 0; $j < $toj; $j++) {
                imagesetpixel($im, $$jj, $$ii, $color);
            }

        }

        return $im;
    }

    /**
     * 画圆角
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/10/20
     * Time: 9:55
     * @param $im resource 画布资源
     * @param $w double 宽
     * @param $h double 高
     * @param $radius int 圆角
     * @return mixed|resource
     */
    protected function setPixelRadius($im, $w, $h, $radius)
    {
        $newIm = $this->createIm($w, $h, [], true);;

        $len = $w > $h ? $h / 2 : $w / 2;

        list($leftTopRadius, $rightTopRadius, $leftBottomRadius, $rightBottomRadius) = $this->getRadiusType($radius, $len);

        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $color = imagecolorat($im, $x, $y);
                if (($x >= $leftTopRadius || $y >= $leftTopRadius)
                    && (($x <= ($w - $rightTopRadius) || $y >= $rightTopRadius))
                    && (($x >= $leftBottomRadius || $y <= ($h - $leftBottomRadius)))
                    && (($x <= ($w - $rightBottomRadius)) || $y <= ($h - $rightBottomRadius))) {
                    //不在四角的范围内,直接画
                    imagesetpixel($newIm, $x, $y, $color);
                } else {
                    // 上左
                    $y_x = $leftTopRadius;
                    $y_y = $leftTopRadius;
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($leftTopRadius * $leftTopRadius))) {
                        imagesetpixel($newIm, $x, $y, $color);
                    }

                    // 上右
                    $y_x = $w - $rightTopRadius;
                    $y_y = $rightTopRadius;
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($rightTopRadius * $rightTopRadius))) {
                        imagesetpixel($newIm, $x, $y, $color);
                    }

                    //下左
                    $y_x = $leftBottomRadius;
                    $y_y = $h - $leftBottomRadius;
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($leftBottomRadius * $leftBottomRadius))) {
                        imagesetpixel($newIm, $x, $y, $color);
                    }

                    //下右
                    $y_x = $w - $rightBottomRadius;
                    $y_y = $h - $rightBottomRadius;
                    if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($rightBottomRadius * $rightBottomRadius))) {
                        imagesetpixel($newIm, $x, $y, $color);
                    }
                }
            }
        }

        return $newIm;
    }

    /**
     * 渐变处理方法 对角 分两段循环
     * @Author lang
     * @Email: 732853989@qq.com
     * Date: 2022/10/20
     * Time: 上午12:13
     * @param $im
     * @param $toi
     * @param $toj
     * @param $rgbaColor
     * @param $rgbaCount
     * @param int $x
     * @param int $y
     * @return mixed
     */
    protected function linearGradientLeftTopRightBottom($im, $toi, $toj, $rgbaColor, $rgbaCount, $x = 0, $y = 0)
    {

        $toLen = $toi >= $toj ? $toi : $toj;

        $len = $toi + $toj;

        $ii = $len - 1;

        if ($x == 0 && $y == 0) {
            // 从 0,0 开始
            for ($i = 0; $i < $toLen + 1; $i++) {
                //设$i为y轴坐标
                $f = 0;
                $color = $this->getColor($im, $rgbaColor, $rgbaCount, $len, $ii--);
                for ($j = 0; $j <= $i; $j++) {
                    if ($j <= $toi && ($i - $j) <= $toj) {
                        if (!$f) {
                            $x = $j;
                            $y = $i - $j;
                            $f = 1;
                        }
                        imagesetpixel($im, $j, $i - $j, $color);
                    }
                }
            }
            //加入右半段
            for ($i = $x + 1; $i <= $toi; $i++) {
                $color = $this->getColor($im, $rgbaColor, $rgbaCount, $len, $ii--);
                for ($j = 0; $j <= $y; $j++) {
                    if (($i + $j) <= $toi && ($y - $j) <= $toj) {
                        imagesetpixel($im, $i + $j, $y - $j, $color);
                    }
                }
            }
        } else {
            // 从 0,y 开始
            for ($i = 0; $i < $toLen + 1; $i++) {
                //设$i为y轴坐标
                $f = false;
                $color = $this->getColor($im, $rgbaColor, $rgbaCount, $len, $ii--);
                for ($j = 0; $j <= $i; $j++) {
                    if ($j <= $toi && ($i - $j) <= $toj) {
                        if (!$f) {
                            $x = $j;
                            $y = $i - $j;
                            $f = true;
                        }
                        imagesetpixel($im, $j, $toj - ($i - $j), $color);
                    }
                }
            }

            //加入后半段
            for ($i = $x + 1; $i <= $toi; $i++) {
                $color = $this->getColor($im, $rgbaColor, $rgbaCount, $len, $ii--);
                for ($j = 0; $j <= $y; $j++) {
                    if (($i + $j) <= $toi && ($y - $j) <= $toj) {
                        imagesetpixel($im, $i + $j, $j, $color);
                    }
                }
            }
        }

        return $im;
    }

    /**
     * 根据对角线长度循环
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/11/23
     * Time: 11:35
     * @param $im
     * @param $toi
     * @param $toj
     * @param $rgbaColor
     * @param $rgbaCount
     * @param int $x
     * @param int $y
     * @return mixed
     */
    protected function linearGradientLeftTopRightBottomDiagonal($im, $toi, $toj, $rgbaColor, $rgbaCount, $x = 0, $y = 0)
    {
        $total = $toi + $toj + 1;    // 对角线最大循环次数
        $isRectangle = $toi != $toj; // 判断是否是长方形
        // 获取中间位置数值
        $centerNum = !$isRectangle ? $this->centerNumSquare($total) : $this->centerNumRectangle($toi, $toj);

        $ii = $total; // 颜色计算递减数值

        $toiTag = 'ii'; // 默认宽大于长
        $tojTag = 'jj'; // 默认宽大于长
        if ($toj > $toi) {  // 长大于宽
            $toiTag = 'jj';
            $tojTag = 'ii';
        }

        if ($isRectangle) { // 长方形
            for ($i = 0; $i < $total; $i++) {
                $color = $this->getColor($im, $rgbaColor, $rgbaCount, $total, $ii--);
                $im = $this->getPointRectangle($im, $i, $centerNum, $total, $color, $toiTag, $tojTag, $x, $y);
            }
        } else {
            // 正方形
            for ($i = 0; $i < $total; $i++) {
                $color = $this->getColor($im, $rgbaColor, $rgbaCount, $total, $ii--);
                $im = $this->getPointSquare($im, $i, $centerNum, $color, $x, $y);
            }
        }

        return $im;
    }

    /**
     * 正方形获取中间位置数值
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/11/23
     * Time: 10:38
     * @param $num
     * @return float|int
     */
    protected function centerNumSquare($num)
    {
        return $num / 2;
    }

    /**
     * 长方形获取中间位置数值
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/11/23
     * Time: 10:39
     * @param $x
     * @param $y
     * @return int
     */
    protected function centerNumRectangle($x, $y)
    {
        return $x > $y ? $y + 1 : $x + 1;
    }

    /**
     * 长方形通过对角线循环绘画
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/12/12
     * Time: 9:51
     * @param $im
     * @param $num
     * @param $centerNum
     * @param $total
     * @param $color
     * @param $toiTag
     * @param $tojTag
     * @param int $x
     * @param int $y
     * @return mixed
     */
    protected function getPointRectangle($im, $num, $centerNum, $total, $color, $toiTag, $tojTag, $x = 0, $y = 0)
    {

        $len = $total - $centerNum * 2; // 求取对角线上相交线坐标到边的最大宽度数量

        $min = $centerNum;  // 从第几次循环开始保持最大宽度
        $max = $min + $len; // 到第几结束

        if ($num >= $min && $num <= $max) {
            $ii = $num - $centerNum + 1;
            for ($jj = $centerNum - 1; $jj >= 0; $jj--) {
                imagesetpixel($im, ceil($$toiTag), abs($y - floor($$tojTag)), $color);
                $ii++;
            }

        } elseif ($num > $max) {

            $num = $num - $centerNum;
            $ii = $num + 1;
            $jj = $max - $len - 1;
            for ($i = $max; $i > $num; $i--) {
                imagesetpixel($im, ceil($$toiTag), abs($y - floor($$tojTag)), $color);
                $ii++;
                $jj--;
            }

        } else {

            for ($i = 0; $i <= $num; $i++) {
                imagesetpixel($im, $i, abs($y - ($num - $i)), $color);
            }

        }

        return $im;
    }

    /**
     * 正方形通过对角线循环绘画
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/12/12
     * Time: 9:52
     * @param $im
     * @param $num
     * @param $centerNum
     * @param $color
     * @param int $x
     * @param int $y
     * @return mixed
     */
    protected function getPointSquare($im, $num, $centerNum, $color, $x = 0, $y = 0)
    {
        if ($num > $centerNum) {
            $num = $num - $centerNum;
            $ii = $num;
            for ($i = $centerNum; $i >= $num; $i--) {
                // $arr[] = [ceil($ii) , floor($i)];
                imagesetpixel($im, ceil($ii), abs($y - floor($i)), $color);
                $ii++;
            }
        } else {

            for ($i = 0; $i <= $num; $i++) {
                // $arr[] = [$i , $num-$i];
                imagesetpixel($im, $i, abs($y - ($num - $i)), $color);
            }
        }
        return $im;
    }

    /**
     * 字体加粗
     */
    protected function fontWeight($weight, $fontSize, $angle, $dst_x, $dst_y, $color, $font, $contents)
    {
        for ($i = 0; $i < $weight; $i++) {
            list($really_dst_x, $really_dst_y) = $this->calcWeight($i, $weight, $fontSize, $dst_x, $dst_y);
            imagettftext($this->im, $fontSize, $angle, intval($really_dst_x), intval($really_dst_y), $color, $font, $contents);
        }
    }

    /**
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/6/2
     * Time: 14:26
     */
    protected function fontWeightArr($weight, $fontSize, $angle, $dst_x, $dst_y, $color, $font, $contentsArr)
    {

        $dst_x_old = $dst_x;
        // $max = max(array_column($contentsArr, 'w')); // 取最大宽度

        foreach ($contentsArr as $v) {

            $contents = $v['value'];

            if ($contents == "\n") {
                $dst_x = $dst_x_old;
                $dst_y += 1.75 * $fontSize;
                continue;
            }

            $customColor = !empty($v['color']) ? $v['color'] : $color;

            $this->fontWeight($weight, $fontSize, $angle, $dst_x, $dst_y, $customColor, $font, $contents);

            // $dst_x += $max;
            $dst_x += $v['w'];
        }
    }

    /** 设置背景透明 */
    public function setImageAlpha($pic, $w, $h, $alphas)
    {

        $mask = $this->createIm($w, $h, [], $alphas > 1);

        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgbColor = imagecolorat($pic, $x, $y);
                $thisColor = imagecolorsforindex($pic, $rgbColor);
                $color = $this->createColorAlpha($pic, [$thisColor['red'], $thisColor['green'], $thisColor['blue'], $alphas]);
                imagesetpixel($mask, $x, $y, $color);
            }
        }

        return $mask;
    }
}