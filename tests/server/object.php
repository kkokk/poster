<?php
/**
 * User: lang
 * Date: 2024/11/26
 * Time: 11:15
 */

// use Kkokk\Poster\Image\Gd\Canvas;

use Kkokk\Poster\Image\Imagick\Canvas;

require '../../vendor/autoload.php';

$color = "#fffffff0";
$rgb = sscanf($color, "#%02x%02x%02x%02x");


$canvas = new Canvas(500, 500, '#ffffff50');

// $image = new \Kkokk\Poster\Image\Gd\Image('https://portrait.gitee.com/uploads/avatars/user/721/2164500_langlanglang_1601019617.png');
$image = new \Kkokk\Poster\Image\Imagick\Image('https://portrait.gitee.com/uploads/avatars/user/721/2164500_langlanglang_1601019617.png');
$canvas->addImage($image, 'center', 'center');
$canvas->addImage((clone $image)->scale(50, 50)->circle(), 0, 0);
$canvas->addImage($image->scale(100, 100)->circle(), 'center', 0);

$canvas->output(__DIR__ . '/../poster/test5.png');