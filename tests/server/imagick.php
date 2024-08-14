<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/21
 * Time: 17:39
 */
use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Facades\Poster;

require '../vendor/autoload.php';

try {

    $res = Poster::extension('imagick')
        ->config(['path'=>'poster/imagick.png','font_family'=> __DIR__ . '/../src/style/simkai.ttf'])
        ->buildIm(638,826,[255,255,255,1],true)
        ->getPoster();

} catch (PosterException $e) {

    print_r($e->getMessage());

}