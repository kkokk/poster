<?php
/**
 * User: lang
 * Date: 2024/11/26
 * Time: 11:15
 */

use Kkokk\Poster\Image\Gd\Canvas as GdCanvas;
use Kkokk\Poster\Image\Gd\Image as GdImage;
use Kkokk\Poster\Image\Gd\Text as GdText;
use Kkokk\Poster\Image\Gd\ImageText as GdImageText;
use Kkokk\Poster\Image\Imagick\Canvas as ImagickCanvas;
use Kkokk\Poster\Image\Imagick\Image as ImagickImage;
use Kkokk\Poster\Image\Imagick\Text as ImagickText;
use Kkokk\Poster\Image\Imagick\ImageText as ImagickImageText;
use Kkokk\Poster\Facades\Poster;

require '../../vendor/autoload.php';

$file = 'C:\\\\\\\\///Users\\\////32822\Pictures////\\\\\\\\\\' . '朝天门.jpg';


// 设置路径
$sourcePath = __DIR__ . "/../poster/test7.png";          // 原始图片所在目录
$targetPath = __DIR__ . "/../poster/src/output1.png";    // 处理后的图片保存目录
$maskPath = __DIR__ . "/../poster/mask/mask1.png";       // 蒙版图片路径

$maskCanvas = Poster::config(['type' => 'png'])
    ->buildBg(638, 826, [
        'color' => [
            [255, 255, 255],
            [0, 0, 0],
            [255, 255, 255],
        ],
    ])->getCanvas();
// $file = 'https://img2.baidu.com/it/u=1310029438,409566289&fm=253&fmt=auto&app=138&f=JPEG?w=800&h=1541';

$imageText = (new ImagickImageText())
    ->setMaxWidth(300)
    ->setFontAlign('left')
    ->addText((new ImagickText())
        ->setText("床前明月光，")
        ->setFontSize(16)
        ->setFontColor('#000000')
    )
    ->addText((new ImagickText())
        ->setText("疑似地上霜。")
        ->setFontSize(22)
        ->setFontColor('#00ff00')
    )
    ->addText((new ImagickText())
        ->setText("举头望明月，")
        ->setFontSize(12)
        ->setFontColor('#0000ff')
    )
    ->addText((new ImagickText())
        ->setText("低头思故乡。")
        ->setFontSize(28)
        ->setFontColor('#ff0000')
        ->setLineHeight(40)
    )
    ->addImage((new ImagickImage($file))->scale(50, 30))
    ->addText((new ImagickText())
        ->setText("图")
        ->setFontSize(28)
        ->setFontColor('#ff0000')
    );

// $imageText = (new GdImageText())
//     ->setMaxWidth(300)
//     ->setFontAlign('left')
//     ->addText((new GdText())
//         ->setText("床前明月光，")
//         ->setFontSize(16)
//         ->setFontColor('#000000')
//     )
//     ->addText((new GdText())
//         ->setText("疑似地上霜。")
//         ->setFontSize(22)
//         ->setFontColor('#00ff00')
//     )
//     ->addText((new GdText())
//         ->setText("举头望明月，")
//         ->setFontSize(12)
//         ->setFontColor('#0000ff')
//     )
//     ->addText((new GdText())
//         ->setText("低头思故乡。")
//         ->setFontSize(28)
//         ->setFontColor('#ff0000')
//         ->setLineHeight(40)
//     )
//     ->addImage((new GdImage($file))->scale(50, 30))
//     ->addText((new GdText())
//         ->setText("图")
//         ->setFontSize(28)
//         ->setFontColor('#ff0000')
//     );

$canvas = Poster::extension('imagick')
    ->config(['type' => 'png'])
    // ->buildImDst($file)
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
        'to'     => 'bottom',
        'radius' => '40',
    ], true, 'center', 'center', 0, 0,
        function (\Kkokk\Poster\Image\Builder $builder) {
            $builder->buildLine(10, 100, 100, 100, [0, 0, 0, 1]);
            $builder->buildArc(100, 100, 100, 100, 0, 180, [0, 0, 0, 1]);
            $builder->buildText('明月几时有，把酒问青天，不知天上宫阙，今夕是何年。', 10, 200, 28,
                [0, 0, 0], 0, '', 6, 10);
            $builder->buildText('明月几时有，把酒问青天，不知天上宫阙，今夕是何年。', 10, 40);
            $builder->buildQr('http://www.520yummy.com/poster-doc/guide/', 'center', 'center');
        })
    ->buildText($imageText, 300, 300);
// ->crop(0, 0, 500, 500)
$canvas->getCanvas()->applyMask($maskCanvas)->getStream();