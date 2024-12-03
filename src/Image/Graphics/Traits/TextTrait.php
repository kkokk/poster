<?php
/**
 * @author  : lang
 * @email   : 732853989@qq.com
 * @date    : 2024/12/3
 * @time    : 22:12
 * @fileName: TextTrait.php
 */

namespace Kkokk\Poster\Image\Graphics\Traits;

use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Image\Graphics\Interfaces\ImageGraphicsEngineInterface;
use Kkokk\Poster\Image\Graphics\Interfaces\TextGraphicsEngineInterface;

trait TextTrait
{
    protected $springRate = 0.3548;

    protected function singleImageTextSplit($graphics, $textColor = null)
    {
        $singleGraphics = [];

        if ($graphics instanceof TextGraphicsEngineInterface) {
            if (is_null($textColor)) {
                throw new PosterException('TextGraphicsEngineInterface must set textColor');
            }
            $textFont = $graphics->getFont();
            $textFontSize = $graphics->resolveFontSize();
            $textFontAngle = $graphics->getFontAngle();
            $textFontSpace = $graphics->getFontSpace();
            $textFontWeight = $graphics->getFontWeight();
            $textLineHeight = $graphics->getLineHeight();
            $textTextAlign = $graphics->getFontAlign();
            $content = $graphics->getText();
            for ($i = 0; $i < mb_strlen($content); $i++) {
                $singleGraphics[] = [
                    'type'       => 'text',
                    'text'       => mb_substr($content, $i, 1),
                    'color'      => $textColor,
                    'font'       => $textFont,
                    'size'       => $textFontSize,
                    'angle'      => $textFontAngle,
                    'space'      => $textFontSpace,
                    'weight'     => $textFontWeight,
                    'lineHeight' => $textLineHeight,
                    'align'      => $textTextAlign,
                ];
            }
        } elseif ($graphics instanceof ImageGraphicsEngineInterface) {
            $singleGraphics[] = [
                'type'       => 'image',
                'image'      => $graphics,
                'width'      => $graphics->getWidth(),
                'height'     => $graphics->getHeight(),
                'lineHeight' => $graphics->getHeight(),
            ];
        }

        return $singleGraphics;
    }
}