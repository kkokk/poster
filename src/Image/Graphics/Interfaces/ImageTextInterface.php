<?php
/**
 * User: lang
 * Date: 2024/11/28
 * Time: 9:30
 */

namespace Kkokk\Poster\Image\Graphics\Interfaces;

interface ImageTextInterface
{
    public function addText(TextGraphicsEngineInterface $text);

    public function addImage(ImageGraphicsEngineInterface $image);

    public function draw(ImageGraphicsEngineInterface $canvas, $x = 0, $y = 0);
}