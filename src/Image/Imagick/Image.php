<?php
/**
 * User: lang
 * Date: 2024/11/27
 * Time: 13:28
 */

namespace Kkokk\Poster\Image\Imagick;

use Kkokk\Poster\Image\Traits\ImagickTrait;

class Image
{
    use ImagickTrait;

    private $image;
    private $width;
    private $height;

    private $circleCropped = false;

    public function __construct($path)
    {
        $this->image = $this->createImagick($path);
        $this->width = $this->image->getImageWidth();
        $this->height = $this->image->getImageHeight();
    }

    /**
     * 缩放会降低质量，适合小尺寸图、缩略图
     * 内存使用低
     * User: lang
     * Date: 2024/11/27
     * Time: 13:58
     * @param $newWidth
     * @param $newHeight
     * @param $bestFit bool 是否保持原图比例，默认 false 表示强制指定宽高
     */
    public function thumb($newWidth, $newHeight, $bestFit = false)
    {
        $this->image->thumbnailImage($newWidth, $newHeight, $bestFit);
        $this->width = $newWidth;
        $this->height = $newHeight;
        return $this;
    }

    /**
     * 缩放保持较高质量
     * 内存使用较 thumb 高
     * User: lang
     * Date: 2024/11/27
     * Time: 13:58
     * @param $newWidth
     * @param $newHeight
     * @param $bestFit bool 是否保持原图比例，默认 false 表示强制指定宽高
     */
    public function scale($newWidth, $newHeight, $bestFit = false)
    {
        $this->image->scaleImage($newWidth, $newHeight, $bestFit);
        $this->width = $newWidth;
        $this->height = $newHeight;
        return $this;
    }

    /**
     * 圆形剪裁
     * User: lang
     * Date: 2024/11/27
     * Time: 14:02
     */
    public function circle()
    {
        if ($this->circleCropped) {
            return $this;
        }
        $this->image->setImageFormat("png");
        $this->image->setImageMatte(true); // 激活遮罩通道

        // 创建一个圆形遮罩图片
        $mask = $this->createImagick();

        $bgWidth = $this->width;
        $bgHeight = $this->height;

        $mask->newImage($bgWidth, $bgHeight, $this->createColor([255, 255, 255, 127]));

        $circle = $this->createImagickDraw();
        $circle->setFillColor($this->createColor());
        $circle->circle($bgWidth / 2, $bgHeight / 2, $bgWidth / 2, $bgHeight);

        $mask->drawImage($circle);

        // 合并原始图片和圆形遮罩图片
        $this->image->compositeImage($mask, ($this->image)::COMPOSITE_DSTIN, 0, 0);

        $this->destroyImage($circle);
        $this->destroyImage($mask);
        $this->circleCropped = true;

        return $this;
    }

    public function rotate($angle, $bgColor = [255, 255, 255, 127])
    {
        // 旋转图片
        $this->image->rotateImage($this->createColor($bgColor), $angle);

        // 获取旋转后图片的尺寸
        $rotatedWidth = $this->image->getImageWidth();
        $rotatedHeight = $this->image->getImageHeight();

        // 创建与原图相同大小的裁剪区域
        $croppedImage = $this->createCanvas($this->width, $this->height, [255, 255, 255, 127]);

        // 计算偏移（将旋转后的图像中心对齐到裁剪区域中心）
        $offsetX = ($rotatedWidth - $this->width) / 2;
        $offsetY = ($rotatedHeight - $this->height) / 2;

        // 合成裁剪区域
        $croppedImage->compositeImage($this->image, ($this->image)::COMPOSITE_DEFAULT, -$offsetX, -$offsetY);

        // 更新图像资源
        $this->image = $croppedImage;
        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function __clone()
    {
        $this->image = clone $this->image;
    }
}