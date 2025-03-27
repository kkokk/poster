<?php
/**
 * @author  : lang
 * @email   : 732853989@qq.com
 * @date    : 2024/12/3
 * @time    : 21:49
 * @fileName: ImagickImageTextGraphicsEngine.php
 */

namespace Kkokk\Poster\Image\Graphics;

use Kkokk\Poster\Image\Graphics\Traits\ImagickTextTrait;
use Kkokk\Poster\Image\Traits\ImagickTrait;

class ImagickImageTextGraphicsEngine extends ImageTextGraphicsEngine
{
    use ImagickTrait, ImagickTextTrait;
}