<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/21
 * Time: 18:05
 */

namespace Kkokk\Poster\Base;


use Kkokk\Poster\Exception\PosterException;

class PosterBase
{
    /**
     * 设置文件路径
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/12/12
     * Time: 9:55
     * @param $path
     * @throws PosterException
     */
    public function setFilePath($path)
    {

        $path = is_array($path) ? $path : [$path];
        if (PHP_VERSION <= 7) {
            $pathFileName = isset($path[0]) ? $path[0] : '';
        } else {
            $pathFileName = $path[0] ?? '';
        }
        $pathFileName = str_replace(['\\', '/'], '/', $pathFileName);

        $fileName = $pathFileName ?: time();

        if (strripos($pathFileName, '/') !== false) {
            $this->setPathName($pathFileName);
            $fileName = substr($pathFileName, strripos($pathFileName, '/') + 1);
        }

        $this->setFileName($fileName);
        $this->setPath($pathFileName);
    }

    /**
     * setFileName 设置文件名
     * @Author lang
     * @Date   2022-03-10T15:42:06+0800
     * @param  [type]                   $fileName [description]
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
     * @Author lang
     * @Date   2022-03-10T15:42:19+0800
     * @param  [type]                   $fileName [description]
     */
    public function setPathName($pathFileName)
    {
        $this->pathname = substr($pathFileName, 0, strripos($pathFileName, '/'));
    }

    /**
     * setPath 设置文件位置
     * @Author lang
     * @Date   2022-03-10T15:42:38+0800
     * @param  [type]                   $fileName [description]
     */
    public function setPath($pathFileName)
    {
        // 绝对路径 or 相对路径
        $absolute = $this->isAbsolute($pathFileName);
        $this->path = $this->getDocumentRoot();
        $this->path = $absolute ? '' : ($this->path ? $this->path . '/' : __DIR__ . '/../../tests/');
    }

    /**
     * 获取项目根目录
     * @Author lang
     * @Date   2022-03-10T15:42:38+0800
     */
    public function getDocumentRoot()
    {
        return iconv('UTF-8', 'GBK', $_SERVER['DOCUMENT_ROOT']);
    }

    /**
     * 判断是否是绝对路径
     * Author: lang
     * Email: 732853989@qq.com
     * Date: 2022/12/12
     * Time: 9:54
     * @param $pathFileName
     * @return bool
     */
    public function isAbsolute($pathFileName)
    {

        // 区分WIN系统绝对路径、暂时只区分linux win mac
        switch (PHP_OS) {
            case 'Darwin':
                $absolute = stripos($pathFileName, '/') === 0 ?: false;
                break;
            case 'linux':
            default:
                if (stripos(PHP_OS, 'WIN') !== false) {
                    $absolute = substr($pathFileName, 1, 1) === ':' ?: false;
                } else {
                    $absolute = stripos($pathFileName, '/') === 0 ?: false;
                }
                break;
        }

        return $absolute;
    }

    /**
     * 检查文件是否存在并创建
     * @Author lang
     * @Date   2020-08-14T15:32:04+0800
     * @param  [type]
     * @return [type]
     */
    public function dirExists($pathname)
    {

        if (!file_exists($this->path . $pathname)) {
            return mkdir($this->path . $pathname, 0777, true);
        }
    }
}