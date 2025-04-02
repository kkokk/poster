<?php
/**
 * User: lang
 * Date: 2024/11/28
 * Time: 9:26
 */

namespace Kkokk\Poster\Image\Graphics;

use Kkokk\Poster\Image\Graphics\Interfaces\ImageGraphicsEngineInterface;
use Kkokk\Poster\Image\Imagick\Canvas;
use Kkokk\Poster\Image\Imagick\Image;
use Kkokk\Poster\Image\Traits\ImagickTrait;

/**
 * User: lang
 * @extends ImageGraphicsEngine<\Imagick>
 * @package Kkokk\Poster\Image\Graphics
 * @class   ImagickImageGraphicsEngine
 */
class ImagickImageGraphicsEngine extends ImageGraphicsEngine implements ImageGraphicsEngineInterface
{
    use ImagickTrait;

    /** @var int[] 默认 x y 分辨率 默认是 [72, 72] */
    protected $dpi = [];

    public function config($configs = [])
    {
        if (isset($configs['dpi']) && !empty($configs['dpi'])) {
            $this->dpi = is_numeric($configs['dpi']) ? [$configs['dpi'], $configs['dpi']] : $configs['dpi'];
        }
        return parent::config($configs);
    }

    /**
     * 生成图片并保存
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:43
     * @param $path
     * @return string|string[]|null
     * @throws \ImagickException
     * @throws \Kkokk\Poster\Exception\PosterException
     */
    public function getData($path = '')
    {
        if ($path) {
            $this->setFilePath($path);
        }
        $this->setDPI();
        return $this->returnImage($this->getType());
    }

    /**
     * 获取图片流
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:43
     * @param $type
     * @return string|string[]|null
     * @throws \ImagickException
     * @throws \Kkokk\Poster\Exception\PosterException
     */
    public function getStream($type = '')
    {
        $this->setDPI();
        return $this->returnImage($type ?: $this->getType(), false);
    }

    /**
     * 获取图片 base64 编码
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:43
     * @return string
     * @throws \ImagickException
     * @throws \Kkokk\Poster\Exception\PosterException
     */
    public function getBaseData()
    {
        $this->setDPI();
        return base64_data($this->image->getImageBlob(), $this->getType());
    }

    /**
     * 获取图片二进制流
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:43
     * @return mixed
     * @throws \Kkokk\Poster\Exception\PosterException
     */
    public function blob()
    {
        $this->setDPI();
        return $this->getBlob($this->image);
    }

    /**
     * 临时文件
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:42
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
     * Time: 9:42
     * @return bool
     * @throws \Kkokk\Poster\Exception\PosterException
     */
    public function setData()
    {
        $this->setDPI();
        return $this->setImage($this->source);
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
        if ($newWidth == 0 || $newHeight == 0) {
            return $this;
        }
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
        if ($newWidth == 0 || $newHeight == 0) {
            return $this;
        }
        $this->image->scaleImage($newWidth, $newHeight, $bestFit);
        $this->width = $newWidth;
        $this->height = $newHeight;
        return $this;
    }

    /**
     * 圆形剪裁
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:42
     * @return $this
     * @throws \ImagickDrawException
     * @throws \ImagickException
     * @throws \Kkokk\Poster\Exception\PosterException
     */
    public function circle()
    {
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

        return $this;
    }

    /**
     * 裁剪图片
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:42
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
        $this->cropHandle($this->image, $x, $y, $width, $height);
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

    /**
     * 设置透明度
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:42
     * @param $transparency
     * @return $this
     */
    public function transparent($transparency)
    {
        $this->setImageAlpha($this->image, $transparency);
        return $this;
    }

    /**
     * 设置圆角
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:42
     * @param $radius
     * @return $this
     * @throws \ImagickDrawException
     * @throws \ImagickException
     * @throws \Kkokk\Poster\Exception\PosterException
     */
    public function borderRadius($radius = 0)
    {
        $this->setPixelRadius($this->image, $this->width, $this->height, $radius);
        return $this;
    }

