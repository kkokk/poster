<?php
/**
 * User: lang
 * Date: 2024/11/29
 * Time: 11:01
 */

namespace Kkokk\Poster\Image\Graphics\Interfaces;

interface GraphicsEngineInterface
{
    public function getImage();
    public function getWidth();
    public function getHeight();
}