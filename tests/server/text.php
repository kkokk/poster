<?php

require '../../vendor/autoload.php';

use Kkokk\Poster\Facades\Poster;

$image = 'https://pic.rmb.bdstatic.com/bjh/news/7dfe43f0ddf2f910ed5e68d17b67a35b.png';

// Poster::extension('gd')
//     ->buildIm(320, 320, [], true)
//     ->buildText('Poster', 'center', 'center', 80, [0, 0, 0, 1])
//     ->stream();

// echo "<img style='color:#1900ff;' />";

Poster::extension('imagick')
    ->buildImDst($image)
    ->getCanvas()
    ->borderRadius(50)
    ->getStream();


$canvas = Poster::extension('gd')
    ->buildIm(320, 320, [], true)
    // ->buildBg(320, 320, [
    //     'color' => [
    //         [7, 143, 233],
    //         [189, 73, 164]
    //     ],
    //     'to'    => 'right bottom',
    //     'alpha' => 20
    //     // 'radius' => 50
    // ], true)
    ->buildText('Poster', 'center', 'center', 58, [0, 0, 0, 1], '', '', 10)
    ->getCanvas();

// $canvas->getData('C:\lang\custom\poster-doc\docs\public\image.png');
$canvas->getStream();