<?php

namespace Kkokk\Poster\Base;
require_once(__DIR__ . '/../PHPQrcode/phpqrcode.php');

/**
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 11:21:08
 * @Last Modified by:   lang
 * @Last Modified time: 2022-03-10 17:58:41
 */

use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Common\Common;

/**
 *
 */
class PosterBase
{

    protected $im;
    protected $im_w;
    protected $im_h;
    protected $pathname = 'poster';
    protected $filename;
    protected $type = '';
    protected $path;
    protected $source;
    protected $font_family = __DIR__ . '/../style/simkai.ttf';
    protected $poster_type = [
        'gif' => 'imagegif',
        'jpeg' => 'imagejpeg',
        'jpg' => 'imagejpeg',
        'png' => 'imagepng',
        'wbmp' => 'imagewbmp'
    ];


    public function __construct($params = [])
    {
        $params = is_array($params) ? $params : [$params];

        if (PHP_VERSION <= 7) {
            $pathFileName = isset($params[0]) ? $params[0] : '';
        } else {
            $pathFileName = $params[0] ?? '';
        }
        $pathFileName = str_replace(['\\', '/'], '/', $pathFileName);

        $fileName = $pathFileName ?: time();

        if (strripos($pathFileName, '/') !== false) {
            $this->setPathName($pathFileName);
            $fileName = substr($pathFileName, strripos($pathFileName, '/') + 1);
        }

        $this->setFileName($fileName);
        $this->setPath($pathFileName);
    }

    public function setFilePath($path)
    {

        $path = is_array($path) ? $path : [$path];
        if (PHP_VERSION <= 7) {
            $pathFileName = isset($path[0]) ? $path[0] : '';
        } else {
            $pathFileName = $path[0] ?? '';
        }
        $pathFileName = str_replace(['\\', '/'], '/', $pathFileName);

        $fileName = $pathFileName ?: time();

        if (strripos($pathFileName, '/') !== false) {
            $this->setPathName($pathFileName);
            $fileName = substr($pathFileName, strripos($pathFileName, '/') + 1);
        }

        $this->setFileName($fileName);
        $this->setPath($pathFileName);
    }

    /**
     * setFileName 设置文件名
     * @Author lang
     * @Date   2022-03-10T15:42:06+0800
     * @param  [type]                   $fileName [description]
     */
    private function setFileName($fileName)
    {
        $this->filename = $fileName;
        if (strripos($this->filename, '.') !== false) {
            $this->type = substr($this->filename, strripos($this->filename, '.') + 1);
            if (!in_array($this->type, ['jpeg', 'jpg', 'png', 'gif', 'wbmp'])) {
                throw new PosterException('The file naming format is incorrect');
            }
        }
    }

    /**
     * setPathName 设置目录名
     * @Author lang
     * @Date   2022-03-10T15:42:19+0800
     * @param  [type]                   $fileName [description]
     */
    private function setPathName($pathFileName)
    {
        $this->pathname = substr($pathFileName, 0, strripos($pathFileName, '/'));
    }

    /**
     * setPath 设置文件位置
     * @Author lang
     * @Date   2022-03-10T15:42:38+0800
     * @param  [type]                   $fileName [description]
     */
    private function setPath($pathFileName)
    {
        // 绝对路径 or 相对路径
        $absolute = $this->isAbsolute($pathFileName);
        $this->path = $this->getDocumentRoot();
        $this->path = $absolute ? '' : ($this->path ? $this->path . '/' : __DIR__ . '/../../tests/');
    }

    /**
     * 获取项目根目录
     * @Author lang
     * @Date   2022-03-10T15:42:38+0800
     */
    private function getDocumentRoot(){
        return iconv('UTF-8', 'GBK', $_SERVER['DOCUMENT_ROOT']);
    }

    private function isAbsolute($pathFileName)
    {

        // 区分WIN系统绝对路径、暂时只区分linux win mac
        switch (PHP_OS) {
            case 'Darwin':
                $absolute = stripos($pathFileName, '/') === 0 ?: false;
                break;
            case 'linux':
            default:
                if (stripos(PHP_OS, 'WIN') !== false) {
                    $absolute = substr($pathFileName, 1, 1) === ':' ?: false;
                } else {
                    $absolute = stripos($pathFileName, '/') === 0 ?: false;
                }
                break;
        }

        return $absolute;
    }

    /**
     * @Author lang
     * @Date   2020-08-14T14:06:27+0800
     * @return [type]
     */
    protected function getData()
    {
        if (empty($this->type)) $this->type = 'png';
        return $this->returnImage($this->type);
    }

    /**
     * @Author lang
     * @Date   2020-08-14T14:06:27+0800
     * @return [type]
     */
    protected function getStream()
    {
        if (empty($this->type)) $this->type = 'png';
        return $this->returnImage($this->type, false);
    }

