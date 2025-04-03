<?php

require '../../vendor/autoload.php';

use Kkokk\Poster\Facades\Poster;


// Poster::extension('gd')
//     ->buildIm(320, 320, [], true)
//     ->buildText('Poster', 'center', 'center', 80, [0, 0, 0, 1])
//     ->stream();

// echo "<img style='color:#1900ff;' />";

Poster::extension('imagick')
    ->buildImDst('C:\lang\custom\poster-doc\docs\public\favicon.png')
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