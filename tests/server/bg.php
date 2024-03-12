<?php
/**
 * User: lang
 * Date: 2023/9/22
 * Time: 9:21
 */

use Kkokk\Poster\Facades\Poster;
require '../../vendor/autoload.php';

try {
    Poster::extension('imagick')
        ->config(
        [
            'path' => __DIR__ . '/../poster/test1.png',
        ]
    )
        ->buildIm(500, 600, [255, 255, 255, 127], true)
        ->buildBg(400,400,[
            'color'=>[
                [0,0,162],
                [0,255,162],
                [255,255,162],
                [255, 0, 0],
                [0, 255, 0]
            ],
            'alpha'=>0,
            'to'=>'bottom',
            'radius'=>20,
            'content_alpha' => 20
        ], true, 'center', 'center', 0, 0 , function($im){
            /** @var \Kkokk\Poster\Image\Builder $im */
            $im->buildText('明月几时有，把酒问青天。不知天上宫阙，今夕是何年。','center',100,20,[255, 255, 255, 1]);
        })->getPoster(); # 创建画布
} catch (\Exception $e) {
    print_r($e);
}