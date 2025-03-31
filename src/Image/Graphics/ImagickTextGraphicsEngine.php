<?php
/**
 * @author  : lang
 * @email   : 732853989@qq.com
 * @date    : 2024/11/30
 * @time    : 17:14
 * @fileName: ImagickTextGraphicsEngine.php
 */

namespace Kkokk\Poster\Image\Graphics;

use Kkokk\Poster\Image\Graphics\Interfaces\TextGraphicsEngineInterface;
use Kkokk\Poster\Image\Graphics\Traits\ImagickTextTrait;
use Kkokk\Poster\Image\Traits\ImagickTrait;

class ImagickTextGraphicsEngine extends TextGraphicsEngine implements TextGraphicsEngineInterface
{
    use ImagickTrait, ImagickTextTrait;

    public function setFontSize($fontSize)
    {
        $this->fontSize = $fontSize;
        $this->lineHeight = round((($fontSize ?: $this->fontSize) * 3) / 4 * 1.5);
        return $this;
    }
}