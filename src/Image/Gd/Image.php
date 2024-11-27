<?php
/**
 * User: lang
 * Date: 2024/11/26
 * Time: 11:03
 */

namespace Kkokk\Poster\Image\Gd;

use Kkokk\Poster\Image\Traits\GdTrait;

class Image
{
    use GdTrait;

    private $image;
    private $width;
    private $height;

    public function __construct($path)
    {
        list($this->image, $this->width, $this->height) = $this->createImage($path);
    }

    // 获取图片资源
    public function getImage()
    {
        return $this->image;
    }

    // 获取宽度
    public function getWidth()
    {
        return $this->width;
    }

    // 获取高度
    public function getHeight()
    {
        return $this->height;
    }

    public function thumb($newWidth, $newHeight, $bestFit = false)
    {
        if ($bestFit) {
            $aspectRatio = $this->width / $this->height;
            if ($newWidth / $newHeight > $aspectRatio) {
                $newWidth = $newHeight * $aspectRatio;
            } else {
                $newHeight = $newWidth / $aspectRatio;
            }
        }
        $resizedImage = $this->createCanvas($newWidth, $newHeight, [], true);
        imagecopyresized($resizedImage, $this->image, 0, 0, 0, 0, $newWidth, $newHeight, $this->width, $this->height);
        $this->image = $resizedImage;
        $this->width = $newWidth;
        $this->height = $newHeight;
        return $this;
    }

    public function scale($newWidth, $newHeight, $bestFit = false)
    {
        if ($bestFit) {
            $aspectRatio = $this->width / $this->height;
            if ($newWidth / $newHeight > $aspectRatio) {
                $newWidth = $newHeight * $aspectRatio;
            } else {
                $newHeight = $newWidth / $aspectRatio;
            }
        }

        $resizedImage = $this->createCanvas($newWidth, $newHeight, [], true);
        imagecopyresampled($resizedImage, $this->image, 0, 0, 0, 0, $newWidth, $newHeight, $this->width, $this->height);
        $this->image = $resizedImage;
        $this->width = $newWidth;
        $this->height = $newHeight;
        return $this;
    }

    // 圆形裁剪
    public function circle()
    {
        $size = min($this->width, $this->height);
        $circleImage = $this->createCanvas($size, $size, [], true);

        // 绘制圆形区域
        $centerX = $size / 2;
        $centerY = $size / 2;
        $radius = $size / 2;
        for ($x = 0; $x < $size; $x++) {
            for ($y = 0; $y < $size; $y++) {
                $dx = $x - $centerX;
                $dy = $y - $centerY;
                if ($dx * $dx + $dy * $dy <= $radius * $radius) {
                    $color = imagecolorat($this->image, $x, $y);
                    imagesetpixel($circleImage, $x, $y, $color);
                }
            }
        }
        $this->image = $circleImage;
        $this->width = $size;
        $this->height = $size;
        return $this;
    }

    // 旋转图片
    public function rotate($angle, $bgColor = [255, 255, 255])
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
