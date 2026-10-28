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
            throw new PosterException('The parameter of the addText method must be Kkokk\Poster\Image\Gd\Text');
        }
        $this->contents[] = $text;
        return $this;
    }

    public function addImage(ImageGraphicsEngineInterface $image)
    {
        if (!$image instanceof Image) {
            throw new PosterException('The parameter of the addImage method must be Kkokk\Poster\Image\Gd\Image');
        }
        $this->contents[] = $image;
        return $this;
    }

    public function draw(ImageGraphicsEngineInterface $canvas, $x = 0, $y = 0)
    {
        $characters = [];
        foreach ($this->contents as $content) {
            $textColor = null;
            if ($content instanceof TextGraphicsEngineInterface) {
                $textColor = $this->createColor($canvas->getImage(), $content->getFontColor());
            }
            $characters = array_merge($characters, $this->singleImageTextSplit($content, $textColor));
        }

        $this->drawTextLayout($canvas, $characters, $x, $y);
    }
}
