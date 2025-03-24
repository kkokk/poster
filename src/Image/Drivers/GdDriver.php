<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/24
 * Time: 11:30
 */

namespace Kkokk\Poster\Image\Drivers;

use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Image\Gd\Canvas;
use Kkokk\Poster\Image\Gd\Image;
use Kkokk\Poster\Image\Gd\Text;
use Kkokk\Poster\Image\Traits\GdTrait;

class GdDriver extends Driver implements DriverInterface
{
    use GdTrait;

    function __construct()
    {
    }

    /**
     * 创建指定宽高，颜色，透明的画布
     */
    public function Im($w, $h, $rgba = [255, 255, 255, 1], $alpha = false)
    {
        if (!empty($rgba)) {
            $rgba = $alpha ? array_pad($rgba, 4, 1) : array_slice($rgba, 0, 3);
        }
        $this->canvas = new Canvas($w, $h, $rgba);
    }

    /**
     * 创建指定图片为画布 宽高，颜色，透明的画布
     */
    public function ImDst($source, $w = 0, $h = 0)
    {
        if ($source instanceof Canvas) {
            $this->canvas = $source;
        } else {
            $this->canvas = new Canvas();
            $this->canvas->readImage($source, $w, $h);
        }
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
     * @param int   $dstX
     * @param int   $dstY
     * @param int   $srcX
     * @param int   $srcY
     * @param array $query
     */
    public function Bg($w, $h, $rgba = [], $alpha = false, $dstX = 0, $dstY = 0, $srcX = 0, $srcY = 0, $query = [])
    {
        // 判断颜色是否渐变
        list($rgbaColor, $transparency, $to, $radius, $contentAlpha) = $this->maskBackgroundResolve($rgba);

        // im不存在则创建
        if (empty($this->canvas)) {
            $this->Im($w, $h, [], $alpha);
        }
        // 渐变处理->直接处理im
        // 计算颜色方向
        $background = [255, 255, 255];
        $mask = new Canvas($w, $h, $alpha ? array_merge($background, [127]) : $background);
        $mask->linearGradient($rgbaColor, $to);

        // 设置透明度，内容不透明
        if ($alpha && !$contentAlpha) {
            $mask->transparent($transparency);
        }

        if (!empty($query)) {
            $that = clone $this;
            $that->ImDst($mask);
            $that->execute($query, $that);
        }

        // 设置透明度内容也透明
        if ($alpha && $contentAlpha) {
            $mask->transparent($transparency);
        }

        // 如果设置了圆角则画圆角
        if ($radius) {
            $mask->borderRadius($radius);
        }

        $this->canvas->addImage($mask, $dstX, $dstY, $srcX, $srcY);
    }

    /**
     * 创建图片，合并到画布，释放内存
     */
    public function CopyImage(
        $src,
        $dstX = 0,
        $dstY = 0,
        $srcX = 0,
        $srcY = 0,
        $srcWidth = 0,
        $srcHeight = 0,
        $alpha = false,
        $type = 'normal'
    ) {
        if (empty($this->canvas)) {
            throw new PosterException('Image resources not be found');
        }

        $angle = 0;
        if (is_array($src)) {
            $angle = isset($src['angle']) ? $src['angle'] : 0;
            $src = isset($src['src']) ? $src['src'] : '';
            if (empty($src)) {
                throw new PosterException('Image resources cannot be empty (' . $src . ')');
            }
        }
        $image = new Image($src);
        $image->rotate($angle);
        $image->scale($srcWidth, $srcHeight);
        if ($type == 'circle') {
            $image->circle();
        }

        $this->canvas->addImage($image, $dstX, $dstY, $srcX, $srcY);
    }

    /**
     * 合并文字
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2023/2/13
     * Time: 15:33
     * @param        $content
     * @param        $dstX
     * @param        $dstY
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
        $dstX = 0,
        $dstY = 0,
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
        if (empty($this->canvas)) {
            throw new PosterException('Image resources not be found');
        }
        if (!empty($font)) {
            $font = get_real_path($font);
        } else {
            $font = $this->font;
        }
        $text = new Text();
        $text->config([
            'font_family' => $font,
            'font_size'   => $fontSize ?: $this->font_size,
            'font_color'  => $rgba ?: $this->font_rgba,
            'font_weight' => $weight ?: $this->font_weight,
            'font_max_w'  => $max_w ?: $this->font_max_w,
            'font_space'  => $space ?: $this->font_space,
            'font_angle'  => $angle ?: $this->font_angle,
        ]);
        $text->setText($content);
        $this->canvas->addText($text, $dstX, $dstY);
        return true;
    }

    public function CopyLine($x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0, $rgba = [], $type = '', $weight = 1)
    {
        imagesetthickness($this->canvas->getImage(), $weight); // 划线的线宽加粗
        $color = $this->createColor($this->canvas->getImage(), $rgba);

        switch ($type) {
            case 'rectangle':
                imagerectangle($this->canvas->getImage(), $x1, $y1, $x2, $y2, $color);
                break;
            case 'filled_rectangle':
            case 'filledRectangle':
                imagerectangle($this->canvas->getImage(), $x1, $y1, $x2, $y2, $color);
                imagefilledrectangle($this->canvas->getImage(), $x1, $y1, $x2, $y2, $color);
                break;
            default:
                imageline($this->canvas->getImage(), $x1, $y1, $x2, $y2, $color);
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
        imagesetthickness($this->image, $weight); // 划线的线宽加粗
        $color = $this->createColor($this->image, $rgba);

        switch ($type) {
            case 'filled_arc':
            case 'filledArc':
                imagearc($this->image, $cx, $cy, $w, $h, $s, $e, $color);
                $style = $style ?: IMG_ARC_PIE;
                // IMG_ARC_PIE
                // IMG_ARC_CHORD
                // IMG_ARC_NOFILL
                // IMG_ARC_EDGED
                imagefilledarc($this->image, $cx, $cy, $w, $h, $s, $e, $color, $style);
                break;
            default:
                imagearc($this->image, $cx, $cy, $w, $h, $s, $e, $color);
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
     * @param  [type]                   $dstX  [description]
     * @param  [type]                   $dstY  [description]
     * @param  [type]                   $srcX  [description]
     * @param  [type]                   $srcY  [description]
     */
    public function CopyQr(
        $text,
        $level = 'L',
        $size = 4,
        $margin = 1,
        $dstX = 0,
        $dstY = 0,
        $srcX = 0,
        $srcY = 0,
        $srcWidth = 0,
        $srcHeight = 0
    ) {
        if (empty($this->image)) {
            throw new PosterException('Image resources not be found');
        }

        $result = \QRcode::re_png($text, $level, $size, $margin);
        if ($srcWidth > 0) {

            $bgWidth = $srcWidth;
            $Width = imagesx($result);
        } else {

            $bgWidth = imagesx($result);

        }

        if ($srcHeight > 0) {

            $bgHeight = $srcHeight;
            $Height = imagesy($result);
        } else {

            $bgHeight = imagesy($result);

        }


        # 处理目标 x 轴
        $dstX = calc_dst_x($dstX, $this->im_w, $bgWidth);


        # 处理目标 y 轴
        $dstY = calc_dst_y($dstY, $this->im_h, $bgHeight);

        # 自定义宽高的时候
        if (!empty($srcWidth) && !empty($srcHeight)) {
            $circle_new = $this->createIm($bgWidth, $bgHeight, [255, 255, 255, 127], true);
            $w_circle_new = $bgWidth;
            $h_circle_new = $bgHeight;
            # 按比例缩放
            imagecopyresized($circle_new, $result, 0, 0, 0, 0, $w_circle_new, $h_circle_new, $Width, $Height);
            $result = $circle_new;
        }


        //整合海报
        imagecopy($this->image, $result, $dstX, $dstY, $srcX, $srcY, $bgWidth, $bgHeight);
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
        $this->image = $this->cropHandle($this->image, ['x' => $x, 'y' => $y, 'width' => $width, 'height' => $height]);
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
     * 析构方法，用于销毁图像资源
     */
    public function __destruct()
    {
        empty($this->image) || !is_resource($this->image) || imagedestroy($this->image);
    }
}