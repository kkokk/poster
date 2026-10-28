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
        foreach ($this->contents as $content) {
            $textColor = null;
            if ($content instanceof TextGraphicsEngineInterface) {
                $textColor = $this->createColor($content->getFontColor());
            }
            $characters = array_merge($characters, $this->singleImageTextSplit($content, $textColor));
        }

        $this->drawTextLayout($canvas, $characters, $x, $y);
    }
}
