<?php
/**
 * User: lang
 * Date: 2023/8/9
 * Time: 17:12
 */

use Kkokk\Poster\Facades\Html;
use Kkokk\Poster\Facades\Poster;

require '../../vendor/autoload.php';

$html = <<<HTML
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>你好</title>
        <style>
          html, body{
            margin: 0;
            padding: 0;
          }
          .app {
            width: 338px;
            height: 426px;
            background: -webkit-linear-gradient(top left,red, orange, yellow, green, blue, purple);
            border-radius: 40px;
          }
          h1{
            margin: 0;
            text-align: center;
          }
        </style>
    </head>
    <body>
      <div class="app">
        <h1>你好，世界</h1>
      </div>
    </body>
</html>
HTML;

$img = Html::channel('wk')->load($html)->transparent()->size(338, 426)->render();

$result = Poster::extension('gd')
    ->config([
        'path' => __DIR__ . '/../poster/test1.png',
    ])
    ->buildIm(638, 826, [41, 43, 48, 127], false)
    ->buildImage([
        // 'src' => __DIR__ . '/../poster/1689560381.png',
        'src'   => $img->getImageBlob(),
        'angle' => 0
    ], 'center', 'center')
    ->buildImage([
        'src'   => 'https://portrait.gitee.com/uploads/avatars/user/721/2164500_langlanglang_1601019617.png',
        'angle' => 80
    ], 253, 326, 0, 0, 131, 131, false, 'circle')
    ->buildText('苏轼', 'center', 477, 16, [255, 255, 255, 1])
    ->buildText('明月几时有，把酒问青天。不知天上宫阙，今夕是何年。', 'center', 515, 14, [255, 255, 255, 1])
    ->stream();