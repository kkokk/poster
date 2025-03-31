<?php
/**
 * User: lang
 * Date: 2025/3/24
 * Time: 17:33
 */

namespace Kkokk\Poster\Image\Imagick;

use Kkokk\Poster\Exception\PosterException;
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
        if (!$text instanceof Text) {
            throw new PosterException('The parameter of the addText method must be Kkokk\Poster\Image\Imagick\Text');
        }
        $this->contents[] = $text;
        return $this;
    }

    public function addImage(ImageGraphicsEngineInterface $image)
    {
        if (!$image instanceof Image) {
            throw new PosterException('The parameter of the addImage method must be Kkokk\Poster\Image\Imagick\Image');
        }
        $this->contents[] = $image;
        return $this;
    }

    public function draw(ImageGraphicsEngineInterface $canvas, $x = 0, $y = 0)
    {
        $this->setCanvas($canvas);
        $characters = [];
        $maxWidth = $this->getMaxWidth() ?: $canvas->getWidth();
        foreach ($this->contents as $content) {
            $textColor = null;
            if ($content instanceof TextGraphicsEngineInterface) {
                $textColor = $this->createColor($content->getFontColor());
            }
            $characters = array_merge($characters, $this->singleImageTextSplit($content, $textColor));
        }

        list($lines, $maxTextWidth, $maxTextHeight, $textWidths) = $this->autoWrap($characters, $maxWidth, false);
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
                    $draw = $this->createImagickDraw();
                    $draw->setFont($char['font']);
                    $draw->setFontSize($char['size']);
                    $draw->setFillColor($char['color']);
                    for ($index = 0; $index < $char['weight']; $index++) {
                        list($offsetX, $lineY) = calc_font_weight($index, $char['weight'], $char['size'],
                            $offsetX, $lineY);
                        $canvas->getImage()->annotateImage($draw, $offsetX, $lineY, $char['angle'], $char['text']);
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