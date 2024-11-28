<?php
/**
 * User: lang
 * Date: 2024/11/26
 * Time: 11:15
 */

use Kkokk\Poster\Image\Gd\Canvas;

require '../../vendor/autoload.php';

$canvas = new Canvas();

// $file = 'C:\\\\\\\\///Users\\\////32822\Pictures////\\\\\\\\\\' . '朝天门.jpg';
$file = '/Users/lang/Documents/image/3e000e09b00c001a6ff3d0ec9fb1e01b.jpeg';
// $file = 'https://img2.baidu.com/it/u=257681495,312745373&fm=253&fmt=auto&app=120&f=JPEG?w=750&h=500';
// $file = 'https://img2.baidu.com/it/u=1310029438,409566289&fm=253&fmt=auto&app=138&f=JPEG?w=800&h=1541';

$canvas->readImage($file);

// $image = new \Kkokk\Poster\Image\Gd\Image('https://portrait.gitee.com/uploads/avatars/user/721/2164500_langlanglang_1601019617.png');
$image = new \Kkokk\Poster\Image\Gd\Image('https://img2.baidu.com/it/u=1310029438,409566289&fm=253&fmt=auto&app=138&f=JPEG?w=800&h=1541');
// $canvas->addImage($image->scale(100, 100)->circle(), 'center', 'center');
// $canvas->addImage((clone $image)->scale(50, 50)->circle(), 0, 0);
// $canvas->addImage($image->scale(100, 100)->circle(), 'center', 0);
$canvas->addImage($image->crop(200, 200, 500, 500));

// $canvas->getData(__DIR__ . '/../poster/test7.png');
// $canvas->setData();
$canvas->getStream();