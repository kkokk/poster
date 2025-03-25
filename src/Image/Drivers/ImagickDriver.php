<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/24
 * Time: 11:31
 */

namespace Kkokk\Poster\Image\Drivers;

use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Image\Imagick\Canvas;
use Kkokk\Poster\Image\Imagick\Image;
use Kkokk\Poster\Image\Imagick\ImageText;
use Kkokk\Poster\Image\Imagick\Text;
use Kkokk\Poster\Image\Traits\ImagickTrait;

class ImagickDriver extends Driver implements DriverInterface
{
    use ImagickTrait;

    protected $image;

    protected $ImagickDraw;

    function __construct()
    {

    }

    public function im($w, $h, $rgba = [255, 255, 255, 1], $alpha = false)
    {
        if (!empty($rgba)) {
            $rgba = $alpha ? array_pad($rgba, 4, 1) : array_slice($rgba, 0, 3);
        }
        $this->canvas = new Canvas($w, $h, $rgba);
    }

    public function ImDst($source, $w = 0, $h = 0)
    {
        if ($source instanceof Canvas) {
            $this->canvas = $source;
        } else {
            $this->canvas = new Canvas();
            $this->canvas->readImage($source, $w, $h);
        }
    }

    public function Bg($w, $h, $rgba = [], $alpha = false, $dstX = 0, $dstY = 0, $srcX = 0, $srcY = 0, $query = [])
    {
        // 判断颜色是否渐变
        list($rgbaColor, $transparency, $to, $radius, $contentAlpha) = $this->maskBackgroundResolve($rgba);

        // im不存在则创建
        if (empty($this->canvas)) {
            $this->im($w, $h, [], $alpha);
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

        if (!$content instanceof Text) {
            $content = (new Text())->setFont(!empty($font) ? get_real_path($font) : $this->font)
                ->setFontSize($fontSize ?: $this->font_size)
                ->setFontAngle($angle ?: $this->font_angle)
                ->setFontWeight($weight ?: $this->font_weight)
                ->setMaxWidth($max_w ?: $this->font_max_w)
                ->setFontColor($rgba ?: $this->font_rgba)
                ->setFontSpace($space ?: $this->font_space)
                ->setFontAlign('center')
                ->setText($content);
        }
        $imageText = new ImageText();
        $imageText->addText($content);
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

        if ($this->type == 'gif') {
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
                $draw->setFillColor($this->createColor());
                $draw->arc($cx - $wr, $cy - $hr, $cx + $wr, $cy + $hr, $s, $e);
                break;
        }
        if ($this->type == 'gif') {
            // 每帧长宽不一致问题, 水印会不一致
            foreach ($this->image as $frame) {
                $frame->drawImage($draw);
            }
        } else {
            $this->image->drawImage($draw);
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
        if (empty($this->image)) {
            throw new PosterException('Image resources not be found');
        }

        $qr = \QRcode::re_png($text, $level, $size, $margin);

        $bgWidth = imagesx($qr);
        $bgHeight = imagesy($qr);

        ob_start();                          // 打开一个输出缓冲区
        gd_image_create('png')($qr);         // 将 GD 图像输出到缓冲区
        $imageData = ob_get_contents();      // 从缓冲区中读取图像数据
        ob_end_clean();

        $pic = $this->createImagick();
        $pic->readImageBlob($imageData);

        if ($srcWidth > 0) {
            $bgWidth = $srcWidth;
        }

        if ($srcHeight > 0) {
            $bgHeight = $srcHeight;
        }

        # 处理目标 x 轴
        $dstX = calc_dst_x($dstX, $this->im_w, $bgWidth);

        # 处理目标 y 轴
        $dstY = calc_dst_y($dstY, $this->im_h, $bgHeight);

        # 自定义宽高的时候
        if (!empty($srcWidth) && !empty($srcHeight)) {
            $pic->resizeImage($bgWidth, $bgHeight, $pic::FILTER_LANCZOS, 1, true);
        }

        // 裁剪图片
        $this->cropImage($pic, $srcX, $srcY);
        // 合并图片
        if ($this->type == 'gif') {
            // 每帧长宽不一致问题, 水印会不一致
            foreach ($this->image as $frame) {
                $frame->compositeImage($pic, ($this->image)::COMPOSITE_DEFAULT, $dstX, $dstY);
            }
        } else {
            $this->image->compositeImage($pic, ($this->image)::COMPOSITE_DEFAULT, $dstX, $dstY);
        }

        !is_resource($qr) || imagedestroy($qr);
        $this->destroyImage($pic);
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
        $this->image = $this->cropHandle($this->image, $width, $height, $x, $y);
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
        empty($this->image) || $this->image->destroy();
        empty($this->ImagickDraw) || $this->ImagickDraw->destroy();
    }
}