<?php
/**
 * @author  : lang
 * @email   : 732853989@qq.com
 * @date    : 2024/11/30
 * @time    : 17:01
 * @fileName: Texts.php
 */

namespace Kkokk\Poster\Image\Gd;

use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Image\Graphics\GdImageTextGraphicsEngine;
use Kkokk\Poster\Image\Graphics\Interfaces\ImageGraphicsEngineInterface;
use Kkokk\Poster\Image\Graphics\Interfaces\TextGraphicsEngineInterface;
use Kkokk\Poster\Image\Graphics\Interfaces\ImageTextInterface;

class ImageText extends GdImageTextGraphicsEngine implements ImageTextInterface
{
    /** @var array|Text[] */
    protected $contents = [];

    public function addText(TextGraphicsEngineInterface $text)
    {
        if (!$text instanceof Text) {
            throw new PosterException('must be Kkokk\Poster\Image\Gd\Text');
        }
        $this->contents[] = $text;
        return $this;
    }

    public function addImage(ImageGraphicsEngineInterface $image)
    {
        if (!$image instanceof Image) {
            throw new PosterException('must be Kkokk\Poster\Image\Gd\Image');
        }
        $this->contents[] = $image;
        return $this;
    }

    public function draw(ImageGraphicsEngineInterface $canvas, $x = 0, $y = 0)
    {
        $characters = [];
        $maxWidth = $this->getMaxWidth() ?: $canvas->getWidth();
        foreach ($this->contents as $content) {
            $textColor = null;
            if ($content instanceof TextGraphicsEngineInterface) {
                $textColor = $this->createColor($canvas->getImage(), $content->getFontColor());
            }
            $characters = array_merge($characters, $this->singleImageTextSplit($content, $textColor));
        }

        list($lines, $maxTextWidth, $maxTextHeight, $textWidths) = $this->autoWrap($characters, $maxWidth);
        $distX = calc_text_dst_x($x, ['max_width' => $maxTextWidth], $canvas->getWidth());
        $distY = calc_text_dst_y($y, ['max_height' => $maxTextHeight], $canvas->getHeight());
        foreach ($lines as $lineIndex => $lineCharacters) {
            // 总宽度（文字宽度 + 间隔 + 字体粗细）
            $textWidth = $textWidths[$lineIndex];
            switch ($this->textAlign) {
                case 'center':
                    $offsetX = $distX + floor($maxTextWidth - $textWidth) / 2;
                    break;
                case 'right':
                    $offsetX = floor($maxTextWidth - $textWidth) + $distX;
                    break;
                default:
                    $offsetX = $distX;
            }
            $lineMaxHeight = max(array_column($lineCharacters, 'lineHeight'));
            $lineHeight = $lineIndex > 0 ? $lineMaxHeight : 0;
            foreach ($lineCharacters as $char) {
                $lineY = $distY + $lineHeight;
                if ($char['type'] == 'text') {
                    for ($index = 0; $index < $char['weight']; $index++) {
                        list($offsetX, $lineY) = calc_font_weight($index, $char['weight'], $char['size'],
                            $offsetX, $lineY);
                        imagettftext($canvas->getImage(), $char['size'], $char['angle'], intval($offsetX),
                            intval($lineY),
                            $char['color'],
                            $char['font'],
                            $char['text']);
                    }
                    $offsetX += $char['width'] + $char['space'];
                } else {
                    $canvas->addImage($char['image'], intval($offsetX), intval($lineY - $char['height']));
                    $offsetX += $char['width'];
                }
            }
            $distY += $lineHeight;
        }
    }
}