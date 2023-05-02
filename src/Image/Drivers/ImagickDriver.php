<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/24
 * Time: 11:31
 */

namespace Kkokk\Poster\Image\Drivers;

use Kkokk\Poster\Common\Common;
use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Image\Traits\ImagickTrait;

class ImagickDriver extends Driver implements DriverInterface
{
    use ImagickTrait;

    protected $ImagickDraw;

    function __construct()
    {

    }

    public function getData($path = '')
    {
        if ($path) {
            $this->setFilePath($path);
        }
        $this->setDPI();
        return $this->returnImage($this->type);
    }

    public function getStream()
    {
        $this->setDPI();
        return $this->returnImage($this->type, false);
    }

    public function getBaseData()
    {
        $this->setDPI();
        $common = new Common();
        return $common->baseData($this->im->getImageBlob(), $this->type);
    }

    public function setData()
    {
        $this->setDPI();
        return $this->setImage($this->source);
    }

    public function im($w, $h, $rgba = [255, 255, 255, 1], $alpha = false)
    {
        $this->im_w = $w;
        $this->im_h = $h;
        $this->im = $this->createIm($w, $h, $rgba, $alpha);
    }

    public function ImDst($source, $w = 0, $h = 0)
    {
        // if (!is_file($source)) {
        //     throw new PosterException('水印图像不存在');
        // }
        $this->source = $source;

        $pic = $this->createImagick($source);

        $bgWidth = $pic->getImageWidth();
        $bgHeight = $pic->getImageHeight();
        $this->type = strtolower($pic->getImageFormat());

        if (!empty($w) && !empty($h)) {
            $this->im_w = $w;
            $this->im_h = $h;
            $pic->resizeImage($w, $h, $pic::FILTER_LANCZOS, 1, true);
        } else {
            $this->im_w = $bgWidth;
            $this->im_h = $bgHeight;
        }

        $this->im = $pic;
    }

    public function Bg($w, $h, $rgba = [], $alpha = false, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $query = [])
    {
        // 判断颜色是否渐变
        $rgbaColor = isset($rgba['color']) ? $rgba['color'] : [[0, 0, 0]];
        $alphas = isset($rgba['alpha']) ? $rgba['alpha'] : 1;
        $to = isset($rgba['to']) ? $rgba['to'] : 'bottom';
        $radius = isset($rgba['radius']) ? $rgba['radius'] : 0;
        $rgbaCount = count($rgbaColor);

        // im不存在则创建
        if (empty($this->im)) {
            $this->im($w, $h, [], $alpha);
        }
        // 渐变处理->直接处理im
        // 计算颜色方向
        $pic = $this->createIm($w, $h, [], $alpha);

        $this->calcColorDirection($pic, $rgbaColor, $rgbaCount, $to, $w, $h);

        $pic->setImageAlpha(round((128 - $alphas) / 127, 2)); // 透明度

        // if ($radius) {
        //     // 暂不支持圆角处理
        // }

        $dst_x = $this->calcDstX($dst_x, $this->im_w, $w);
        $dst_y = $this->calcDstY($dst_y, $this->im_h, $h);

        if (!empty($query)) {
            $that = clone $this;
            $that->im = $pic;
            $that->im_w = $w;
            $that->im_h = $h;
            $that->execute($query, $that);

            // 合并图片
            $pic->compositeImage($that->im, ($that->im)::COMPOSITE_DEFAULT, $dst_x, $dst_y);
        }

        // 裁剪图片
        $this->cropImage($pic, $src_x, $src_y);

        // 合并图片
        $this->im->compositeImage($pic, ($this->im)::COMPOSITE_DEFAULT, $dst_x, $dst_y);

        if($that) unset($that);
        $this->destroyImage($pic);
    }

