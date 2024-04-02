<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/24
 * Time: 11:30
 */

namespace Kkokk\Poster\Image\Drivers;


use Kkokk\Poster\Common\Common;
use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Image\Traits\GdTrait;

class GdDriver extends Driver implements DriverInterface
{
    use GdTrait;

    private $common;

    function __construct()
    {
        $this->common = new Common();
    }

    public function getData($path = '')
    {
        if ($path) {
            $this->setFilePath($path);
        }
        return $this->returnImage($this->type);
    }

    public function getStream()
    {
        return $this->returnImage($this->type, false);
    }

    public function getBaseData()
    {
        return $this->common->baseData($this->im, $this->type);
    }

    public function setData()
    {
        return $this->setImage($this->source);
    }

    public function getIm()
    {
        return $this->im;
    }

    public function blob()
    {
        return $this->getBlob($this->type, $this->im);
    }

    public function tmp()
    {
        return $this->getTmp($this->type, $this->im);
    }

    /**
     * 创建指定宽高，颜色，透明的画布
     */
    public function Im($w, $h, $rgba = [255, 255, 255, 1], $alpha = false)
    {
        $this->im_w = $w;
        $this->im_h = $h;
        $this->im = $this->createIm($w, $h, $rgba, $alpha);
    }

    /**
     * 创建指定图片为画布 宽高，颜色，透明的画布
     */
    public function ImDst($source, $w = 0, $h = 0)
    {
        $this->source = $source;
        list($cut, $bgWidth, $bgHeight) = $this->createImage($source);

        //设定水印图像的混色模式
        imagealphablending($cut, true);

        if (!empty($w) && !empty($h)) {
            $this->im_w = $w;
            $this->im_h = $h;
            $circle_new = $this->createIm($w, $h, [255, 255, 255, 127], true);
            imagecopyresized($circle_new, $cut, 0, 0, 0, 0, $w, $h, $bgWidth, $bgHeight);
            $cut = $circle_new;
            // $this->destroyImage($circle_new);
        } else {
            $this->im_w = $bgWidth;
            $this->im_h = $bgHeight;
            $circle_new = $this->createIm($bgWidth, $bgHeight, [255, 255, 255, 127], true);
            imagecopy($circle_new, $cut, 0, 0, 0, 0, $bgWidth, $bgHeight);
            $cut = $circle_new;
        }

        $this->im = $cut;
    }

