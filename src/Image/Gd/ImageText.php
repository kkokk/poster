<?php
/**
 * @author  : lang
 * @email   : 732853989@qq.com
 * @date    : 2024/11/30
 * @time    : 17:01
 * @fileName: Texts.php
 */

namespace Kkokk\Poster\Image\Gd;

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
        $this->contents[] = $text;
        return $this;
    }

    public function addImage(ImageGraphicsEngineInterface $image)
    {
        $this->contents[] = $image;
        return $this;
    }

    public function draw(ImageGraphicsEngineInterface $image, $x = 0, $y = 0)
    {
        $characters = [];
        $maxWidth = $this->getMaxWidth() ?: $image->getWidth();
        foreach ($this->contents as $content) {
            $textColor = null;
            if ($content instanceof TextGraphicsEngineInterface) {
                $textColor = $this->createColor($image->getImage(), $content->getFontColor());
            }
            $characters = array_merge($characters, $this->singleImageTextSplit($content, $textColor));
        }

        list($lines, $maxTextWidth, $maxTextHeight) = $this->autoWrap($characters, $maxWidth);
        $distX = calc_text_dst_x($x, ['max_width' => $maxTextWidth], $image->getWidth());
        $distY = calc_text_dst_y($y, ['max_height' => $maxTextHeight], $image->getHeight());
        foreach ($lines as $lineIndex => $lineCharacters) {
            $textWidth = array_sum(array_column($lineCharacters, 'width'));
            switch ($this->textAlign) {
                case 'center':
                    $offsetX = $distX + ($maxWidth - $textWidth) / 2;
                    break;
                case 'right':
                    $offsetX = $maxWidth - $textWidth + $distX;
                    break;
                default:
                    $offsetX = $distX;
            }

            $lineMaxHeight = max(array_column($lineCharacters, 'lineHeight'));
            $lineHeight = $lineIndex > 0 ? $lineMaxHeight : 0;
            foreach ($lineCharacters as $char) {
                $lineY = $distY + $lineHeight;
                if ($char['type'] == 'text') {
                    imagettftext($image->getImage(), $char['size'], $char['angle'], intval($offsetX), intval($lineY),
                        $char['color'],
                        $char['font'],
                        $char['text']);
                    $offsetX += $char['width'] + $char['space'];
                } else {
                    $image->addImage($char['image'], intval($offsetX), intval($lineY - $char['height']));
                    $offsetX += $char['width'];
                }
            }
            $distY += $lineHeight;
        }
    }
}