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

    // 等比缩放
    public function resize($newWidth, $newHeight)
    {
        $aspectRatio = $this->width / $this->height;
        if ($newWidth / $newHeight > $aspectRatio) {
            $newWidth = $newHeight * $aspectRatio;
        } else {
            $newHeight = $newWidth / $aspectRatio;
        }
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
        imagecopyresampled($resizedImage, $this->image, 0, 0, 0, 0, $newWidth, $newHeight, $this->width, $this->height);
        $this->image = $resizedImage;
        $this->width = $newWidth;
        $this->height = $newHeight;
        return $this;
    }

    // 圆形裁剪
    public function cropToCircle()
    {
        $size = min($this->width, $this->height);
        $circleImage = imagecreatetruecolor($size, $size);
        imagealphablending($circleImage, false);
        imagesavealpha($circleImage, true);

        // 填充透明背景
        $transparent = imagecolorallocatealpha($circleImage, 0, 0, 0, 127);
        imagefill($circleImage, 0, 0, $transparent);

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
}