    /**
     * 创建背景
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/3/25
     * Time: 17:55
     * @param int   $w     宽
     * @param int   $h     高
     * @param array $rgba  背景颜色
     * @param false $alpha 是否透明
     * @param int   $dst_x
     * @param int   $dst_y
     * @param int   $src_x
     * @param int   $src_y
     * @param array $query
     */
    public function Bg($w, $h, $rgba = [], $alpha = false, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $query = [])
    {
        // 判断颜色是否渐变
        $rgbaColor = isset($rgba['color']) ? $rgba['color'] : [[0, 0, 0]];
        $alphas = isset($rgba['alpha']) ? $rgba['alpha'] : 1;
        $to = isset($rgba['to']) ? $rgba['to'] : 'bottom';
        $radius = isset($rgba['radius']) ? $rgba['radius'] : 0;
        $contentAlpha = isset($rgba['content_alpha']) ? $rgba['content_alpha'] : false;
        $rgbaCount = count($rgbaColor);

        // im不存在则创建
        if (empty($this->im)) {
            $this->Im($w, $h, [], $alpha);
        }
        // 渐变处理->直接处理im
        // 计算颜色方向
        $pic = $this->createIm($w, $h, [], $alpha);
        $this->calcColorDirection($pic, $rgbaColor, $rgbaCount, $to, $w, $h);

        // 设置透明度，内容不透明
        if ($alpha && !$contentAlpha) {
            $pic = $this->setImageAlpha($pic, $w, $h, $alphas);
        }

        $dst_x = $this->calcDstX($dst_x, $this->im_w, $w);
        $dst_y = $this->calcDstY($dst_y, $this->im_h, $h);

        if (!empty($query)) {
            $that = clone $this;
            $that->im = $pic;
            $that->im_w = $w;
            $that->im_h = $h;
            $that->execute($query, $that);
        }

        // 设置透明度内容也透明
        if ($alpha && $contentAlpha) {
            $pic = $this->setImageAlpha($pic, $w, $h, $alphas);
        }

        // 如果设置了圆角则画圆角
        if ($radius) {
            $pic = $this->setPixelRadius($pic, $w, $h, $radius);
        }

        imagecopy($this->im, $pic, $dst_x, $dst_y, $src_x, $src_y, $w, $h);

        if (isset($pic) && is_resource($pic)) {
            $this->destroyImage($pic);
        }
        if (isset($mask) && is_resource($mask)) {
            $this->destroyImage($mask);
        }
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

    public function getRotatedPoints($x, $y, $angle)
    {
        $theta = deg2rad($angle);
        $cs = cos($theta);
        $sn = sin($theta);
        $newX = $x * $cs - $y * $sn;
        $newY = $x * $sn + $y * $cs;
        return array($newX, $newY);
    }

    public function matrix_multiply($matrix1, $matrix2)
    {
        $result = array();
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $sum = 0;
                for ($k = 0; $k < 3; $k++) {
                    $sum += $matrix1[$i][$k] * $matrix2[$k][$j];
                }
                $result[$i][$j] = $sum;
            }
        }
        return $result;
    }

    /**
     * 创建图片，合并到画布，释放内存
     */
    public function CopyImage(
        $src,
        $dst_x = 0,
        $dst_y = 0,
        $src_x = 0,
        $src_y = 0,
        $src_w = 0,
        $src_h = 0,
        $alpha = false,
        $type = 'normal'
    ) {
        $angle = 0;
        if (empty($this->im)) {
            throw new PosterException('im resources not be found');
        }

        if (is_array($src)) {
            $angle = isset($src['angle']) ? $src['angle'] : 0;
            $src = isset($src['src']) ? $src['src'] : '';
            if (empty($src)) {
                throw new PosterException('image resources cannot be empty (' . $src . ')');
            }
        }

        list($pic, $Width, $Height) = $this->createImage($src);

        $bgWidth = !empty($src_w) ? $src_w : $Width;
        $bgHeight = !empty($src_h) ? $src_h : $Height;

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
                    imagecopyresized($circle_new, $pic, 0, 0, 0, 0, $w_circle_new, $h_circle_new, $Width, $Height);
                    $pic = $circle_new;
                }

                break;
            case 'circle':

                $circle = $this->createIm($bgWidth, $bgHeight, [255, 255, 255, 127], $alpha = true);
                $circle_new = $this->createIm($bgWidth, $bgHeight, [255, 255, 255, 127], $alpha = true);

                $w_circle = $bgWidth;
                $h_circle = $bgHeight;
                # 按比例缩放
                imagecopyresized($circle_new, $pic, 0, 0, 0, 0, $w_circle, $h_circle, $Width, $Height);

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

        # 处理旋转
        if ($angle > 0) {
            $pic = imagerotate($pic, abs($angle % 360 - 360), $this->createColorAlpha($this->im));
            //获取旋转后的宽高
            $newWidth = imagesx($pic);
            $newHeight = imagesy($pic);
            if (empty($src_w)) {
                $bgWidth = $newWidth;
            } else {
                if ($newWidth != $newHeight) {
                    $bgWidth = $newWidth;
                }
                $src_x = ceil(($newWidth - $bgWidth) / 2);
            }
            if (empty($src_h)) {
                $bgHeight = $newHeight;
            } else {
                if ($newWidth != $newHeight) {
                    $bgHeight = $newHeight;
                }
                $src_y = ceil(($newHeight - $bgHeight) / 2);
            }
        }

        //整合海报
        imagecopy($this->im, $pic, $dst_x, $dst_y, $src_x, $src_y, $bgWidth, $bgHeight);

        if (isset($pic) && is_resource($pic)) {
            $this->destroyImage($pic);
        }
        if (isset($circle) && is_resource($circle)) {
            $this->destroyImage($circle);
        }
        if (isset($circle_new) && is_resource($circle_new)) {
            $this->destroyImage($circle_new);
        }
        unset($path);
        unset($bgWidth);
        unset($bgHeight);
        unset($bgType);
    }

    /**
     * 合并图片
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/12/12
     * Time: 9:52
     * @param        $src
     * @param        $dst_x
     * @param        $dst_y
     * @param        $src_x
     * @param        $src_y
     * @param        $src_w
     * @param        $src_h
     * @param false  $alpha
     * @param string $type
     * @throws PosterException
     */
    public function CopyMergeImage(
        $src,
        $dst_x,
        $dst_y,
        $src_x,
        $src_y,
        $src_w,
        $src_h,
        $alpha = false,
        $type = 'normal'
    ) {
        if (empty($this->im)) {
            throw new PosterException('im resources not be found');
        }

        if (strpos($src, 'http') === false) {
            $src = $this->getRealRoute($src);
        }

        list($Width, $Height, $bgType) = @getimagesize($src);
        $bgType = image_type_to_extension($bgType, false);

        if (empty($bgType)) {
            throw new PosterException('image resources cannot be empty (' . $src . ')');
        }

        // if ($bgType == 'gif') {
        //     $pic = imagecreatefromstring(file_get_contents($src));
        // } else {
        //
        //     $fun = 'imagecreatefrom' . $bgType;
        //     $pic = @$fun($src);
        // }

        $fun = 'imagecreatefrom' . $bgType;
        $pic = @$fun($src);

        $bgWidth = !empty($src_w) ? $src_w : $Width;
        $bgHeight = !empty($src_h) ? $src_h : $Height;

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
                    imagecopyresized($circle_new, $pic, 0, 0, 0, 0, $w_circle_new, $h_circle_new, $Width, $Height);
                    $pic = $circle_new;
                }

                break;
            case 'circle':

                $circle = $this->createIm($bgWidth, $bgHeight, [255, 255, 255, 127], $alpha = true);
                $circle_new = $this->createIm($bgWidth, $bgHeight, [255, 255, 255, 127], $alpha = true);

                $w_circle = $bgWidth;
                $h_circle = $bgHeight;
                # 按比例缩放
                imagecopyresized($circle_new, $pic, 0, 0, 0, 0, $w_circle, $h_circle, $Width, $Height);

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

        if (isset($circle) && is_resource($circle)) {
            $this->destroyImage($circle);
        }
        if (isset($circle_new) && is_resource($circle_new)) {
            $this->destroyImage($circle_new);
        }
    }

    /**
     * 合并文字
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/2/13
     * Time: 15:33
     * @param        $content
     * @param        $dst_x
     * @param        $dst_y
     * @param        $fontSize
     * @param        $rgba
     * @param int    $max_w
     * @param string $font
     * @param int    $weight
     * @param int    $space
     * @return bool
     * @throws PosterException
     */
    public function CopyText(
        $content,
        $dst_x = 0,
        $dst_y = 0,
        $fontSize = null,
        $rgba = null,
        $max_w = null,
        $font = null,
        $weight = null,
        $space = null,
        $angle = null
    ) {
        if ($content == '') {
            return true;
        }

        if (empty($this->im)) {
            throw new PosterException('im resources not be found');
        }

        $fontSize = $fontSize ?: $this->font_size;
        $rgba = $rgba ?: $this->font_rgba;
        $max_w = $max_w ?: $this->font_max_w;
        $weight = $weight ?: $this->font_weight;
        $space = $space ?: $this->font_space;
        $angle = $angle ?: $this->font_angle;

        if (!empty($font)) {
            $font = $this->getRealRoute($font);
        } else {
            $font = $this->font;
        }

        $calcSpace = $space > $fontSize ? ($space - $fontSize) : 0; // 获取间距计算值

        $fontSize = ($fontSize * 3) / 4; // px 转化为 pt

        $color = $this->createColorAlpha($this->im, $rgba);

        mb_internal_encoding('UTF-8'); // 设置编码

        $max_ws = $this->im_w;
        if (isset($max_w) && !empty($max_w)) {
            $max_ws = $max_w;
        }

        // 这几个变量分别是 字体大小, 角度, 字体名称, 字符串, 预设宽度
        $contents = '';
        $contentsArr = [];
        $letter = [];
        $line = 1;
        $calcSpaceRes = 0;

        // 主动设置是否解析html标签
        if (is_array($content)) {

            if (!isset($content['type'])) {
                throw new PosterException('type is required');
            }
            if (!isset($content['content'])) {
                throw new PosterException('content is required');
            }

            $type = $content['type'];
            $content = $content['content'];

            // 确认包含才处理
            if ($type == 'html' && preg_match('/<[^>]*>/', $content)) {

                // 正则匹配 span 属性
                $pattern = '/<span style="(.*?)">(.*?)<\/span>/i';

                // 分割字符串
                $matches = preg_split($pattern, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

                for ($i = 0; $i < count($matches); $i += 3) {
                    if (!empty($matches[$i])) {
                        $this->getNodeValue($letter, $matches[$i], $color);
                    }

                    if (isset($matches[$i + 1])) {
                        $style = $matches[$i + 1];
                        $colorValue = $this->getStyleAttr($style);
                        $colorCustom = $this->createColorAlpha($this->im,
                            $this->common->getNodeStyleColor($colorValue));
                        $this->getNodeValue($letter, $matches[$i + 2], $colorCustom);
                    }
                }

            } else {
                $this->getNodeValue($letter, $content, $color);
            }

            $normalSize = imagettfbbox($fontSize, 0, $font, '好');
            $punctuationSize = imagettfbbox($fontSize, 0, $font, '，');
            // 计算标点符号的水平偏移量
            $horizontalOffset = abs($punctuationSize[2] - $normalSize[2]) / 2;

            $textWidthArr = [];
            foreach ($letter as $l) {
                $textStr = $contents . $l['value'];
                $fontBox = imagettfbbox($fontSize, $angle, $font, $textStr);
                $textWidth = abs($fontBox[2]) + $calcSpaceRes + 2;
                if (preg_match('/[\x{3002}\x{ff0c}\x{ff1f}\x{ff01}\x{ff1a}\x{ff1b}]/u', $l['value'])) {
                    $textWidth += $horizontalOffset;
                }

                if ($l['value'] == "\n") {
                    $contents = "";
                    $contentsArr[] = $this->getLetterArr();
                    $line++;
                    continue;
                }

                if (!isset($textWidthArr[$line])) {
                    $textWidthArr[$line] = -$space / 2;
                }
                if (($textWidth > $max_ws || $textWidthArr[$line] > $max_ws) && ($contents !== '')) {
                    // 判断拼接后的字符串是否超过预设的宽度
                    $contents = "";
                    $contentsArr[] = $this->getLetterArr();
                    $line++;
                    $textWidthArr[$line] = -$space / 2;
                }
                $contents .= $l['value'];

                $fontBox1 = imagettfbbox($fontSize, $angle, $font, $l['value']);
                $l['w'] = abs($fontBox1[2]) + $calcSpace + 2;
                if (preg_match('/[\x{3002}\x{ff0c}\x{ff1f}\x{ff01}\x{ff1a}\x{ff1b}]/u', $l['value'])) {
                    $l['w'] += $horizontalOffset;
                }
                $textWidthArr[$line] += $l['w'];
                $contentsArr[] = $l;

                $line === 1 && $calcSpaceRes += $calcSpace;
            }

            $calcFont = [
                'text_width'  => max(array_values($textWidthArr)), // 取最宽行宽
                'text_height' => abs($fontBox[1] - $fontBox[7]),
            ];
            $dst_x = $this->calcTextDstX($dst_x, $calcFont);

            $dst_y = $this->calcTextDstY($dst_y, $calcFont);
            # 自定义间距
            $this->fontWeightArr($weight, $fontSize, $angle, $dst_x, $dst_y, $color, $font, $contentsArr);

            return true;

        } else {
            // 将字符串拆分成一个个单字 保存到数组 letter 中
            for ($i = 0; $i < mb_strlen($content); $i++) {
                $letter[] = mb_substr($content, $i, 1);
            }

            $textWidthArr = [];
            $contentStr = '';
            foreach ($letter as $l) {
                $textStr = $contentStr . $l;
                $fontBox = imagettfbbox($fontSize, $angle, $font, $textStr);
                $textWidth = abs($fontBox[2]) + $calcSpaceRes;
                $textWidthArr[$line] = $textWidth;
                // 判断拼接后的字符串是否超过预设的宽度

                if (($textWidth > $max_ws) && ($contents !== '')) {
                    $contents .= "\n";
                    $contentStr = "";
                    $line++;
                }
                $contents .= $l;
                $contentStr .= $l;
                $line === 1 && $calcSpaceRes += $calcSpace;

                $calcFont = [
                    'text_width'  => max(array_values($textWidthArr)),
                    'text_height' => abs($fontBox[1] - $fontBox[7]),
                ];
            }

            $dst_x = $this->calcTextDstX($dst_x, $calcFont);

            $dst_y = $this->calcTextDstY($dst_y, $calcFont);

            # 自定义间距
            if ($space > 0) {

                $dst_x_old = $dst_x;
                for ($j = 0; $j < mb_strlen($contents); $j++) {

                    $spaceStr = mb_substr($contents, $j, 1);
                    if ($spaceStr == "\n") {
                        $dst_x = $dst_x_old;
                        $dst_y += 1.75 * $fontSize;
                        continue;
                    }
                    $this->fontWeight($weight, $fontSize, $angle, $dst_x, $dst_y, $color, $font, $spaceStr);
                    $dst_x += $space;
                }


            } else {
                $this->fontWeight($weight, $fontSize, $angle, $dst_x, $dst_y, $color, $font, $contents);
            }

            return true;
        }
    }

    public function CopyLine($x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0, $rgba = [], $type = '', $weight = 1)
    {
        imagesetthickness($this->im, $weight); // 划线的线宽加粗
        $color = $this->createColorAlpha($this->im, $rgba);

        switch ($type) {
            case 'rectangle':
                imagerectangle($this->im, $x1, $y1, $x2, $y2, $color);
                break;
            case 'filled_rectangle':
            case 'filledRectangle':
                imagerectangle($this->im, $x1, $y1, $x2, $y2, $color);
                imagefilledrectangle($this->im, $x1, $y1, $x2, $y2, $color);
                break;
            default:
                imageline($this->im, $x1, $y1, $x2, $y2, $color);
                break;
        }
    }

    public function CopyArc(
        $cx = 0,
        $cy = 0,
        $w = 0,
        $h = 0,
        $s = 0,
        $e = 0,
        $rgba = [],
        $type = '',
        $style = '',
        $weight = 1
    ) {
        imagesetthickness($this->im, $weight); // 划线的线宽加粗
        $color = $this->createColorAlpha($this->im, $rgba);

        switch ($type) {
            case 'filled_arc':
            case 'filledArc':
                imagearc($this->im, $cx, $cy, $w, $h, $s, $e, $color);
                $style = $style ?: IMG_ARC_PIE;
                // IMG_ARC_PIE
                // IMG_ARC_CHORD
                // IMG_ARC_NOFILL
                // IMG_ARC_EDGED
                imagefilledarc($this->im, $cx, $cy, $w, $h, $s, $e, $color, $style);
                break;
            default:
                imagearc($this->im, $cx, $cy, $w, $h, $s, $e, $color);
                break;
        }
    }

    /**
     * 合并二维码
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
    public function CopyQr(
        $text,
        $level = 'L',
        $size = 4,
        $margin = 1,
        $dst_x = 0,
        $dst_y = 0,
        $src_x = 0,
        $src_y = 0,
        $src_w = 0,
        $src_h = 0
    ) {
        if (empty($this->im)) {
            throw new PosterException('im resources not be found');
        }

        $result = \QRcode::re_png($text, $level, $size, $margin);
        if ($src_w > 0) {

            $bgWidth = $src_w;
            $Width = imagesx($result);
        } else {

            $bgWidth = imagesx($result);

        }

        if ($src_h > 0) {

            $bgHeight = $src_h;
            $Height = imagesy($result);
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
            imagecopyresized($circle_new, $result, 0, 0, 0, 0, $w_circle_new, $h_circle_new, $Width, $Height);
            $result = $circle_new;
        }


        //整合海报
        imagecopy($this->im, $result, $dst_x, $dst_y, $src_x, $src_y, $bgWidth, $bgHeight);
        if (isset($circle_new) && is_resource($circle_new)) {
            $this->destroyImage($circle_new);
        }
        if (isset($result) && is_resource($result)) {
            $this->destroyImage($result);
        }
    }

    /**
     * 裁剪
     * Author: lang
     * Date: 2024/3/12
     * Time: 11:22
     * @param $x
     * @param $y
     * @param $width
     * @param $height
     */
    public function crop($x = 0, $y = 0, $width = 0, $height = 0)
    {
        $this->im = imagecrop($this->im, ['x' => $x, 'y' => $y, 'width' => $width, 'height' => $height]);
        $this->im_w = $width;
        $this->im_h = $height;
    }

    public function execute($query = [], Driver $driver = null)
    {
        if (empty($driver)) {
            $driver = $this;
        }
        foreach ($query as $item) {
            $driver->run($item, $driver);
        }
        return $driver;
    }

    /**
     * 释放资源
     * @Author lang
     * @Date   2020-08-14T14:29:46+0800
     * @param Resource
     */
    protected function destroyImage($Resource)
    {

        !is_resource($Resource) || imagedestroy($Resource);
    }

    /**
     * 析构方法，用于销毁图像资源
     */
    public function __destruct()
    {
        empty($this->im) || !is_resource($this->im) || imagedestroy($this->im);
    }
}