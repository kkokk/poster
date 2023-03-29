<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/23
 * Time: 17:43
 */

namespace Kkokk\Poster\Image;

use Kkokk\Poster\Image\Queries\GdQuery;

class GdExtension extends Extension
{
    public function getQueryInstance()
    {
        return new GdQuery;
    }
}