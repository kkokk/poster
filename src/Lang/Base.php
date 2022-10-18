<?php

namespace Kkokk\Poster\Lang;
require_once(__DIR__ . '/../PHPQrcode/phpqrcode.php');

/**
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 11:21:08
 * @Last Modified by:   lang
 * @Last Modified time: 2022-03-10 17:58:41
 */

use Kkokk\Poster\Exception\PosterException;

/**
 *
 */
class Base
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
        $this->path = iconv('UTF-8', 'GBK', $_SERVER['DOCUMENT_ROOT']);
        $this->path = $absolute ? '' : ($this->path ? $this->path . '/' : __DIR__ . '/../../tests/');
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
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/10/18
     * Time: 16:19
     * @param $im
     * @param $rgbaColor
     * @param $rgbaCount
     * @param $alphas
     * @param $to
     * @param $w
     * @param $h
     */
    protected function calcColorDirection($im, $rgbaColor, $rgbaCount, $alphas, $to, $w, $h)
    {

        if ($to == 'top') {
            $toi = $h;
            $toj = $w;
            $rgbaColor = array_reverse($rgbaColor);
            $this->linearGradient($im, $toi, $toj, $rgbaColor, $rgbaCount, $alphas);
        } elseif ($to == 'left') {
            $toi = $w;
            $toj = $h;
            $this->linearGradient($im, $toi, $toj, $rgbaColor, $rgbaCount, $alphas, 'j', 'i');
        } elseif ($to == 'right') {
            $toi = $w;
            $toj = $h;
            $rgbaColor = array_reverse($rgbaColor);
            $this->linearGradient($im, $toi, $toj, $rgbaColor, $rgbaCount, $alphas, 'j', 'i');
        } else {
            $toi = $h;
            $toj = $w;
            $this->linearGradient($im, $toi, $toj, $rgbaColor, $rgbaCount, $alphas);
        }
    }

    /**
     * 渐变处理方法
     * @Author lang
     * @Email: 732853989@qq.com
     * Date: 2022/10/19
     * Time: 上午12:13
     * @param $im
     * @param $toi
     * @param $toj
     * @param $rgbaColor
     * @param $rgbaCount
     * @param $alphas
     * @param string $ii
     * @param string $jj
     * @return mixed
     */
    protected function linearGradient($im, $toi, $toj, $rgbaColor, $rgbaCount, $alphas, $ii = 'i', $jj = 'j')
    {
        for ($i = $toi; $i >= 0; $i--) {
            // $colorRgb = $this->calcColor($h, $i, $color1, $color2);
            $colorRgb = $this->calcColorArea($rgbaColor, $rgbaCount, $toi, $i);
            $color = imagecolorallocatealpha($im, $colorRgb[0], $colorRgb[1], $colorRgb[2], $alphas);
            // imagefilledrectangle($this->im, 0, $i, $w, 0, $color); // 填充颜色
            // $color = ($colorRgb[0] << 16) + ($colorRgb[1] << 8) + $colorRgb[2];  // 获取颜色参数
            for ($j = 0; $j < $toj; $j++) {
                imagesetpixel($im, $$jj, $$ii, $color);
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
        } else {
            $rgbaColor = $rgba['color'] ?? [[0, 0, 0]];
            $alphas = $rgba['alpha'] ?? 1;
            $to = $rgba['to'] ?? 'bottom';
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

        $dst_x = $this->calcDstX($dst_x, $this->im_w, $w);
        $dst_y = $this->calcDstY($dst_y, $this->im_h, $h);

        imagecopy($this->im, $pic, $dst_x, $dst_y, $src_x, $src_y, $w, $h);

        if($func instanceof \Closure) {
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
    protected function createIm($w, $h, $rgba, $alpha = false)
    {
        $cut = imagecreatetruecolor($w, $h);

        $white = $alpha ? $this->createColorAlpha($cut, $rgba) : $this->createColor($cut, $rgba);
        if ($alpha) {
            imagecolortransparent($cut, $white);
            imagesavealpha($cut, true);
        }
        imagefill($cut, 0, 0, $white);

        return $cut;
    }

    /**
     * 获取颜色值，可设置透明度
     */
    protected function createColorAlpha($cut, $rgba = [255, 255, 255, 127])
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

        $font_family = !empty($font_family) ? $this->path . $font_family : $this->font_family;

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

        imagedestroy($Resource);
    }

    /**
     * 析构方法，用于销毁图像资源
     */
    public function __destruct()
    {
        empty($this->im) || imagedestroy($this->im);
    }
}