    protected function getBaseData(){
        $common = new Common();
        if (empty($this->type)) $this->type = 'png';
        return $common->baseData($this->im, $this->type);
    }


    /**
     * [setData description]
     * @Author   lang
     * @DateTime 2020-08-16T12:34:34+0800
     */
    protected function setData()
    {

        return $this->setImage($this->type);
    }

    /**
     * 返回图片流或者图片
     * @Author lang
     * @Date   2020-08-14T14:29:57+0800
     * @return [type]
     */
    protected function returnImage($type, $outfile = true)
    {

        if (!isset($this->im) || empty($this->im)) throw new PosterException('没有创建任何资源');

        if ($outfile) {
            $this->dirExists($this->pathname);
            if (strripos($this->filename, '.') === false) {
                $this->filename = $this->filename . '.' . $this->type;
            }
            $this->poster_type[$type]($this->im, $this->path . $this->pathname . '/' . $this->filename);

            return ['url' => $this->pathname . '/' . $this->filename];
        }
        header('Content-Type:Image/' . $this->type);
        $this->poster_type[$type]($this->im);

    }

    /**
     * [setImage description]
     * @Author   lang
     * @DateTime 2020-08-16T12:35:17+0800
     * @param    [type]                   $type [description]
     */
    protected function setImage($type)
    {
        if (isset($this->source) && !empty($this->source)) {

            return $this->poster_type[$type]($this->im, $this->source);
        }

        throw new PosterException('没有找到源文件');
    }

    /**
     * @Author lang
     * @Date   2020-08-14T15:32:04+0800
     * @param  [type]
     * @return [type]
     */
    protected function dirExists($pathname)
    {

        if (!file_exists($this->path . $pathname)) {
            return mkdir($this->path . $pathname, 0777, true);
        }

    }

    /**
     * 创建指定宽高，颜色，透明的画布
     */
    protected function Im($w, $h, $rgba, $alpha)
    {
        $this->im_w = $w;
        $this->im_h = $h;
        $this->im = $this->createIm($w, $h, $rgba, $alpha);
    }

