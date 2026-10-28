<?php
/**
 * User: lang
 * Date: 2024/11/28
 * Time: 9:26
 */

namespace Kkokk\Poster\Image\Graphics;

use Kkokk\Poster\Image\Gd\Canvas;
use Kkokk\Poster\Image\Gd\Image;
use Kkokk\Poster\Image\Graphics\Interfaces\ImageGraphicsEngineInterface;
use Kkokk\Poster\Image\Traits\GdTrait;

/**
 * User: lang
 * @extends ImageGraphicsEngine<Resource>
 * @package Kkokk\Poster\Image\Graphics
 * @class   GdImageGraphicsEngine
 */
class GdImageGraphicsEngine extends ImageGraphicsEngine implements ImageGraphicsEngineInterface
{
    use GdTrait;

    protected function fitRaster($scaledWidth, $scaledHeight, $x, $y, $width, $height)
    {
        $scaled = $this->createCanvas($scaledWidth, $scaledHeight);
        // 直接写入alpha，避免透明像素与默认背景混合。
        imagealphablending($scaled, false);
        imagesavealpha($scaled, true);
        imagecopyresampled($scaled, $this->image, 0, 0, 0, 0, $scaledWidth, $scaledHeight, $this->width, $this->height);
        $output = $scaled;
        if ($scaledWidth !== $width || $scaledHeight !== $height) {
            $output = imagecrop($scaled, ['x' => $x, 'y' => $y, 'width' => $width, 'height' => $height]);
            if ($output === false) throw new \Kkokk\Poster\Exception\PosterException('Image fitting crop failed');
            imagesavealpha($output, true);
        }
        $this->destroyImage();
        $this->image = $output;
        $this->width = $width; $this->height = $height;
        if ($output !== $scaled) imagedestroy($scaled);
        return $this;
    }

    /**
     * 生成图片并保存
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:50
     * @param $path
     * @return false|string|string[]|null
     * @throws \Kkokk\Poster\Exception\PosterException
     */
    public function getData($path = '')
    {
        if ($path) {
            $this->setFilePath($path);
        }
        return $this->returnImage($this->getType());
    }

    /**
     * 获取图片流
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:50
     * @param $type
     * @return false|string|string[]|null
     * @throws \Kkokk\Poster\Exception\PosterException
     */
    public function getStream($type = '')
    {
        return $this->returnImage($type ?: $this->getType(), false);
    }

    /**
     * 获取图片 base64 编码
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Email: 732853989@qq.com
     * Time: 9:50
     * @return string
     * @throws \Kkokk\Poster\Exception\PosterException
     */
    public function getBaseData()
    {
        return base64_data($this->image, $this->getType());
    }

    /**
     * 获取图片二进制流
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:51
     * @return false|string
     */
    public function blob()
    {
        return $this->getBlob($this->getType(), $this->image);
    }

    /**
     * 临时文件
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:51
     * @return false|string
     */
    public function tmp()
    {
        return $this->getTmp($this->getType(), $this->image);
    }

    /**
     * 设置图片
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:51
     * @return string
     * @throws \Kkokk\Poster\Exception\PosterException
     */
    public function setData()
    {
        return $this->setImage($this->source);
    }

    /**
     * 缩放
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:47
     * @param $newWidth
     * @param $newHeight
     * @param $bestFit
     * @return $this
     */
    public function thumb($newWidth, $newHeight, $bestFit = false)
    {
        if ($newWidth == 0 || $newHeight == 0) {
            return $this;
        }
        if ($bestFit) {
            $aspectRatio = $this->width / $this->height;
            if ($newWidth / $newHeight > $aspectRatio) {
                $newWidth = round($newHeight * $aspectRatio);
            } else {
                $newHeight = round($newWidth / $aspectRatio);
            }
        }
        $resizedImage = $this->createCanvas($newWidth, $newHeight);
        imagecopyresized($resizedImage, $this->image, 0, 0, 0, 0, intval($newWidth), intval($newHeight),
            intval($this->width), intval($this->height));
        $this->destroyImage();
        $this->image = $resizedImage;
        $this->width = $newWidth;
        $this->height = $newHeight;
        return $this;
    }

    /**
     * 缩放保持较高质量
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:47
     * @param $newWidth
     * @param $newHeight
     * @param $bestFit
     * @return $this
     */
    public function scale($newWidth, $newHeight, $bestFit = false)
    {
        if ($newWidth == 0 || $newHeight == 0) {
            return $this;
        }
        if ($bestFit) {
            $aspectRatio = $this->width / $this->height;
            if ($newWidth / $newHeight > $aspectRatio) {
                $newWidth = round($newHeight * $aspectRatio);
            } else {
                $newHeight = round($newWidth / $aspectRatio);
            }
        }

        $resizedImage = $this->createCanvas($newWidth, $newHeight);
        imagecopyresampled($resizedImage, $this->image, 0, 0, 0, 0, intval($newWidth), intval($newHeight),
            intval($this->width), intval($this->height));
        $this->destroyImage();
        $this->image = $resizedImage;
        $this->width = $newWidth;
        $this->height = $newHeight;
        return $this;
    }

    /**
     * 圆形裁剪
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:47
     * @return $this
     */
    public function circle()
    {
        $size = min($this->width, $this->height);
        $circleImage = $this->createCanvas($size, $size);
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
        $this->destroyImage();
        $this->image = $circleImage;
        $this->width = $size;
        $this->height = $size;
        return $this;
    }

