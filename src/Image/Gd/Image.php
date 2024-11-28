<?php
/**
 * User: lang
 * Date: 2024/11/26
 * Time: 11:03
 */

namespace Kkokk\Poster\Image\Gd;

use Kkokk\Poster\Image\Graphics\GdGraphicsEngine;

class Image extends GdGraphicsEngine
{
    public function __construct($path)
    {
        list($this->image, $this->width, $this->height) = $this->createImage($path);
    }

    // 旋转图片
    public function rotate($angle, $bgColor = [255, 255, 255, 127])
    {
        $rotatedImage = imagerotate($this->image, abs($angle % 360 - 360), $this->createColor($bgColor));

        // 获取旋转后图片的尺寸
        $rotatedWidth = imagesx($rotatedImage);
        $rotatedHeight = imagesy($rotatedImage);

        // 创建与原图相同大小的裁剪区域
        $croppedImage = $this->createCanvas($this->width, $this->height, [], true);

        // 计算偏移（将旋转后的图像中心对齐到裁剪区域中心）
        $offsetX = ($rotatedWidth - $this->width) / 2;
        $offsetY = ($rotatedHeight - $this->height) / 2;

        // 裁剪旋转后的图像
        imagecopy($croppedImage, $rotatedImage, 0, 0, $offsetX, $offsetY, $this->width, $this->height);

        // 更新图像资源
        $this->image = $croppedImage;
        return $this;
    }
}
