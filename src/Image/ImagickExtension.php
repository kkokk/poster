<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/23
 * Time: 17:44
 */

namespace Kkokk\Poster\Image;

use Kkokk\Poster\Image\Queries\ImagickQuery;

class ImagickExtension extends Extension
{
    public function getQueryInstance()
    {
        return new ImagickQuery;
    }
}