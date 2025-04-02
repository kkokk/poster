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
use Kkokk\Poster\Image\Gd\ImageText;
use Kkokk\Poster\Image\Gd\Qr;
use Kkokk\Poster\Image\Gd\Text;
use Kkokk\Poster\Image\Graphics\ImageGraphicsEngine;
use Kkokk\Poster\Image\Graphics\ImageTextGraphicsEngine;
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
        $this->setCanvasConfig($this->canvas);
    }

    /**
     * 创建指定图片为画布 宽高，颜色，透明的画布
     */
    public function ImDst($source, $w = 0, $h = 0)
    {
        if ($source instanceof Canvas) {
            $this->canvas = $source;
        } else {
            if ($source instanceof ImageGraphicsEngine) {
                $source = $source->blob();
            }
            $this->canvas = new Canvas();
            $this->setCanvasConfig($this->canvas);
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
            $this->Im($w, $h, [255, 255, 255, 127], $alpha);
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
        $this->destroyImage($mask->getImage());
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
        $this->destroyImage($image->getImage());
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
     * @param int    $maxWidth
     * @param string $font
     * @param int    $weight
     * @param int    $space
     * @return void
     * @throws PosterException
     */
    public function CopyText(
        $content,
        $dstX = 0,
        $dstY = 0,
        $fontSize = null,
        $rgba = null,
        $maxWidth = null,
        $font = null,
        $weight = null,
        $space = null,
        $angle = null
    ) {
        if (empty($content) && $content != 0) {
            return;
        }

        if (empty($this->canvas)) {
            throw new PosterException('Image resources not be found');
        }

        if ($content instanceof ImageTextGraphicsEngine && !$content instanceof \Kkokk\Poster\Image\Gd\ImageText) {
            throw new PosterException('Content must be: \Kkokk\Poster\Image\Gd\ImageText');
        }

        if ($content instanceof ImageText) {
            $this->canvas->addImageText($content, $dstX, $dstY);
            return;
        }

        if (!$content instanceof Text) {
            $content = (new Text())->config($this->configs)->setText($content);
            $font && $content->setFont($font);
            $fontSize && $content->setFontSize($fontSize);
            $angle && $content->setFontAngle($angle);
            $weight && $content->setFontWeight($weight);
            $maxWidth && $content->setMaxWidth($maxWidth);
            $rgba && $content->setFontColor($rgba);
            $space && $content->setFontSpace($space);
        }

        $imageText = new ImageText();
        $imageText
            ->config($this->configs)
            ->addText($content);
        if ($maxWidth) {
            $imageText->setMaxWidth($maxWidth);
        }
        $this->canvas->addImageText($imageText, $dstX, $dstY);
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
            case 'only_filled_rectangle':
            case 'onlyFilledRectangle':
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
        imagesetthickness($this->canvas->getImage(), $weight); // 划线的线宽加粗
        $color = $this->createColor($this->canvas->getImage(), $rgba);

        switch ($type) {
            case 'filled_arc':
            case 'filledArc':
                imagearc($this->canvas->getImage(), $cx, $cy, $w, $h, $s, $e, $color);
                $style = $style ?: IMG_ARC_PIE;
                // IMG_ARC_PIE
                // IMG_ARC_CHORD
                // IMG_ARC_NOFILL
                // IMG_ARC_EDGED
                imagefilledarc($this->canvas->getImage(), $cx, $cy, $w, $h, $s, $e, $color, $style);
                break;
            default:
                imagearc($this->canvas->getImage(), $cx, $cy, $w, $h, $s, $e, $color);
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
        if (empty($this->canvas)) {
            throw new PosterException('Image resources not be found');
        }
        if ($text instanceof Qr) {
            $this->canvas->addImage($text, $dstX, $dstY, $srcX, $srcY);
            return;
        }
        $qr = new Qr($text, $level, $size, $margin);
        # 自定义宽高的时候
        if (!empty($srcWidth) && !empty($srcHeight)) {
            $qr->scale($srcWidth, $srcHeight);
        }
        $this->canvas->addImage($qr, $dstX, $dstY, $srcX, $srcY);
        $this->destroyImage($qr->getImage());
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
        $this->canvas->crop($x, $y, $width, $height);
    }

    public function newCanvas($width, $height, $background = [])
    {
        return new Canvas($width, $height, $background);
    }

    public function newImage($src)
    {
        return new Image($src);
    }

    public function newImageText()
    {
        return new ImageText();
    }

    public function newText()
    {
        return new Text();
    }
}