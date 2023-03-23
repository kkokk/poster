<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/22
 * Time: 14:27
 */

namespace Kkokk\Poster\Image;


use Kkokk\Poster\Exception\PosterException;

class ExtensionFactory
{

    public function make($name){
        return $this->createExtension($name);
    }

    protected function createExtension($name)
    {
        switch ($name) {
            case 'gd':
                return new GdExtension;
            case 'imagick':
                return new ImagickExtension;
        }

        throw new PosterException("Unsupported extension [{$name}].");
    }
}