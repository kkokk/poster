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
    public function setFilePath($path)
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
    public function setFileName($fileName)
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
    public function setPathName($pathFileName)
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
    public function setPath($pathFileName)
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