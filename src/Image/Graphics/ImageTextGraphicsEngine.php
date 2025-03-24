<?php
/**
 * @author  : lang
 * @email   : 732853989@qq.com
 * @date    : 2024/12/3
 * @time    : 21:42
 * @fileName: ImageTextGraphicsEngine.php
 */

namespace Kkokk\Poster\Image\Graphics;

use Kkokk\Poster\Image\Graphics\Traits\ImagickTextTrait;
use Kkokk\Poster\Image\Traits\ImagickTrait;

class ImageTextGraphicsEngine extends TextGraphicsEngine
{
    use ImagickTrait, ImagickTextTrait;

    protected $lineHeight = 16;

    public function resolveFontSize($fontSize = null)
    {
        return (($fontSize ?: $this->fontSize) * 3) / 4;
    }
}