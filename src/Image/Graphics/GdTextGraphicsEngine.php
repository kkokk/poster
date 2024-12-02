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

    protected function horizontalOffset($fontSize = null, $font = null, $angle = 0)
    {
        $normalSize = $this->textWidth('好', $fontSize, $font, $angle);
        $punctuationSize = $this->textWidth('，', $fontSize, $font, $angle);
        // 计算标点符号的水平偏移量
        return abs($punctuationSize - $normalSize) / 2;
    }


    public function calculateTextBox($text, $fontSize, $font, $angle)
    {
        $rect = imagettfbbox($fontSize, $angle, $font, $text);
        $minX = min([$rect[0], $rect[2], $rect[4], $rect[6]]);
        $maxX = max([$rect[0], $rect[2], $rect[4], $rect[6]]);
        $minY = min([$rect[1], $rect[3], $rect[5], $rect[7]]);
        $maxY = max([$rect[1], $rect[3], $rect[5], $rect[7]]);

        return [
            "left"   => abs($minX) - 1,
            "top"    => abs($minY) - 1,
            "width"  => abs($maxX - $minX),
            "height" => abs($maxY - $minY),
            "box"    => $rect
        ];
    }

    public function textWidth($text, $fontSize = null, $font = null, $angle = 0)
    {
        $fontSize = $this->resolveFontSize($fontSize);
        $calculateTextBox = $this->calculateTextBox($text, $fontSize, $font ?: $this->font, $angle);
        return $calculateTextBox['width'];
    }

    public function textHeight($text, $fontSize = null, $font = null, $angle = 0)
    {
        $fontSize = $this->resolveFontSize($fontSize);
        $calculateTextBox = $this->calculateTextBox($text, $fontSize, $font ?: $this->font, $angle);
        return $calculateTextBox['height'];
    }
}