    public function CopyImage($src, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $src_w = 0, $src_h = 0, $alpha = false, $type = 'normal')
    {
        if (empty($this->im)) throw new PosterException('im resources not be found');

        if (strpos($src, 'http') === false) {
            $src = $this->getRealRoute($src);
        }

        $pic = $this->createImagick($src);

        $Width = $pic->getImageWidth();
        $Height = $pic->getImageHeight();

        $bgWidth = !empty($src_w) ? $src_w : $Width;
        $bgHeight = !empty($src_h) ? $src_h : $Height;

        switch ($type) {
            case 'normal':
                # 自定义宽高的时候
                if (!empty($src_w) && !empty($src_h)) {
                    $pic->resizeImage($bgWidth, $bgHeight, $pic::FILTER_LANCZOS, 1, true);
                }
                break;
            case 'circle':
                if (!empty($src_w) && !empty($src_h)) {
                    $pic->resizeImage($bgWidth, $bgHeight, $pic::FILTER_LANCZOS, 1, true);
                }
                // 创建一个圆形遮罩图片

                $mask = $this->createImagick();

                $mask->newImage($bgWidth, $bgHeight, $this->createColorAlpha([255, 255, 255, 127]));

                $circle = $this->createImagickDraw();
                $circle->setFillColor($this->createColorAlpha([255, 255, 255, 1]));
                $circle->circle($bgWidth / 2, $bgHeight / 2, $bgWidth / 2, $bgHeight);

                $mask->drawImage($circle);

                // 合并原始图片和圆形遮罩图片
                $pic->compositeImage($mask, $pic::COMPOSITE_DSTIN, 0, 0);

                $this->destroyImage($circle);
                $this->destroyImage($mask);
                break;
            default:
                # code...
                break;
        }

        # 处理目标 x 轴
        $dst_x = $this->calcDstX($dst_x, $this->im_w, $bgWidth);

        # 处理目标 y 轴
        $dst_y = $this->calcDstY($dst_y, $this->im_h, $bgHeight);

        // 裁剪图片
        $this->cropImage($pic, $src_x, $src_y);
        // 合并图片
        $this->im->compositeImage($pic, ($this->im)::COMPOSITE_DEFAULT, $dst_x, $dst_y);


        $this->destroyImage($pic);
    }

