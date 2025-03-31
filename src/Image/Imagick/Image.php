<?php
/**
 * User: lang
 * Date: 2024/11/27
 * Time: 13:28
 */

namespace Kkokk\Poster\Image\Imagick;

use Kkokk\Poster\Image\Graphics\ImagickImageGraphicsEngine;

class Image extends ImagickImageGraphicsEngine
{
    public function __construct($path)
    {
        $this->image = $this->createImagick($path);
        $this->width = $this->image->getImageWidth();
        $this->height = $this->image->getImageHeight();
    }

    public function rotate($angle, $bgColor = [255, 255, 255, 127])
    {
        if ($angle == 0) {
            return $this;
        }
        // 旋转图片
        $this->image->rotateImage($this->createColor($bgColor), $angle);

        // 获取旋转后图片的尺寸
        $rotatedWidth = $this->image->getImageWidth();
        $rotatedHeight = $this->image->getImageHeight();

        // 创建与原图相同大小的裁剪区域
        $croppedImage = $this->createCanvas($this->width, $this->height);

        // 计算偏移（将旋转后的图像中心对齐到裁剪区域中心）
        $offsetX = ($rotatedWidth - $this->width) / 2;
        $offsetY = ($rotatedHeight - $this->height) / 2;

        // 合成裁剪区域
        $croppedImage->compositeImage($this->image, ($this->image)::COMPOSITE_DEFAULT, -$offsetX, -$offsetY);

        // 更新图像资源
        $this->image = $croppedImage;
        return $this;
    }
}