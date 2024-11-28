<?php
/**
 * User: lang
 * Date: 2024/11/28
 * Time: 9:26
 */

namespace Kkokk\Poster\Image\Graphics;

use Kkokk\Poster\Image\Traits\GdTrait;

class GdGraphicsEngine extends GraphicsEngine
{
    use GdTrait;

    public function getData($path = '')
    {
        if ($path) {
            $this->setFilePath($path);
        }
        return $this->returnImage($this->type);
    }

    public function getStream()
    {
        return $this->returnImage($this->type, false);
    }

    public function getBaseData()
    {
        return base64_data($this->image, $this->type);
    }

    public function blob()
    {
        return $this->getBlob($this->type, $this->image);
    }

    public function tmp()
    {
        return $this->getTmp($this->type, $this->image);
    }

    public function setData()
    {
        return $this->setImage($this->source);
    }

    public function thumb($newWidth, $newHeight, $bestFit = false)
    {
        if ($bestFit) {
            $aspectRatio = $this->width / $this->height;
            if ($newWidth / $newHeight > $aspectRatio) {
                $newWidth = round($newHeight * $aspectRatio);
            } else {
                $newHeight = round($newWidth / $aspectRatio);
            }
        }
        $resizedImage = $this->createCanvas($newWidth, $newHeight, [255, 255, 255, 127]);
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
                $newWidth = round($newHeight * $aspectRatio);
            } else {
                $newHeight = round($newWidth / $aspectRatio);
            }
        }

        $resizedImage = $this->createCanvas($newWidth, $newHeight, [255, 255, 255, 127]);
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
        $circleImage = $this->createCanvas($size, $size, [255, 255, 255, 127]);

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

    public function crop($x = 0, $y = 0, $width = 0, $height = 0)
    {
        $this->image = $this->cropHandle($this->image, $x, $y, $width, $height);
        $this->width = $width;
        $this->height = $height;
        return $this;
    }
}