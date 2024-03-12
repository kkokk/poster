<?php
/**
 * User: lang
 * Date: 2023/8/9
 * Time: 18:09
 */

namespace Kkokk\Poster\Html\Drivers;

use Kkokk\Poster\Exception\PosterException;

/**
 * User: lang
 * 注意 使用的是 -webkit 语法
 * @package Kkokk\Poster\Html\Drivers
 * @class   WkDriver
 * @author  73285 2023-08-10
 */
class WkDriver extends Driver implements DriverInterface
{
    /**
     *  文件类型，常规图片png、jpg等，和pdf
     * @var string
     */
    private $type = 'png';

    private $driver = "wkhtmltoimage";

    private $size = "";

    private $crop = "";

    private $quality = '--quality 100';

    private $transparent = '';

    private $command = '';

    private $tmpHtmlPath;

    private $tmp = false;

    public function load($html)
    {
        $this->html = $html;
    }

    public function size($width = 0, $height = 0)
    {
        if ($width) {
            $this->size .= "--width " . $width;
        }
        if ($height) {
            $this->size .= " --height " . $height;
        }
    }

    public function crop($crop_w = 0, $crop_h = 0, $crop_x = 0, $crop_y = 0)
    {
        if ($crop_w) {
            $this->crop .= " --crop-w " . $crop_w;
        }
        if ($crop_h) {
            $this->crop .= " --crop-h " . $crop_h;
        }
        if ($crop_x) {
            $this->crop .= " --crop-x " . $crop_x;
        }
        if ($crop_y) {
            $this->crop .= " --crop-y " . $crop_y;
        }
    }

    public function quality($quality)
    {
        $quality = max($quality, 0);
        $quality = min($quality, 100);
        $this->quality = '--quality ' . $quality;
    }

    public function setType($type)
    {
        $this->type = strtolower($type);
        if ($this->type == 'pdf') {
            $this->driver = 'wkhtmltopdf';
        }
    }

    public function output($path, $type = '')
    {
        $this->output = $path;
        if ($type) {
            $this->setType($type);
        }
    }

    public function setTransparent($transparent = true)
    {
        $this->transparent = $transparent ? '--transparent' : '';
    }

    /**
     * 设置原生命令
     * User: lang
     * Date: 2023/8/10
     * Time: 16:35
     * @param $command
     * @return void
     */
    public function setCommand($command)
    {
        $this->command .= ' ' . $command;
    }

    private function tmp()
    {
        $this->output = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . uniqid('html') . '.png';
        $this->tmp = true;
    }

    public function render($query)
    {
        foreach ($query as $v) {
            switch ($v['type']) {
                case "load":
                    $this->load(...$v['params']);
                    break;
                case "crop":
                    $this->crop(...$v['params']);
                    break;
                case "quality":
                    $this->quality(...$v['params']);
                    break;
                case "type":
                    $this->setType(...$v['params']);
                    break;
                case "tmp":
                    $this->tmp();
                    break;
                case "output":
                    $this->output(...$v['params']);
                    break;
                case "size":
                    $this->size(...$v['params']);
                    break;
                case "transparent":
                    $this->setTransparent(...$v['params']);
                    break;
                case "command":
                    $this->setCommand(...$v['params']);
                    break;
            }
        }

        $check = exec($this->driver . ' --version');
        if (empty($check)) {
            throw new PosterException('Please install ' . $this->driver);
        }


        if (preg_match('/<[^>]*>/', $this->html)) {
            $this->tmpHtmlPath = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . uniqid('html') . '.html';
            file_put_contents($this->tmpHtmlPath, $this->html);
            $this->html = $this->tmpHtmlPath;
        }

        if ($this->type == 'pdf') {

            if (empty($this->output)) {
                throw new PosterException('Save path cannot be empty');
            }
            $this->quality = '';

        } else {
            if (empty($this->output)) {
                $this->tmp();
            }
        }

        $command = sprintf("%s %s %s %s %s %s %s %s", $this->driver, $this->command, $this->transparent, $this->size,
            $this->crop, $this->quality, $this->html, $this->output);

        exec($command);

        return $this;
    }

    public function getImageBlob()
    {
        return file_get_contents($this->output);
    }

    public function getFilePath()
    {
        return $this->output;
    }

    function __destruct()
    {
        $this->tmp && @unlink($this->output);
        $this->tmpHtmlPath && @unlink($this->tmpHtmlPath);
    }
}

