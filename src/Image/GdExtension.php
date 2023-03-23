<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/23
 * Time: 17:43
 */

namespace Kkokk\Poster\Image;

use Kkokk\Poster\Image\Drivers\GdDriver;

class GdExtension extends Extension
{
    protected function getDriver()
    {
        return new GdDriver;
    }
}