    /**
     * 创建指定图片为画布 宽高，颜色，透明的画布
     */
    protected function ImDst($source, $w, $h)
    {


        // if (!is_file($source)) {
        //     throw new PosterException('水印图像不存在');
        // }
        $this->source = $source;
        //获取水印图像信息
        $info = @getimagesize($source);
        list($bgWidth, $bgHeight, $bgType) = @getimagesize($source);

        if (false === $info || (IMAGETYPE_GIF === $info[2] && empty($info['bits']))) {
            throw new PosterException('非法水印文件');
        }

        $this->type = image_type_to_extension($bgType, false);
        if (empty($this->type)) throw new PosterException('image resources cannot be empty (' . $source . ')');

        //创建水印图像资源
        $fun = 'imagecreatefrom' . $this->type;
        $cut = @$fun($source);

        //设定水印图像的混色模式
        imagealphablending($cut, true);

        if (!empty($w) && !empty($h)) {
            $this->im_w = $w;
            $this->im_h = $h;
            $circle_new = $this->createIm($w, $h, [255, 255, 255, 127], $alpha = true);
            imagecopyresized($circle_new, $cut, 0, 0, 0, 0, $w, $h, $bgWidth, $bgHeight);
            $cut = $circle_new;
            // $this->destroyImage($circle_new);
        } else {
            $this->im_w = $bgWidth;
            $this->im_h = $bgHeight;
        }


        $this->im = $cut;
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
            for ($j = $rgbaCount + 1; $j > 0; $j--) {

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
     * @param $alphas
     * @param $to
     * @param $w
     * @param $h
     * @return mixed
     */
    public function calcColorDirection($im, $rgbaColor, $rgbaCount, $alphas, $to, $w, $h)
    {

        $to = preg_replace('~\s+~', ' ', trim($to, ' '));

        switch ($to) {
            case '':
            case 'bottom':
                $toi = $h;
                $toj = $w;
                $im = $this->linearGradient($im, $toi, $toj, $rgbaColor, $rgbaCount, $alphas);
                break;
            case 'top':
                $toi = $h;
                $toj = $w;
                $rgbaColor = array_reverse($rgbaColor);
                $im = $this->linearGradient($im, $toi, $toj, $rgbaColor, $rgbaCount, $alphas);
                break;
            case 'left':
                $toi = $w;
                $toj = $h;
                $rgbaColor = array_reverse($rgbaColor);
                $im = $this->linearGradient($im, $toi, $toj, $rgbaColor, $rgbaCount, $alphas, 'j', 'i');
                break;
            case 'right':
                $toi = $w;
                $toj = $h;
                $im = $this->linearGradient($im, $toi, $toj, $rgbaColor, $rgbaCount, $alphas, 'j', 'i');
                break;
            case 'right bottom':
            case 'bottom right':
                $toi = $w;
                $toj = $h;
                $rgbaColor = array_reverse($rgbaColor);
                $im = $this->linearGradientLeftTopRightBottomDiagonal($im, $toi, $toj, $rgbaColor, $rgbaCount, $alphas);
                break;
            case 'right top':
            case 'top right':
                $toi = $w;
                $toj = $h;
                $rgbaColor = array_reverse($rgbaColor);
                $im = $this->linearGradientLeftTopRightBottomDiagonal($im, $toi, $toj, $rgbaColor, $rgbaCount, $alphas, 0, $toj);
                break;
            case 'left bottom':
            case 'bottom left':
                $toi = $w;
                $toj = $h;
                $im = $this->linearGradientLeftTopRightBottomDiagonal($im, $toi, $toj, $rgbaColor, $rgbaCount, $alphas, 0, $toj);
                break;
            case 'left top':
            case 'top left':
                $toi = $w;
                $toj = $h;
                $im = $this->linearGradientLeftTopRightBottomDiagonal($im, $toi, $toj, $rgbaColor, $rgbaCount, $alphas);
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
     * @param $alphas
     * @param $rgbaColor
     * @param $rgbaCount
     * @param $length
     * @param $i
     * @return false|int
     */
    public function getColor($im, $alphas, $rgbaColor, $rgbaCount, $length, $i)
    {
        $colorRgb = $this->calcColorArea($rgbaColor, $rgbaCount, $length, $i);
        $color = imagecolorallocatealpha($im, $colorRgb[0], $colorRgb[1], $colorRgb[2], $alphas);
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
     * @param $alphas int 透明度 1 - 127
     * @param int $radius int 圆角
     * @param string $ii string x,y 变量取值替换
     * @param string $jj string x,y 变量取值替换
     * @return mixed|resource
     */
    protected function linearGradient($im, $toi, $toj, $rgbaColor, $rgbaCount, $alphas, $ii = 'i', $jj = 'j')
    {

        for ($i = $toi; $i >= 0; $i--) {
            // 获取颜色
            $color = $this->getColor($im, $alphas, $rgbaColor, $rgbaCount, $toi, $i);
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
                }else{
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
     * 根据传值类型获取四个角的半径
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/11/12
     * Time: 15:39
     * @param $radius string|array|integer '20 10' [20, 10] 10
     * @param $len
     * @return false[]|float[]
     */
    protected function getRadiusType($radius, $len){
        if(is_string($radius)){
            // 把字符串格式转数组
            $radius = preg_replace('~\s+~', ' ', trim($radius, ' '));
            $radius = explode(' ', $radius);
        } elseif (is_numeric($radius)) {
            // 整形转数组
            $radius = [$radius, $radius, $radius, $radius];
        }else{
            if(!is_array($radius)) throw new PosterException('圆角参数类型错误');
        }
        // [20] 四个角
        // [20,30] 第一个值 左上 右下 第二个值 右上 左下
        // [20,30,20] 第一个值 左上 第二个值 右上 左下 第三个值 右下
        // [20,30,20,10]  左上 右上 右下  左下
        $radiusCount = count($radius);
        if($radiusCount==1){
            $leftTopRadius = $this->getMaxRadius($len, $radius[0]);
            $rightTopRadius = $this->getMaxRadius($len, $radius[0]);
            $leftBottomRadius = $this->getMaxRadius($len, $radius[0]);
            $rightBottomRadius = $this->getMaxRadius($len, $radius[0]);
        }elseif($radiusCount==2){
            $leftTopRadius = $this->getMaxRadius($len, $radius[0]);
            $rightBottomRadius = $this->getMaxRadius($len, $radius[0]);
            $rightTopRadius = $this->getMaxRadius($len, $radius[1]);
            $leftBottomRadius = $this->getMaxRadius($len, $radius[1]);
        }elseif($radiusCount==3){
            $leftTopRadius = $this->getMaxRadius($len, $radius[0]);
            $rightTopRadius = $this->getMaxRadius($len, $radius[1]);
            $leftBottomRadius = $this->getMaxRadius($len, $radius[1]);
            $rightBottomRadius = $this->getMaxRadius($len, $radius[2]);
        }else{
            $leftTopRadius = $this->getMaxRadius($len, $radius[0]);
            $rightTopRadius = $this->getMaxRadius($len, $radius[1]);
            $leftBottomRadius = $this->getMaxRadius($len, $radius[2]);
            $rightBottomRadius = $this->getMaxRadius($len, $radius[3]);
        }

        return [$leftTopRadius, $rightTopRadius, $leftBottomRadius, $rightBottomRadius];
    }

    /**
     * 获取最大圆角半径
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/11/12
     * Time: 15:14
     * @param $len
     * @param $radius
     * @return false|float
     */
    protected function getMaxRadius($len, $radius){
        return $radius < $len ? floor($radius) : floor($len);
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
     * @param $alphas
     * @param int $x
     * @param int $y
     * @return mixed
     */
    protected function linearGradientLeftTopRightBottom($im, $toi, $toj, $rgbaColor, $rgbaCount, $alphas, $x = 0, $y = 0)
    {

        $toLen = $toi >= $toj ? $toi : $toj;

        $len = $toi + $toj;

        $ii = $len - 1;

        if ($x == 0 && $y == 0) {
            // 从 0,0 开始
            for ($i = 0; $i < $toLen + 1; $i++) {
                //设$i为y轴坐标
                $f = 0;
                $color = $this->getColor($im, $alphas, $rgbaColor, $rgbaCount, $len, $ii--);
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
                $color = $this->getColor($im, $alphas, $rgbaColor, $rgbaCount, $len, $ii--);
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
                $color = $this->getColor($im, $alphas, $rgbaColor, $rgbaCount, $len, $ii--);
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
                $color = $this->getColor($im, $alphas, $rgbaColor, $rgbaCount, $len, $ii--);
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
     * @param $alphas
     * @param int $x
     * @param int $y
     * @return mixed
     */
    protected function linearGradientLeftTopRightBottomDiagonal($im, $toi, $toj, $rgbaColor, $rgbaCount, $alphas, $x = 0, $y = 0){
        $total = $toi + $toj + 1; // 对角线最大循环次数
        $isRectangle = $toi != $toj; // 判断是否是长方形
        // 获取中间位置数值
        $centerNum = !$isRectangle ? $this->centerNumSquare($total) : $this->centerNumRectangle($toi, $toj);

        $ii = $total; // 颜色计算递减数值

        $toiTag = 'ii'; // 默认宽大于长
        $tojTag = 'jj'; // 默认宽大于长
        if($toj > $toi){  // 长大于宽
            $toiTag = 'jj';
            $tojTag = 'ii';
        }

        if($isRectangle){ // 长方形
            for ($i=0; $i < $total; $i++) {
                $color = $this->getColor($im, $alphas, $rgbaColor, $rgbaCount, $total, $ii--);
                $im = $this->getPointRectangle($im, $i, $centerNum, $total, $color, $toiTag, $tojTag, $x, $y);
            }
        } else {
            // 正方形
            for ($i=0; $i < $total; $i++) {
                $color = $this->getColor($im, $alphas, $rgbaColor, $rgbaCount, $total, $ii--);
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
    protected function centerNumSquare($num){
        return $num / 2 ;
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
    protected function centerNumRectangle($x, $y){
        return $x > $y ? $y + 1 : $x + 1;
    }

    protected function getPointRectangle($im, $num, $centerNum, $total, $color, $toiTag, $tojTag, $x=0, $y=0){

        $len = $total - $centerNum * 2; // 求取对角线上相交线坐标到边的最大宽度数量

        $min = $centerNum; // 从第几次循环开始保持最大宽度
        $max = $min + $len; // 到第几结束

        if($num >= $min && $num <= $max){
            $ii = $num - $centerNum + 1;
            for ($jj=$centerNum - 1; $jj >= 0; $jj--) {
                imagesetpixel($im, ceil($$toiTag), abs($y - floor($$tojTag)), $color);
                $ii++;
            }

        } elseif ($num > $max){

            $num = $num - $centerNum;
            $ii = $num+1;
            $jj = $max - $len - 1;
            for ($i=$max; $i > $num; $i--) {
                imagesetpixel($im, ceil($$toiTag), abs($y - floor($$tojTag)), $color);
                $ii++;
                $jj--;
            }

        } else {

            for ($i=0; $i <= $num; $i++) {
                imagesetpixel($im, $i,  abs($y - ($num-$i)), $color);
            }

        }

        return $im;
    }

    protected function getPointSquare($im, $num, $centerNum, $color, $x=0, $y=0){
        if($num > $centerNum){
            $num = $num - $centerNum;
            $ii = $num;
            for ($i=$centerNum; $i >= $num; $i--) {
                // $arr[] = [ceil($ii) , floor($i)];
                imagesetpixel($im, ceil($ii), abs($y - floor($i)), $color);
                $ii++;
            }
        } else {

            for ($i=0; $i <= $num; $i++) {
                // $arr[] = [$i , $num-$i];
                imagesetpixel($im, $i, abs($y - ($num-$i)), $color);
            }
        }
        return $im;
    }
    /**
     * 计算画布X轴位置
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/10/18
     * Time: 16:10
     * @return int
     */
    protected function calcDstX($dst_x, $imWidth, $bgWidth)
    {


        if ($dst_x == '0') {
            return $dst_x;
        } elseif ($dst_x == 'center') {

            $dst_x = ceil(($imWidth - $bgWidth) / 2);

        } elseif (is_numeric($dst_x) && $dst_x < 0) {

            $dst_x = ceil($imWidth + $dst_x);

        } elseif (is_array($dst_x)) {
            if ($dst_x[0] == 'center') {
                $dst_x = ceil(($imWidth - $bgWidth) / 2) + $dst_x[1];
            }
        } elseif (strpos($dst_x, '%') !== false) {

            if (substr($dst_x, 0, strpos($dst_x, '%')) < 0) {

                $dst_x = ceil($imWidth + ($imWidth * substr($dst_x, 0, strpos($dst_x, '%')) / 100));

            } else {

                $dst_x = ceil($imWidth * substr($dst_x, 0, strpos($dst_x, '%')) / 100);

            }

        }

        return $dst_x;
    }

    /**
     * 计算画布Y轴位置
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/10/18
     * Time: 16:10
     * @return int
     */
    protected function calcDstY($dst_y, $imHeight, $bgHeight)
    {
        if ($dst_y == '0') {
            return $dst_y;
        } elseif ($dst_y == 'center') {

            $dst_y = ceil(($imHeight - $bgHeight) / 2);

        } elseif (is_numeric($dst_y) && $dst_y < 0) {

            $dst_y = ceil($imHeight + $dst_y);

        } elseif (is_array($dst_y)) {

            if ($dst_y[0] == 'center') {

                $dst_y = ceil(($imHeight - $bgHeight) / 2) + $dst_y[1];

            }

        } elseif (strpos($dst_y, '%') !== false) {

            if (substr($dst_y, 0, strpos($dst_y, '%')) < 0) {

                $dst_y = ceil($imHeight + (($imHeight * substr($dst_y, 0, strpos($dst_y, '%'))) / 100));

            } else {

                $dst_y = ceil($imHeight * substr($dst_y, 0, strpos($dst_y, '%')) / 100);

            }

        }

        return $dst_y;
    }

    /**
     * 创建背景
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/10/17
     * Time: 11:03
     * @param $w
     * @param $h
     * @param $rgba
     * @param $alpha
     * @throws PosterException
     */
    protected function Bg($w, $h, $rgba, $alpha = false, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $func = '')
    {
        // 判断颜色是否渐变
        if (PHP_VERSION <= 7) {
            $rgbaColor = isset($rgba['color']) ? $rgba['color'] : [[0, 0, 0]];
            $alphas = isset($rgba['alpha']) ? $rgba['alpha'] : 1;
            $to = isset($rgba['to']) ? $rgba['to'] : 'bottom';
            $radius = isset($rgba['radius']) ? $rgba['radius'] : 0;
        } else {
            $rgbaColor = $rgba['color'] ?? [[0, 0, 0]];
            $alphas = $rgba['alpha'] ?? 1;
            $to = $rgba['to'] ?? 'bottom';
            $radius = $rgba['radius'] ?? 0;
        }
        $rgbaCount = count($rgbaColor);

        // im不存在则创建
        if (empty($this->im)) {
            $this->Im($w, $h, [], $alpha);
        }
        // 渐变处理->直接处理im
        // 计算颜色方向
        $pic = $this->createIm($w, $h, [], $alpha);
        $this->calcColorDirection($pic, $rgbaColor, $rgbaCount, $alphas, $to, $w, $h);

        // 如果设置了圆角则画圆角
        if($radius) {
            $pic = $this->setPixelRadius($pic, $w, $h, $radius);
        }

        $dst_x = $this->calcDstX($dst_x, $this->im_w, $w);
        $dst_y = $this->calcDstY($dst_y, $this->im_h, $h);

        imagecopy($this->im, $pic, $dst_x, $dst_y, $src_x, $src_y, $w, $h);

        if ($func instanceof \Closure) {
            // 闭包处理 由于设计原因。。。先克隆处理一下
            $that = clone $this;
            $that->im = $pic;
            $that->im_w = $w;
            $that->im_h = $h;
            $func($that);
            imagecopy($this->im, $that->im, $dst_x, $dst_y, $src_x, $src_y, $w, $h);
            unset($that);
        }

        if (isset($pic) && is_resource($pic)) $this->destroyImage($pic);
        unset($rgbaCount);
        unset($rgbaColor);
        unset($alphas);
        unset($to);
        unset($w);
        unset($h);
        unset($rgba);
        unset($alpha);
        unset($dst_x);
        unset($dst_y);
        unset($src_x);
        unset($src_y);
    }

    /**
     * 创建画布
     */
    public function createIm($w, $h, $rgba, $alpha = false)
    {
        $cut = imagecreatetruecolor($w, $h);

        $white = $alpha ? $this->createColorAlpha($cut, $rgba) : $this->createColor($cut, $rgba);
        if ($alpha) {
            // imagecolortransparent($cut, $white);
            imagesavealpha($cut, true);
        }
        imagefill($cut, 0, 0, $white);

        return $cut;
    }

    /**
     * 获取颜色值，可设置透明度
     */
    public function createColorAlpha($cut, $rgba = [255, 255, 255, 127])
    {

        if (empty($rgba)) $rgba = [255, 255, 255, 127];
        if (count($rgba) != 4) throw new PosterException('The length is 4');
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
    protected function createColor($cut, $rgba = [255, 255, 255, 1])
    {

        if (empty($rgba)) $rgba = [255, 255, 255, 1];
        if (count($rgba) < 4) throw new PosterException('The text rgba length is 4');
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
     * 创建图片，合并到画布，释放内存
     */
    protected function CopyImage($src, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $alpha = false, $type = 'normal')
    {
        if (empty($this->im)) throw new PosterException('im resources not be found');

        $path = '';
        if (strpos($src, 'http') === false) {
            $absolute = $this->isAbsolute($src);
            if (!$absolute) {
                $path = $this->path;
            }
        }

        list($Width, $Hight, $bgType) = @getimagesize($path . $src);

        $bgType = image_type_to_extension($bgType, false);

        if (empty($bgType)) throw new PosterException('image resources cannot be empty (' . $path . $src . ')');

        if ($bgType == 'gif') {
            $pic = imagecreatefromstring(file_get_contents($path . $src));
        } else {

            $fun = 'imagecreatefrom' . $bgType;
            $pic = @$fun($path . $src);
        }

        $bgWidth = !empty($src_w) ? $src_w : $Width;
        $bgHeight = !empty($src_h) ? $src_h : $Hight;

        switch ($type) {
            case 'normal':

                # 自定义宽高的时候
                if (!empty($src_w) && !empty($src_h)) {
                    $circle_new = $this->createIm($bgWidth, $bgHeight, [255, 255, 255, 127], $alpha = true);
                    // $circle_new_white = imagecolorallocatealpha($circle_new, 255, 255, 255, 127);
                    // imagecolortransparent($circle_new,$circle_new_white);
                    // imagefill($circle_new, 0, 0, $circle_new_white);
                    $w_circle_new = $bgWidth;
                    $h_circle_new = $bgHeight;
                    # 按比例缩放
                    imagecopyresized($circle_new, $pic, 0, 0, 0, 0, $w_circle_new, $h_circle_new, $Width, $Hight);
                    $pic = $circle_new;
                }

                break;
            case 'circle':

                $circle = $this->createIm($bgWidth, $bgHeight, [255, 255, 255, 127], $alpha = true);
                $circle_new = $this->createIm($bgWidth, $bgHeight, [255, 255, 255, 127], $alpha = true);

                $w_circle = $bgWidth;
                $h_circle = $bgHeight;
                # 按比例缩放
                imagecopyresized($circle_new, $pic, 0, 0, 0, 0, $w_circle, $h_circle, $Width, $Hight);

                $r = ($w_circle / 2); //圆半径
                for ($x = 0; $x < $w_circle; $x++) {
                    for ($y = 0; $y < $h_circle; $y++) {
                        $rgbColor = imagecolorat($circle_new, $x, $y);
                        // $thisColor = imagecolorsforindex($circle_new, $rgbColor); // imagecolorallocatealpha

                        if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {

                            imagesetpixel($circle, $x, $y, $rgbColor);

                        }

                        // $newR = $r - 0.5;
                        // if (((($x - $newR) * ($x - $newR) + ($y - $newR) * ($y - $newR)) == ($newR * $newR))) {
                        //     imagesetpixel($circle, $x + 1, $y, $rgbColor);
                        //     imagesetpixel($circle, $x, $y + 1, $rgbColor);
                        //     imagesetpixel($circle, $x + 1, $y + 1, $rgbColor);
                        // }
                    }
                }

                $pic = $circle;
                break;
            default:
                # code...
                break;
        }

        # 处理目标 x 轴
        $dst_x = $this->calcDstX($dst_x, $this->im_w, $bgWidth);

        # 处理目标 y 轴
        $dst_y = $this->calcDstY($dst_y, $this->im_h, $bgHeight);

        //整合海报
        imagecopy($this->im, $pic, $dst_x, $dst_y, $src_x, $src_y, $bgWidth, $bgHeight);

        if (isset($pic) && is_resource($pic)) $this->destroyImage($pic);
        if (isset($circle) && is_resource($circle)) $this->destroyImage($circle);
        if (isset($circle_new) && is_resource($circle_new)) $this->destroyImage($circle_new);
        unset($path);
        unset($bgWidth);
        unset($bgHeight);
        unset($bgType);
    }

    protected function CopyMergeImage($src, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $alpha = false, $type = 'normal')
    {
        if (empty($this->im)) throw new PosterException('im resources not be found');

        $path = '';
        if (strpos($src, 'http') === false) {
            $absolute = $this->isAbsolute($src);
            if (!$absolute) {
                $path = $this->path;
            }
        }

        list($Width, $Hight, $bgType) = @getimagesize($path . $src);
        $bgType = image_type_to_extension($bgType, false);

        if (empty($bgType)) throw new PosterException('image resources cannot be empty (' . $path . $src . ')');

        if ($bgType == 'gif') {
            $pic = imagecreatefromstring(file_get_contents($path . $src));
        } else {

            $fun = 'imagecreatefrom' . $bgType;
            $pic = @$fun($path . $src);
        }

        $bgWidth = !empty($src_w) ? $src_w : $Width;
        $bgHeight = !empty($src_h) ? $src_h : $Hight;

        switch ($type) {
            case 'normal':

                $circle_new = $this->createIm($bgWidth, $bgHeight, [255, 255, 255, 127], $alpha = true);
                //整合水印
                imagecopy($circle_new, $pic, 0, 0, 0, 0, $bgWidth, $bgWidth);
                # 自定义宽高的时候
                if (!empty($src_w) && !empty($src_h)) {
                    // $circle_new_white = imagecolorallocatealpha($circle_new, 255, 255, 255, 127);
                    // imagecolortransparent($circle_new,$circle_new_white);
                    // imagefill($circle_new, 0, 0, $circle_new_white);
                    $w_circle_new = $bgWidth;
                    $h_circle_new = $bgHeight;
                    # 按比例缩放
                    imagecopyresized($circle_new, $pic, 0, 0, 0, 0, $w_circle_new, $h_circle_new, $Width, $Hight);
                    $pic = $circle_new;
                }

                break;
            case 'circle':

                $circle = $this->createIm($bgWidth, $bgHeight, [255, 255, 255, 127], $alpha = true);
                $circle_new = $this->createIm($bgWidth, $bgHeight, [255, 255, 255, 127], $alpha = true);

                $w_circle = $bgWidth;
                $h_circle = $bgHeight;
                # 按比例缩放
                imagecopyresized($circle_new, $pic, 0, 0, 0, 0, $w_circle, $h_circle, $Width, $Hight);

                $r = ($w_circle / 2); //圆半径
                for ($x = 0; $x < $w_circle; $x++) {
                    for ($y = 0; $y < $h_circle; $y++) {
                        $rgbColor = imagecolorat($circle_new, $x, $y);
                        if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                            imagesetpixel($circle, $x, $y, $rgbColor);
                        }
                    }
                }

                $pic = $circle;
                break;
            default:
                # code...
                break;
        }

        //整合水印
        imagecopymerge($this->im, $pic, $dst_x, $dst_y, $src_x, $src_y, $bgWidth, $bgHeight, 100);

        if (isset($circle) && is_resource($circle)) $this->destroyImage($circle);
        if (isset($circle_new) && is_resource($circle_new)) $this->destroyImage($circle_new);
    }

    /**
     * 合并文字
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/10/18
     * Time: 16:20
     * @param $content
     * @param $dst_x
     * @param $dst_y
     * @param $font
     * @param $rgba
     * @param int $max_w
     * @param string $font_family
     * @param int $weight
     * @param int $space
     * @return bool
     * @throws PosterException
     */
    protected function CopyText($content, $dst_x, $dst_y, $font, $rgba, $max_w = 0, $font_family = '', $weight = 1, $space = 0)
    {
        if (empty($this->im)) throw new PosterException('im resources not be found');

        $font = ($font * 3) / 4; // px 转化为 pt

        if ($content == '') return true;

        if(!empty($font_family)) {
            $isAbsolute = $this->isAbsolute($font_family);
            $font_family = !$isAbsolute ? $this->getDocumentRoot() . $font_family : realpath($font_family);
        } else {
            $font_family = $this->font_family;
        }

        $color = $this->createColorAlpha($this->im, $rgba);

        mb_internal_encoding('UTF-8'); // 设置编码

        // 这几个变量分别是 字体大小, 角度, 字体名称, 字符串, 预设宽度
        $contents = '';
        $letter = [];

        // 将字符串拆分成一个个单字 保存到数组 letter 中
        for ($i = 0; $i < mb_strlen($content); $i++) {
            $letter[] = mb_substr($content, $i, 1);
        }

        foreach ($letter as $l) {
            $teststr = $contents . ' ' . $l;
            $fontBox = imagettfbbox($font, 0, $font_family, $teststr);
            // $testbox = imagettfbbox($fontsize, $angle, $fontface, $teststr);
            // 判断拼接后的字符串是否超过预设的宽度
            $max_ws = $this->im_w;
            if (isset($max_w) && !empty($max_w)) {
                $max_ws = $max_w;
            }

            if (($fontBox[2] > $max_ws) && ($contents !== '')) {
                $contents .= "\n";
            }
            $contents .= $l;
        }

        if ($dst_x == 'center') {
            $dst_x = ceil(($this->im_w - $fontBox[2]) / 2);
        } elseif (is_array($dst_x)) {

            if ($dst_x[0] == 'center') {

                $dst_x = ceil(($this->im_w - $fontBox[2]) / 2) + $dst_x[1];
            }
        }

        # 自定义间距
        if ($space > 0) {

            $dst_x_old = $dst_x;
            for ($j = 0; $j < mb_strlen($contents); $j++) {

                if (mb_substr($contents, $j, 1) == "\n") {
                    $dst_x = $dst_x_old;
                    $dst_y += 2 * $font;
                    continue;
                }
                for ($i = 0; $i < $weight; $i++) {
                    imagettftext($this->im, $font, 0, $dst_x + ($i * 0.25), $dst_y + $font + ($i * 0.25), $color, $font_family, mb_substr($contents, $j, 1));
                }
                $dst_x += $space;
            }


        } else {
            for ($i = 0; $i < $weight; $i++) {
                imagettftext($this->im, $font, 0, $dst_x + ($i * 0.25), $dst_y + $font + ($i * 0.25), $color, $font_family, $contents);
            }
        }

    }

    /**
     * [CopyQr description]
     * @Author lang
     * @Date   2020-10-14T14:40:51+0800
     * @param  [type]                   $text   [description]
     * @param  [type]                   $size   [description]
     * @param  [type]                   $margin [description]
     * @param  [type]                   $dst_x  [description]
     * @param  [type]                   $dst_y  [description]
     * @param  [type]                   $src_x  [description]
     * @param  [type]                   $src_y  [description]
     */
    protected function CopyQr($text, $size, $margin, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h)
    {
        if (empty($this->im)) throw new PosterException('im resources not be found');

        $result = \QRcode::re_png($text, $size, $margin);
        if ($src_w > 0) {

            $bgWidth = $src_w;
            $Width = imagesx($result);
        } else {

            $bgWidth = imagesx($result);

        }

        if ($src_h > 0) {

            $bgHeight = $src_h;
            $Hight = imagesy($result);
        } else {

            $bgHeight = imagesy($result);

        }


        # 处理目标 x 轴
        $dst_x = $this->calcDstX($dst_x, $this->im_w, $bgWidth);


        # 处理目标 y 轴
        $dst_y = $this->calcDstY($dst_y, $this->im_h, $bgHeight);

        # 自定义宽高的时候
        if (!empty($src_w) && !empty($src_h)) {
            $circle_new = $this->createIm($bgWidth, $bgHeight, [255, 255, 255, 127], $alpha = true);
            // $circle_new_white = imagecolorallocatealpha($circle_new, 255, 255, 255, 127);
            // imagecolortransparent($circle_new,$circle_new_white);
            // imagefill($circle_new, 0, 0, $circle_new_white);
            $w_circle_new = $bgWidth;
            $h_circle_new = $bgHeight;
            # 按比例缩放
            imagecopyresized($circle_new, $result, 0, 0, 0, 0, $w_circle_new, $h_circle_new, $Width, $Hight);
            $result = $circle_new;
        }


        //整合海报
        imagecopy($this->im, $result, $dst_x, $dst_y, $src_x, $src_y, $bgWidth, $bgHeight);
        if (isset($circle_new) && is_resource($circle_new)) $this->destroyImage($circle_new);
        if (isset($result) && is_resource($result)) $this->destroyImage($result);
    }

    /**
     * [creatQr description]
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
    protected function creatQr($text, $outfile, $level, $size, $margin, $saveAndPrint)
    {
        if ($outfile) {
            $this->setPath($outfile);
            $this->setPathName($outfile);
            $this->dirExists($this->pathname);
        }
        \QRcode::png($text, $this->path . $outfile, $level, $size, $margin, $saveAndPrint);
        return ['url' => $outfile];
    }

    /**
     * 释放资源
     * @Author lang
     * @Date   2020-08-14T14:29:46+0800
     * @param Resource
     * @return [type]
     */
    protected function destroyImage($Resource)
    {

        !is_resource($this->im) || imagedestroy($Resource);
    }

    /**
     * 析构方法，用于销毁图像资源
     */
    public function __destruct()
    {
        empty($this->im) || !is_resource($this->im) || imagedestroy($this->im);
    }
}