    /**
     * 裁剪图片
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:47
     * @param $x
     * @param $y
     * @param $width
     * @param $height
     * @return $this
     */
    public function crop($x = 0, $y = 0, $width = 0, $height = 0)
    {
        $x = calc_dst_x($x, $this->width, $width);
        $y = calc_dst_Y($y, $this->height, $height);
        $cropImage = $this->cropHandle($this->image, $x, $y, $width, $height);
        $this->destroyImage();
        $this->image = $cropImage;
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

    /**
     * 设置透明度
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:48
     * @param $transparency
     * @return $this
     */
    public function transparent($transparency)
    {
        $mask = $this->setImageAlpha($this->image, $this->width, $this->height, $transparency);
        $this->destroyImage();
        $this->image = $mask;
        return $this;
    }

    /**
     * 设置圆角
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:48
     * @param $radius
     * @return $this
     */
    public function borderRadius($radius = 0)
    {
        $radiusImage = $this->setPixelRadius($this->image, $this->width, $this->height, $radius);
        $this->destroyImage();
        $this->image = $radiusImage;
        return $this;
    }

    /**
     * 应用蒙版
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:48
     * @param $mask
     * @return $this
     */
    public function applyMask($mask)
    {
        imagepalettetotruecolor($this->image);
        imagealphablending($this->image, false);
        imagesavealpha($this->image, true);
        $width = $this->width;
        $height = $this->height;
        $maskImage = (new Image($mask))->scale($width, $height);
        // 遍历每个像素，调整 Alpha 透明度
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $maskColor = imagecolorat($maskImage->getImage(), $x, $y) & 0xFF; // 灰度遮罩，保留历史蓝通道读取规则
                $imageColor = imagecolorat($this->image, $x, $y);
                // 提取 RGBA 组件
                $r = ($imageColor >> 16) & 0xFF;
                $g = ($imageColor >> 8) & 0xFF;
                $b = $imageColor & 0xFF;
                // 遮罩只减少不透明度；不能覆盖源alpha使透明像素重新可见。
                $sourceAlpha = ($imageColor >> 24) & 0x7F;
                $alpha = (int) round(127 - ($maskColor / 255) * (127 - $sourceAlpha));
                $newColor = imagecolorallocatealpha($this->image, $r, $g, $b, $alpha);
                imagesetpixel($this->image, $x, $y, $newColor);
            }
        }
        $this->destroyImage($maskImage->getImage());
        return $this;
    }

    /**
     * 抠图
     * author: lang
     * email: 732853989@qq.com
     * date: 2025/4/6
     * time: 08:13
     * @param               $x1
     * @param               $y1
     * @param               $width
     * @param               $height
     * @param \Closure|null $crossCondition
     * @return \Kkokk\Poster\Image\Gd\Canvas
     */
    public function cutout($x1, $y1, $width, $height, \Closure $crossCondition = null)
    {
        $croppedImage = new Canvas($width, $height, []);
        imagealphablending($croppedImage->getImage(), false);
        for ($i = 0; $i < $this->getWidth(); $i++) {
            for ($j = 0; $j < $this->getHeight(); $j++) {
                $shouldProcessPixel = is_null($crossCondition) || $crossCondition([$i, $j]);
                if ($shouldProcessPixel) {
                    // 获取颜色
                    $rgba = imagecolorsforindex($this->image, imagecolorat($this->image, $i, $j));
                    $rgbColor = imagecolorallocatealpha($croppedImage->getImage(), $rgba['red'], $rgba['green'], $rgba['blue'], $rgba['alpha']);
                    imagesetpixel($croppedImage->getImage(), $i - $x1, $j - $y1, $rgbColor); // 抠图
                }
            }
        }
        return $croppedImage;
    }

    /**
     * 画多边形
     * author: lang
     * email: 732853989@qq.com
     * date: 2025/4/6
     * time: 08:14
     * @param $points
     * @param $color
     * @param $thickness
     * @return $this
     */
    public function drawImagePolygon($points, $color, $thickness = 1)
    {
        $borderColor = $this->createColor($this->image, $color);
        imagesetthickness($this->image, intval($thickness)); // GD 使用整数线宽，显式截断避免精度丢失警告
        if (version_compare(PHP_VERSION, '8.1.0') === -1) {
            imagepolygon($this->image, $points, count($points) / 2, $borderColor);
        } else {
            imagepolygon($this->image, $points, $borderColor);
        }
        return $this;
    }

    /**
     * 填充多边形
     * author: lang
     * email: 732853989@qq.com
     * date: 2025/4/6
     * time: 08:14
     * @param $points
     * @param $color
     * @return $this
     */
    public function drawImageFilledPolygon($points, $color)
    {
        $bgColor = $this->createColor($this->image, $color);
        if (version_compare(PHP_VERSION, '8.1.0') === -1) {
            imagefilledpolygon($this->image, $points, count($points) / 2, $bgColor);
        } else {
            imagefilledpolygon($this->image, $points, $bgColor);
        }
        return $this;
    }

    /**
     * 显式复制像素及透明度；原有对象传入和 clone 行为保持兼容。
     * @return static
     */
    public function copy()
    {
        if (empty($this->image)) {
            throw new \Kkokk\Poster\Exception\PosterException('Cannot copy an empty image');
        }
        $image = imagecreatetruecolor($this->width, $this->height);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagecopy($image, $this->image, 0, 0, 0, 0, $this->width, $this->height);
        $copy = clone $this;
        $copy->image = $image;
        return $copy;
    }
    public function __destruct()
    {
        $this->destroyImage();
    }
}