    /**
     * 应用蒙版
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:41
     * @param $mask
     * @return $this
     * @throws \ImagickException
     */
    public function applyMask($mask)
    {
        $width = $this->width;
        $height = $this->height;
        // 加载蒙版图片并转换为灰度
        $maskImage = (new Image($mask))->scale($width, $height);
        // 确保蒙版是灰度图
        if ($maskImage->getImage()->getImageColorspace() != ($this->image)::COLORSPACE_GRAY) {
            $maskImage->getImage()->transformImageColorspace(($this->image)::COLORSPACE_GRAY);
        }

        // 获取源图片的alpha通道
        $imageClone = clone $this->image;
        $imageClone->separateImageChannel(($this->image)::CHANNEL_ALPHA);

        // 将蒙版和源图片的alpha通道相乘
        $maskImage->getImage()->compositeImage($imageClone, ($this->image)::COMPOSITE_MULTIPLY, 0, 0);

        $this->image->compositeImage($maskImage->getImage(), ($this->image)::COMPOSITE_COPYOPACITY, 0, 0);

        // 释放资源
        $this->destroyImage($maskImage->getImage());
        $this->destroyImage($imageClone);
        return $this;
    }

    public function cutout($x1, $y1, $width, $height, \Closure $crossCondition = null)
    {
        $croppedImage = new Canvas($width, $height);
        $draw = $this->createImagickDraw();
        for ($i = 0; $i < $this->getWidth(); $i++) {
            for ($j = 0; $j < $this->getHeight(); $j++) {
                $shouldProcessPixel = is_null($crossCondition) || $crossCondition([$i, $j]);
                if ($shouldProcessPixel) {
                    // 获取颜色
                    $pixel = $this->image->getImagePixelColor($i, $j);
                    $draw->setFillColor($pixel);
                    $draw->point($i - $x1, $j - $y1);
                }
            }
        }
        $croppedImage->getImage()->drawImage($draw);
        $this->destroyImage($draw);
        return $croppedImage;
    }

    public function drawImagePolygon($points, $color, $thickness = 1)
    {
        $draw = $this->createImagickDraw();
        // 颜色
        $draw->setStrokeColor($this->createColor($color));
        // 线条宽度
        $draw->setStrokeWidth($thickness);
        // 不填充
        $draw->setFillColor($this->createColor('none'));
        $imagickPoints = $this->pointsToImagick($points);
        // 画线
        $draw->polyline($imagickPoints);
        // 闭合多边形
        if ($imagickPoints[0] !== end($imagickPoints)) {
            $draw->line($imagickPoints[count($imagickPoints) - 1]['x'], $imagickPoints[count($imagickPoints) - 1]['y'],
                $imagickPoints[0]['x'], $imagickPoints[0]['y']);
        }
        // 应用到图像
        $this->image->drawImage($draw);
        $this->destroyImage($draw);
        return $this;
    }

    public function drawImageFilledPolygon($points, $color)
    {
        $imagickPoints = $this->pointsToImagick($points);
        $draw = $this->createImagickDraw();
        // 设置填充颜色
        $draw->setFillColor($this->createColor($color));
        // 绘制填充多边形
        $draw->polygon($imagickPoints);
        // 将绘制内容应用到图像
        $this->image->drawImage($draw);
        $this->destroyImage($draw);
        return $this;
    }

    /**
     * 转换 GD 格式数组为 Imagick 格式
     * User: lang
     * Date: 2025/4/2
     * Time: 10:43
     * @param $points
     * @return array
     */
    protected function pointsToImagick($points)
    {
        $imagickPoints = [];
        for ($i = 0; $i < count($points); $i += 2) {
            $imagickPoints[] = ['x' => $points[$i], 'y' => $points[$i + 1]];
        }
        return $imagickPoints;
    }

    public function __destruct()
    {
        $this->destroyImage();
    }

    public function __clone()
    {
        $this->image = clone $this->image;
    }
}