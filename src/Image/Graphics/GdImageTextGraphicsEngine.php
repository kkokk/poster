<?php
/**
 * @author  : lang
 * @email   : 732853989@qq.com
 * @date    : 2024/12/3
 * @time    : 21:48
 * @fileName: GdImageTextGraphicsEngine.php
 */

namespace Kkokk\Poster\Image\Graphics;

use Kkokk\Poster\Image\Graphics\Traits\GdTextTrait;
use Kkokk\Poster\Image\Traits\GdTrait;

class GdImageTextGraphicsEngine extends ImageTextGraphicsEngine
{
    use GdTrait, GdTextTrait;
}