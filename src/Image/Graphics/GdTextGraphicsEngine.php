<?php
/**
 * @author  : lang
 * @email   : 732853989@qq.com
 * @date    : 2024/11/30
 * @time    : 17:13
 * @fileName: GdTextGraphicsEngine.php
 */

namespace Kkokk\Poster\Image\Graphics;

use Kkokk\Poster\Image\Graphics\Interfaces\TextGraphicsEngineInterface;
use Kkokk\Poster\Image\Graphics\Traits\GdTextTrait;
use Kkokk\Poster\Image\Traits\GdTrait;

class GdTextGraphicsEngine extends TextGraphicsEngine implements TextGraphicsEngineInterface
{
    use GdTrait, GdTextTrait;

    public function resolveFontSize($fontSize = null)
    {
        return (($fontSize ?: $this->fontSize) * 3) / 4;
    }
}