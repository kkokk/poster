<?php
/**
 * User: lang
 * Date: 2025/3/24
 * Time: 17:33
 */

namespace Kkokk\Poster\Image\Imagick;

use Kkokk\Poster\Image\Graphics\ImagickImageTextGraphicsEngine;
use Kkokk\Poster\Image\Graphics\Interfaces\ImageTextInterface;
use Kkokk\Poster\Image\Graphics\Interfaces\TextGraphicsEngineInterface;
use Kkokk\Poster\Image\Graphics\Interfaces\ImageGraphicsEngineInterface;

class ImageText extends ImagickImageTextGraphicsEngine implements ImageTextInterface
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