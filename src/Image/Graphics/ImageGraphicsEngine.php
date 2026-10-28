<?php
/**
 * User: lang
 * Date: 2024/11/28
 * Time: 9:26
 */

namespace Kkokk\Poster\Image\Graphics;

use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Image\Enums\ImageType;

/**
 * @template ImageInstance
 */
class ImageGraphicsEngine
{
    /**
     * 画布
     * @var ImageInstance
     */
    protected $image;

    /** 源文件 */
    protected $source;

    /** @var int 画布尺寸宽 */
    protected $width;

    /** @var int 画布尺寸高 */
    protected $height;

    /** @var string 存储路径 */
    protected $path;

    /** @var string 默认目录 */
    protected $pathname = 'poster';

    /** @var string 文件名 */
    protected $filename;

    /** @var string 图片类型 */
    protected $type = '';

    /** @var string 默认图片类型 */
    protected $defaultType = 'png';

    /**
     * @var int jpeg | webp 图片质量 0 - 100 默认75
     */
    protected $quality = 75;

    protected $remoteImageLoader;

    /**
     * 显式图片适配；contain不补边，cover的focus表示剩余裁剪距离比例。
     * @param int $width
     * @param int $height
     * @param string $mode contain|cover|fill
     * @param int|float $focusX 0左至1右
     * @param int|float $focusY 0上至1下
     * @return $this
     * @throws PosterException
     */
    public function fit($width, $height, $mode = 'contain', $focusX = 0.5, $focusY = 0.5)
    {
        if (!is_int($width) || !is_int($height) || $width <= 0 || $height <= 0
            || !in_array($mode, ['contain', 'cover', 'fill'], true)) {
            throw new PosterException('Invalid image fitting dimensions or mode');
        }
        foreach ([$focusX, $focusY] as $focus) {
            if ((!is_int($focus) && !is_float($focus)) || !is_finite((float) $focus) || $focus < 0 || $focus > 1) {
                throw new PosterException('Image fitting focus must be between 0 and 1');
            }
        }
        if ($this->width <= 0 || $this->height <= 0) throw new PosterException('Image fitting requires a non-empty image');
        $scaledWidth = $width; $scaledHeight = $height;
        if ($mode !== 'fill') {
            $ratio = $mode === 'cover' ? max($width / $this->width, $height / $this->height)
                : min($width / $this->width, $height / $this->height);
            // cover向上取整以免留下空边，contain四舍五入并保证至少一像素。
            $scaledWidth = max(1, $mode === 'cover' ? ceil($this->width * $ratio) : round($this->width * $ratio));
            $scaledHeight = max(1, $mode === 'cover' ? ceil($this->height * $ratio) : round($this->height * $ratio));
        }
        if (!is_finite((float) $scaledWidth) || !is_finite((float) $scaledHeight)
            || $scaledWidth >= PHP_INT_MAX || $scaledHeight >= PHP_INT_MAX) {
            throw new PosterException('Image fitting dimensions exceed integer range');
        }
        $scaledWidth = (int) $scaledWidth; $scaledHeight = (int) $scaledHeight;
        $outputWidth = $mode === 'cover' ? $width : $scaledWidth;
        $outputHeight = $mode === 'cover' ? $height : $scaledHeight;
        $x = (int) round(($scaledWidth - $outputWidth) * $focusX);
        $y = (int) round(($scaledHeight - $outputHeight) * $focusY);
        return $this->fitRaster($scaledWidth, $scaledHeight, $x, $y, $outputWidth, $outputHeight);
    }

    /** 在 readImage 前注入实例策略；不影响其他图片对象。 */
    public function setRemoteImageLoader(\Kkokk\Poster\Image\Resources\RemoteImageLoader $loader)
    {
        $this->remoteImageLoader = $loader;
        return $this;
    }

    protected function getRemoteImageLoader()
    {
        return $this->remoteImageLoader ?: new \Kkokk\Poster\Image\Resources\RemoteImageLoader();
    }
    public function config($configs = [])
    {
        !empty($configs['path']) && $this->setFilePath($configs['path']);
        !empty($configs['type']) && $this->setType($configs['type']);
        !empty($configs['quality']) && $this->setQuantity($configs['quality']);
        return $this;
    }

    /**
     * 设置文件路径
     * User: lang
     * Date: 2024/11/28
     * Time: 9:56
     * @param $path
     * @return void
     * @throws \Kkokk\Poster\Exception\PosterException
     */
    protected function setFilePath($path)
    {
        $path = is_array($path) ? $path : [$path];
        $pathFileName = isset($path[0]) ? $path[0] : '';
        $pathFileName = str_replace(['\\', '/', DIRECTORY_SEPARATOR], DIRECTORY_SEPARATOR, $pathFileName);
        $fileName = $pathFileName ?: time();
        if (strripos($pathFileName, DIRECTORY_SEPARATOR) !== false) {
            $this->setPathName($pathFileName);
            $fileName = substr($pathFileName, strripos($pathFileName, DIRECTORY_SEPARATOR) + 1);
        }
        $this->setFileName($fileName);
        $this->setPath($pathFileName);
    }

    /**
     * setFileName 设置文件名
     * User: lang
     * Date: 2024/11/28
     * Time: 9:55
     * @param $fileName
     * @return void
     * @throws \Kkokk\Poster\Exception\PosterException
     */
    protected function setFileName($fileName)
    {
        $this->filename = $fileName;
        if (strripos($this->filename, '.') !== false) {
            $this->setType(substr($this->filename, strripos($this->filename, '.') + 1));
            if (!in_array($this->getType(), ImageType::types())) {
                throw new PosterException('The file naming format is incorrect');
            }
        }
    }

    /**
     * setPathName 设置目录名
     * User: lang
     * Date: 2024/11/28
     * Time: 9:55
     * @param $pathFileName
     * @return void
     */
    protected function setPathName($pathFileName)
    {
        $this->pathname = substr($pathFileName, 0, strripos($pathFileName, DIRECTORY_SEPARATOR));
    }

    /**
     * setPath 设置文件位置
     * User: lang
     * Date: 2024/11/28
     * Time: 9:55
     * @param $pathFileName
     * @return void
     */
    protected function setPath($pathFileName)
    {
        $this->path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR;
        // 绝对路径 or 相对路径
        $absolute = is_absolute($pathFileName);
        if ($absolute) {
            $this->path = '';
        } elseif (get_document_root()) {
            $this->path = get_document_root();
        }
    }

    public function setType($type, $force = false)
    {
        if (empty($this->type) || $force) {
            $this->type = $type;
        }
    }

    public function setQuantity($quality)
    {
        $this->quality = $quality;
    }

    /**
     * 获取图片资源
     * User: lang
     * Date: 2025/4/1
     * Email: 732853989@qq.com
     * Time: 9:35
     * @return ImageInstance
     */
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

    public function getType()
    {
        return $this->type ?: $this->defaultType;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getPathName()
    {
        return $this->pathname;
    }

    public function getFileName()
    {
        return $this->filename;
    }

    public function getQuantity()
    {
        return $this->quality;
    }
}
