<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/24
 * Time: 11:31
 */

namespace Kkokk\Poster\Image\Drivers;

use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Image\Graphics\ImageGraphicsEngine;
use Kkokk\Poster\Image\Graphics\ImageTextGraphicsEngine;
use Kkokk\Poster\Image\Imagick\Canvas;
use Kkokk\Poster\Image\Imagick\Image;
use Kkokk\Poster\Image\Imagick\ImageText;
use Kkokk\Poster\Image\Imagick\Qr;
use Kkokk\Poster\Image\Imagick\Text;
use Kkokk\Poster\Image\Traits\ImagickTrait;

class ImagickDriver extends Driver implements DriverInterface
{
    use ImagickTrait;

    function __construct()
    {
    }

    public function im($w, $h, $rgba = [255, 255, 255, 1], $alpha = false)
    {
        if (!empty($rgba)) {
            $rgba = $alpha ? array_pad($rgba, 4, 1) : array_slice($rgba, 0, 3);
        }
        $this->canvas = new Canvas($w, $h, $rgba);
        $this->setCanvasConfig($this->canvas);
    }

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

    public function Bg($w, $h, $rgba = [], $alpha = false, $dstX = 0, $dstY = 0, $srcX = 0, $srcY = 0, $query = [])
    {
        // 判断颜色是否渐变
        list($rgbaColor, $transparency, $to, $radius, $contentAlpha) = $this->maskBackgroundResolve($rgba);

        // im不存在则创建
        if (empty($this->canvas)) {
            $this->im($w, $h, [255, 255, 255, 127], $alpha);
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

        // 设置透明度，内容也透明
        if ($alpha && $contentAlpha) {
            $mask->transparent($transparency);
        }

        if ($radius) {
            // 圆角处理
            $mask->borderRadius($radius);
        }

        $this->canvas->addImage($mask, $dstX, $dstY, $srcX, $srcY);
    }

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
    }

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
        if (empty($content)) {
            return true;
        }

        if (empty($this->canvas)) {
            throw new PosterException('Image resources not be found');
        }

        if ($content instanceof ImageTextGraphicsEngine && !$content instanceof \Kkokk\Poster\Image\Imagick\ImageText) {
            throw new PosterException('Content must be: \Kkokk\Poster\Image\Imagick\ImageText');
        }

        if ($content instanceof ImageText) {
            $this->canvas->addImageText($content, $dstX, $dstY);
            return true;
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
        return true;
    }

    public function CopyLine($x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0, $rgba = [], $type = '', $weight = 1)
    {
        $color = $this->createColor($rgba);
        $draw = $this->createImagickDraw();
        $draw->setStrokeColor($color);
        $draw->setStrokeWidth($weight);
        switch ($type) {
            case 'rectangle':
                $draw->setFillColor($this->createColor());
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

        if ($this->canvas->getType() == 'gif') {
            // 每帧长宽不一致问题, 水印会不一致
            foreach ($this->canvas->getImage() as $frame) {
                $frame->drawImage($draw);
            }
        } else {
            $this->canvas->getImage()->drawImage($draw);
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
        $color = $this->createColor($rgba);
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
                $draw->setFillColor($this->createColor([255, 255, 255, 127]));
                $draw->arc($cx - $wr, $cy - $hr, $cx + $wr, $cy + $hr, $s, $e);
                break;
        }
        if ($this->canvas->getType() == 'gif') {
            foreach ($this->canvas->getImage() as $frame) {
                $frame->drawImage($draw);
            }
        } else {
            $this->canvas->getImage()->drawImage($draw);
        }
    }

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
}