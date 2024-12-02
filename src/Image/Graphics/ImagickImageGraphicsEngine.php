<?php
/**
 * User: lang
 * Date: 2024/11/28
 * Time: 9:26
 */

namespace Kkokk\Poster\Image\Graphics;

use Kkokk\Poster\Image\Graphics\Interfaces\ImageGraphicsEngineInterface;
use Kkokk\Poster\Image\Traits\ImagickTrait;

class ImagickImageGraphicsEngine extends ImageGraphicsEngine implements ImageGraphicsEngineInterface
{
    use ImagickTrait;

    /** @var \Imagick */
    protected $image;

    /** @var int[] 默认 x y 分辨率 默认是 [72, 72] */
    protected $dpi = [];

    public function config($configs = [])
    {
        if (isset($configs['dpi']) && !empty($configs['dpi'])) {
            $this->dpi = is_numeric($configs['dpi']) ? [$configs['dpi'], $configs['dpi']] : $configs['dpi'];
        }
        return parent::config($configs);
    }

    public function getData($path = '')
    {
        if ($path) {
            $this->setFilePath($path);
        }
        $this->setDPI();
        return $this->returnImage($this->type);
    }

    public function getStream()
    {
        $this->setDPI();
        return $this->returnImage($this->type, false);
    }

    public function getBaseData()
    {
        $this->setDPI();
        return base64_data($this->image->getImageBlob(), $this->type);
    }

    public function blob()
    {
        $this->setDPI();
        return $this->getBlob($this->image);
    }

    public function tmp()
    {
        return $this->getTmp($this->type, $this->image);
    }

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

    public function crop($x = 0, $y = 0, $width = 0, $height = 0)
    {
        $x = calc_dst_x($x, $this->width, $width);
        $y = calc_dst_Y($y, $this->height, $height);
        $this->cropHandle($this->image, $x, $y, $width, $height);
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

    public function __destruct()
    {
        $this->destroyImage();
    }
}