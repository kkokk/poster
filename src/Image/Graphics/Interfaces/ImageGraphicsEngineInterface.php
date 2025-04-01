<?php
/**
 * User: lang
 * Date: 2024/11/29
 * Time: 11:01
 */

namespace Kkokk\Poster\Image\Graphics\Interfaces;

interface ImageGraphicsEngineInterface
{
    public function getImage();

    public function getWidth();

    public function getHeight();

    public function getData($path = '');

    public function getStream($type = '');

    public function getBaseData();

    public function blob();

    public function tmp();

    public function setData();

    public function thumb($newWidth, $newHeight, $bestFit = false);

    public function scale($newWidth, $newHeight, $bestFit = false);

    public function circle();

    public function crop($x = 0, $y = 0, $width = 0, $height = 0);

    public function transparent($transparency);

    public function borderRadius($radius = 0);

    public function applyMask($mask);

}