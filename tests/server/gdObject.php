<?php
/**
 * User: lang
 * Date: 2024/11/26
 * Time: 11:15
 */

use Kkokk\Poster\Image\Gd\Canvas;
use Kkokk\Poster\Image\Gd\Image;
use Kkokk\Poster\Image\Gd\Text;

require '../../vendor/autoload.php';

$canvas = new Canvas();

$file = 'C:\\\\\\\\///Users\\\////32822\Pictures////\\\\\\\\\\' . '朝天门.jpg';
// $file = 'https://img2.baidu.com/it/u=1310029438,409566289&fm=253&fmt=auto&app=138&f=JPEG?w=800&h=1541';

$canvas->readImage($file);

// $image = new Image('https://portrait.gitee.com/uploads/avatars/user/721/2164500_langlanglang_1601019617.png');
$image = new Image('https://img2.baidu.com/it/u=1310029438,409566289&fm=253&fmt=auto&app=138&f=JPEG?w=800&h=1541');
// $canvas->addImage($image->scale(100, 100)->circle(), 'center', 'center');
// $canvas->addImage((clone $image)->scale(50, 50)->circle(), 0, 0);
// $canvas->addImage($image->scale(100, 100)->circle(), 'center', 0);

$canvas->addImage($image->crop('center', 'center', 500, 500));

$qr = new \Kkokk\Poster\Image\Gd\Qr('http://www.baidu.com');

$canvas->addImage($qr, 'center', 'center');

$text = (new Text())
    ->setText("Hello World! This is a test for text wrapping and alignment.")
    ->setFontAlign('center');

// $canvas->getData(__DIR__ . '/../poster/test7.png');
// $canvas->setData();
$text->draw($canvas, 20, 50);


$canvas->getStream();