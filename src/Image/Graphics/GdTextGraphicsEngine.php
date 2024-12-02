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
use Kkokk\Poster\Image\Traits\GdTrait;

class GdTextGraphicsEngine extends TextGraphicsEngine implements TextGraphicsEngineInterface
{
    use GdTrait;

    protected function resolveFontSize($fontSize = null)
    {
        return (($fontSize ?: $this->fontSize) * 3) / 4;
    }

    protected function horizontalOffset()
    {
        $normalSize = $this->textWidth('好');
        $punctuationSize = $this->textWidth('，');
        // 计算标点符号的水平偏移量
        return abs($punctuationSize - $normalSize) / 2;
    }

    public function textWidth($text, $fontSize = null, $font = null, $angle = 0)
    {
        $textBox = imagettfbbox($this->resolveFontSize($fontSize), 0, $font ?: $this->font, $text);
        return $textBox[2] - $textBox[0];
    }
}