    public function CopyText($content, $dst_x = 0, $dst_y = 0, $fontSize = null, $rgba = null, $max_w = null, $font = null, $weight = null, $space = null, $angle = null)
    {
        if ($content == '') return true;

        if (empty($this->im)) throw new PosterException('im resources not be found');

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

        $color = $this->createColorAlpha($rgba);

        // 这几个变量分别是 字体大小, 角度, 字体名称, 字符串, 预设宽度
        $contents = '';
        $letter = [];

        // 将字符串拆分成一个个单字 保存到数组 letter 中
        for ($i = 0; $i < mb_strlen($content); $i++) {
            $letter[] = mb_substr($content, $i, 1);
        }

        $max_ws = $this->im_w;
        if (isset($max_w) && !empty($max_w)) {
            $max_ws = $max_w;
        }

        $draw = $this->createTextImagickDraw();
        $draw->setFont($font);
        $draw->setFillColor($color);
        $draw->setFontSize($fontSize);

        $line = 1;
        $calcSpaceRes = 0;
        foreach ($letter as $l) {
            $textStr = $contents . ' ' . $l;
            $fontBox = $this->im->queryFontMetrics($draw, $textStr);
            $textWidth = abs($fontBox['textWidth'] + $fontBox['descender']) + $calcSpaceRes;
            // 判断拼接后的字符串是否超过预设的宽度
            if (($textWidth > $max_ws) && ($contents !== '')) {
                $contents .= "\n";
                $line++;
            }
            $contents .= $l;
            $line === 1 && $calcSpaceRes += $calcSpace;
        }

        $calcFont = [
            'text_width' => $textWidth,
            'text_height' => abs($fontBox['textHeight'] + $fontBox['descender']),
        ];
        $dst_x = $this->calcTextDstX($dst_x, $calcFont) - $fontBox['descender']; // 调整和 gd 的误差值

        $dst_y = $this->calcTextDstY($dst_y, $calcFont);

        $fontSize = ($fontSize * 3) / 4; // 使和gd一致

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
                $this->fontWeight($draw, $weight, $fontSize, $angle, $dst_x, $dst_y, $spaceStr);
                $dst_x += $space;
            }

        } else {
            $this->fontWeight($draw, $weight, $fontSize, $angle, $dst_x, $dst_y, $contents);
        }
    }

    public function CopyLine($x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0, $rgba = [], $type = '', $weight = 1)
    {
        $color = $this->createColorAlpha($rgba);
        $draw = $this->createImagickDraw();
        $draw->setStrokeColor($color);
        $draw->setStrokeWidth($weight);
        switch ($type) {
            case 'rectangle':
                $draw->setFillColor($this->createColorAlpha());
                $draw->rectangle($x1, $y1, $x2, $y2);
                break;
            case 'filled_rectangle':
            case 'filledRectangle':
                $draw->rectangle($x1, $y1, $x2, $y2);
                break;
            default:
                $draw->line($x1, $y1, $x2, $y2);
                break;
        }
        $this->im->drawImage($draw);
    }

    public function CopyArc($cx = 0, $cy = 0, $w = 0, $h = 0, $s = 0, $e = 0, $rgba = [], $type = '', $style = '', $weight = 1)
    {
        $color = $this->createColorAlpha($rgba);
        $draw = $this->createImagickDraw();
        $draw->setStrokeColor($color);
        $draw->setStrokeWidth($weight);
        $wr = $w / 2;
        $hr = $h / 2;
        switch ($type) {
            case 'filled_arc':
            case 'filledArc':
                $draw->arc($cx - $wr, $cy - $hr, $cx + $wr, $cy + $hr, $s, $e);
                break;
            default:
                $draw->setFillColor($this->createColorAlpha());
                $draw->arc($cx - $wr, $cy - $hr, $cx + $wr, $cy + $hr, $s, $e);
                break;
        }
        $this->im->drawImage($draw);
    }

    public function CopyQr($text, $size = 4, $margin = 1, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $src_w = 0, $src_h = 0)
    {
        if (empty($this->im)) throw new PosterException('im resources not be found');

        $qr = \QRcode::re_png($text, $size, $margin);

        $bgWidth = imagesx($qr);
        $bgHeight = imagesy($qr);

        ob_start(); // 打开一个输出缓冲区
        $this->poster_type['png']($qr); // 将 GD 图像输出到缓冲区
        $imageData = ob_get_contents(); // 从缓冲区中读取图像数据
        ob_end_clean();

        $pic = $this->createImagick();
        $pic->readImageBlob($imageData);

        if ($src_w > 0) {
            $bgWidth = $src_w;
        }

        if ($src_h > 0) {
            $bgHeight = $src_h;
        }

        # 处理目标 x 轴
        $dst_x = $this->calcDstX($dst_x, $this->im_w, $bgWidth);

        # 处理目标 y 轴
        $dst_y = $this->calcDstY($dst_y, $this->im_h, $bgHeight);

        # 自定义宽高的时候
        if (!empty($src_w) && !empty($src_h)) {
            $pic->resizeImage($bgWidth, $bgHeight, $pic::FILTER_LANCZOS, 1, true);
        }

        // 裁剪图片
        $this->cropImage($pic, $src_x, $src_y);
        // 合并图片
        $this->im->compositeImage($pic, ($this->im)::COMPOSITE_DEFAULT, $dst_x, $dst_y);

        !is_resource($qr) || imagedestroy($qr);
        $this->destroyImage($pic);
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

    public function destroyImage($Imagick)
    {
        empty($Imagick) || $Imagick->destroy();
    }

    /**
     * 析构方法，用于销毁图像资源
     */
    public function __destruct()
    {
        empty($this->im) || $this->im->destroy();
        empty($this->ImagickDraw) || $this->ImagickDraw->destroy();
    }
}