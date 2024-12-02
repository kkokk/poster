<?php
/**
 * User: lang
 * Date: 2024/11/26
 * Time: 11:15
 */

use Kkokk\Poster\Image\Imagick\Canvas;
use Kkokk\Poster\Image\Imagick\Text;

require '../../vendor/autoload.php';

$canvas = new Canvas(500, 500);


$file = '/Users/lang/Documents/image/3e000e09b00c001a6ff3d0ec9fb1e01b.jpeg';
// $file = 'https://img2.baidu.com/it/u=1310029438,409566289&fm=253&fmt=auto&app=138&f=JPEG?w=800&h=1541';

// $canvas->readImage($file);

// $image = new \Kkokk\Poster\Image\Imagick\Image('https://portrait.gitee.com/uploads/avatars/user/721/2164500_langlanglang_1601019617.png');
// $image = new \Kkokk\Poster\Image\Imagick\Image('https://img2.baidu.com/it/u=1310029438,409566289&fm=253&fmt=auto&app=138&f=JPEG?w=800&h=1541');
// $canvas->addImage($image->scale(100, 100)->circle(), 'center', 'center');
// $canvas->addImage((clone $image)->scale(50, 50)->circle(), 0, 0);
// $canvas->addImage($image->scale(100, 100)->circle(), 'center', 0);

// $canvas->addImage($image->crop('center', 'center', 500, 500));

// $qr = new \Kkokk\Poster\Image\Imagick\Qr('http://www.baidu.com', 'L', 4);
// $canvas->addImage($qr, 'center', 'center');

$text = (new Text())
    ->setText("床前明月光，疑似地上霜。举头望明月，低头思故乡。床前明月光，疑似地上霜。举头望明月，低头思故乡。")
    ->setAlign('left');


// $canvas->getData(__DIR__ . '/../poster/test7.png');
// $canvas->setData();
$text->draw($canvas, 20, 50);
$canvas->getStream();