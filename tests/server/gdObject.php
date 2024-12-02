<?php
/**
 * User: lang
 * Date: 2024/11/26
 * Time: 11:15
 */

use Kkokk\Poster\Image\Gd\Canvas;
use Kkokk\Poster\Image\Gd\Image;
use Kkokk\Poster\Image\Gd\Text;
use Kkokk\Poster\Image\Gd\Texts;

require '../../vendor/autoload.php';

$canvas = new Canvas(500, 500);

// $file = '/Users/lang/Documents/image/3e000e09b00c001a6ff3d0ec9fb1e01b.jpeg';
// $file = 'https://img2.baidu.com/it/u=1310029438,409566289&fm=253&fmt=auto&app=138&f=JPEG?w=800&h=1541';

// $canvas->readImage($file);

// $image = new Image('https://portrait.gitee.com/uploads/avatars/user/721/2164500_langlanglang_1601019617.png');
// $image = new Image('https://img2.baidu.com/it/u=1310029438,409566289&fm=253&fmt=auto&app=138&f=JPEG?w=800&h=1541');
// $canvas->addImage($image->scale(100, 100)->circle(), 'center', 'center');
// $canvas->addImage((clone $image)->scale(50, 50)->circle(), 0, 0);
// $canvas->addImage($image->scale(100, 100)->circle(), 'center', 0);

// $canvas->addImage($image->crop('center', 'center', 500, 500));

// $qr = new \Kkokk\Poster\Image\Gd\Qr('http://www.baidu.com');
//
// $canvas->addImage($qr, 'center', 'center');

$texts = new Texts();
$texts
    ->setFontAlign('right')
    ->setMaxWidth(300)
    ->addText((new Text())
        ->setText("床前明月光，")
        ->setFontSize(22)
        ->setFontColor('#000000')
    )
    ->addText((new Text())
        ->setText("疑似地上霜。")
        ->setFontSize(22)
        ->setFontColor('#00ff00')
    )
    ->addText((new Text())
        ->setText("举头望明月，")
        ->setFontSize(22)
        ->setFontColor('#0000ff')
    )
    ->addText((new Text())
        ->setText("低头思故乡。")
        ->setFontSize(22)
        ->setFontColor('#ff0000')
    );

$texts->draw($canvas, 'center', 'center');

(new Text())
    ->setText("床前明月光，")
    ->setFontSize(16)
    ->setFontColor('#000000')->draw($canvas, 0, 100);

(new Text())
    ->setText("疑似地上霜。")
    ->setFontSize(22)
    ->setFontColor('#00ff00')->draw($canvas, 90, 92);

(new Text())
    ->setText("举头望明月，")
    ->setFontSize(12)
    ->setFontColor('#0000ff')->draw($canvas, 212, 106);

(new Text())
    ->setText("低头思故乡。")
    ->setFontSize(28)
    ->setFontColor('#ff0000')->draw($canvas, 280, 83);

// $canvas->getData(__DIR__ . '/../poster/test7.png');
// $canvas->setData();


$canvas->getStream();

