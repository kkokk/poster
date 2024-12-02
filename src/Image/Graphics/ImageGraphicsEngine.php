<?php
/**
 * User: lang
 * Date: 2024/11/28
 * Time: 9:26
 */

namespace Kkokk\Poster\Image\Graphics;

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'PHPQrcode' . DIRECTORY_SEPARATOR . 'phpqrcode.php');

use Kkokk\Poster\Exception\PosterException;

class ImageGraphicsEngine
{
    /** 画布 */
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
    protected $type = 'png';

    public function config($configs = [])
    {
        isset($configs['path']) && !empty($configs['path']) && $this->setFilePath($configs['path']);
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
        $pathFileName = str_replace(['\\', DIRECTORY_SEPARATOR], DIRECTORY_SEPARATOR, $pathFileName);

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
            $this->type = substr($this->filename, strripos($this->filename, '.') + 1);
            if (!in_array($this->type, ['jpeg', 'jpg', 'png', 'gif', 'wbmp'])) {
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
        // 绝对路径 or 相对路径
        $absolute = is_absolute($pathFileName);
        $this->path = get_document_root();
        $this->path = $absolute ? '' : ($this->path ?: __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR);
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

    public function __clone()
    {
        $this->image = clone $this->image;
    }
}