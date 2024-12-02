<?php
/**
 * User: lang
 * Date: 2024/11/28
 * Time: 9:30
 */

namespace Kkokk\Poster\Image\Graphics\Interfaces;

interface TextsInterface
{
    public function addText(TextGraphicsEngineInterface $text);

    public function draw(ImageGraphicsEngineInterface $image, $x = 0, $y = 0);
}