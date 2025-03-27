<?php
/**
 * User: lang
 * Date: 2024/11/26
 * Time: 11:15
 */

use Kkokk\Poster\Image\Gd\Canvas;
use Kkokk\Poster\Image\Gd\Image;
use Kkokk\Poster\Image\Gd\Text;
use Kkokk\Poster\Facades\Poster;

require '../../vendor/autoload.php';


// $canvas = new Canvas(500, 500, [255, 255, 255, 127]);

$file = 'C:\\\\\\\\///Users\\\////32822\Pictures////\\\\\\\\\\' . '朝天门.jpg';
// $file = 'https://img2.baidu.com/it/u=1310029438,409566289&fm=253&fmt=auto&app=138&f=JPEG?w=800&h=1541';

// $canvas->readImage($file, 500, 500);
// $canvas->getStream();
// exit;

$canvas = Poster::extension('imagick')
    // ->buildIm(800, 1200, [255, 255, 255])
    ->buildBg(638, 826, [
        'color'  => [
            [255, 0, 0],
            [255, 125, 0],
            [255, 255, 0],
            [0, 255, 0],
            [0, 255, 255],
            [0, 0, 255],
            [255, 0, 255]
        ],
        'alpha'  => 50,
        'to'     => 'left top',
        'radius' => '40'
    ], true, 'center', 'center', 0, 0,
        function (\Kkokk\Poster\Image\Builder $builder) {
            $builder->buildLine(10, 100, 100, 100, [0, 0, 0, 1]);
            $builder->buildText('明月几时有，把酒问青天，不知天上宫阙，今夕是何年。', 'center', 'center', 28,
                [0, 0, 0], 0, '', 6, 10);
        });
$canvas->stream();
exit;

// $image = new Image('https://portrait.gitee.com/uploads/avatars/user/721/2164500_langlanglang_1601019617.png');
$image = new Image('https://img2.baidu.com/it/u=1310029438,409566289&fm=253&fmt=auto&app=138&f=JPEG?w=800&h=1541');
// $canvas->addImage($image->scale(100, 100)->circle(), 'center', 'center');
// $canvas->addImage((clone $image)->scale(50, 50)->circle(), 0, 0);
// $canvas->addImage($image->scale(100, 100)->circle(), 'center', 0);

$canvas->addImage($image->crop('center', 'center', 500, 500));

// $qr = new \Kkokk\Poster\Image\Gd\Qr('http://www.baidu.com');

// $canvas->addImage($qr, 'center', 'center');


// (new Texts())
//     ->setMaxWidth(300)
//     ->setFontAlign('left')
//     ->addText((new Text())
//         ->setText("床前明月光，")
//         ->setFontSize(16)
//         ->setFontColor('#000000')
//     )
//     ->addText((new Text())
//         ->setText("疑似地上霜。")
//         ->setFontSize(22)
//         ->setFontColor('#00ff00')
//     )
//     ->addText((new Text())
//         ->setText("举头望明月，")
//         ->setFontSize(12)
//         ->setFontColor('#0000ff')
//     )
//     ->addText((new Text())
//         ->setText("低头思故乡。")
//         ->setFontSize(28)
//         ->setFontColor('#ff0000')
//     )->draw($canvas, 'center', 100);

// (new Texts())
//     ->setMaxWidth(300)
//     ->setFontAlign('center')
//     ->addText((new Text())
//         ->setText("床前明月光，")
//         ->setFontSize(16)
//         ->setFontColor('#000000')
//     )
//     ->addText((new Text())
//         ->setText("疑似地上霜。")
//         ->setFontSize(22)
//         ->setFontColor('#00ff00')
//     )
//     ->addText((new Text())
//         ->setText("举头望明月，")
//         ->setFontSize(12)
//         ->setFontColor('#0000ff')
//     )
//     ->addText((new Text())
//         ->setText("低头思故乡。")
//         ->setFontSize(28)
//         ->setFontColor('#ff0000')
//     )->draw($canvas, 'center', 200);

// (new Texts())
//     ->setMaxWidth(300)
//     ->setFontAlign('right')
//     ->addText((new Text())
//         ->setText("床前明月光，")
//         ->setFontSize(16)
//         ->setFontColor('#000000')
//     )
//     ->addText((new Text())
//         ->setText("疑似地上霜。")
//         ->setFontSize(22)
//         ->setFontColor('#00ff00')
//     )
//     ->addText((new Text())
//         ->setText("举头望明月，")
//         ->setFontSize(12)
//         ->setFontColor('#0000ff')
//     )
//     ->addText((new Text())
//         ->setText("低头思故乡。")
//         ->setFontSize(28)
//         ->setFontColor('#ff0000')
//     )->draw($canvas, 'center', 300);

// $texts = new Texts();
// $texts
//     ->setMaxWidth(300)
//     ->setFontAlign('left')
//     ->addText((new Text())
//         ->setText("床前明月光，")
//         ->setFontSize(16)
//         ->setFontColor('#000000')
//     )
//     ->addText((new Text())
//         ->setText("疑似地上霜。")
//         ->setFontSize(22)
//         ->setFontColor('#00ff00')
//     )
//     ->addText((new Text())
//         ->setText("举头望明月，")
//         ->setFontSize(12)
//         ->setFontColor('#0000ff')
//     )
//     ->addText((new Text())
//         ->setText("低头思故乡。")
//         ->setFontSize(28)
//         ->setFontColor('#ff0000')
//     )
//     ->addText((new Text())
//         ->setText("岐王宅里寻常见，崔九堂前几度闻。正是江南好风景，落花时节又逢君。岐王宅里寻常见，崔九堂前几度闻。正是江南好风景，落花时节又逢君。岐王宅里寻常见，崔九堂前几度闻。正是江南好风景，落花时节又逢君。")
//         ->setFontSize(14)
//         ->setFontColor('#ff00fd')
//     )
//     ->addText((new Text())
//         ->setText("岐王宅里寻常见，崔九堂前几度闻。正是江南好风景，落花时节又逢君。")
//         ->setFontSize(22)
//         ->setFontColor('#00eefd')
//     )
//     ->addText((new Text())
//         ->setText("岐王宅里寻常见，崔九堂前几度闻。正是江南好风景，落花时节又逢君。岐王宅里寻常见，崔九堂前几度闻。正是江南好风景，落花时节又逢君。岐王宅里寻常见，崔九堂前几度闻。正是江南好风景，落花时节又逢君。")
//         ->setFontSize(18)
//         ->setFontColor('#ee00fd')
//     );
//
// $texts->draw($canvas, 'center', 'center');
//
(new Text())
    ->setText("床前明月光，")
    ->setFontSize(16)
    ->setFontColor('#0000ff')->draw($canvas, 0, 28);

(new Text())
    ->setText("疑似地上霜。")
    ->setFontSize(22)
    ->setFontColor('#00ff00')->draw($canvas, 90, 28);

(new Text())
    ->setText("举头望明月，")
    ->setFontSize(12)
    ->setFontColor('#0000ff')->draw($canvas, 212, 28);

(new Text())
    ->setText("低头思故乡。")
    ->setFontSize(28)
    ->setFontColor('#ff0000')->draw($canvas, 280, 28);

// $canvas->getData(__DIR__ . '/../poster/test7.png');
// $canvas->setData();


$canvas->getStream();