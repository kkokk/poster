<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/22
 * Time: 14:27
 */

namespace Kkokk\Poster\Image;

use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Image\Drivers\GdDriver;
use Kkokk\Poster\Image\Drivers\ImagickDriver;

class ExtensionFactory
{

    protected $path;

    public function make($name, $path = null)
    {
        $this->path = $path;
        return $this->createExtension($name);
    }

    protected function createDriver($name)
    {
        switch ($name) {
            case 'gd':
                return new GdDriver();
            case 'imagick':
                return new ImagickDriver();
        }

        throw new PosterException("Unsupported driver [{$name}].");
    }

    protected function createExtension($name)
    {
        switch ($name) {
            case 'gd':
                return new GdExtension($this->createDriver($name), $this->path);
            case 'imagick':
                return new ImagickExtension($this->createDriver($name), $this->path);
        }

        throw new PosterException("Unsupported extension [{$name}].");
    }
}