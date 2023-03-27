<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/27
 * Time: 10:35
 */

use Kkokk\Poster\Facades\Poster;

require '../../vendor/autoload.php';


// function setTextUnderColor($strokeColor = '#ffff00', $fillColor = '#00ff00', $backgroundColor = '#ff00ff', $textUnderColor = '#000000') {
//     $draw = new \ImagickDraw();
//
//     $draw->setStrokeColor($strokeColor);
//     $draw->setFillColor($fillColor);
//     $draw->setStrokeWidth(2);
//     $draw->setFontSize(72);
//     $draw->annotation(50, 75, "Lorem Ipsum!");
//     $draw->setTextUnderColor($textUnderColor);
//     $draw->annotation(50, 175, "Lorem Ipsum!");
//
//     $imagick = new \Imagick();
//     $imagick->newImage(500, 500, $backgroundColor);
//     $imagick->setImageFormat("png");
//
//     $imagick->drawImage($draw);
//
//     header("Content-Type: image/png");
//     echo $imagick->getImageBlob();
// }
// setTextUnderColor();
// exit;

$result = Poster::extension('imagick')
    ->config([
        'path' => 'poster/test1.png',
        'font' => __DIR__ . '/../../src/style/simkai.ttf',
        'dpi' => 72
    ])
    ->buildIm(638,826,[255,255,255,127],false)
    ->buildImage('https://test.acyapi.51acy.com/wechat/poster/top_bg.png')
    ->buildImage('https://test.acyapi.51acy.com/wechat/poster/half_circle.png',254,321)
    ->buildImage('https://portrait.gitee.com/uploads/avatars/user/721/2164500_langlanglang_1601019617.png',253,326,0,0,131,131,false,'circle')
    ->buildImage('https://test.acyapi.51acy.com/wechat/poster/fengexian.png',0,655)
    ->buildImage('https://test.acyapi.51acy.com/wechat/qrcode/poster_241.jpg',37,692,0,0,122,122)
    ->buildText('明月几时有，把酒问青天', ['center'], 200, 20, [52, 52, 52, 2],0,'',1,40)
    ->buildText('明月几时有，把酒问青天', ['center'], 300, 20, [52, 52, 52, 2],0,'',1,40)
    ->buildText('苏轼','center',477,16,[51, 51, 51, 1])
    ->buildText('明月几时有，把酒问青天。不知天上宫阙，今夕是何年。','center',515,14,[153, 153, 153, 1])
    ->buildText('长按识别',497,720,15,[153, 153, 153, 1])
    ->buildText('查看TA的更多作品',413,757,15,[153, 153, 153, 1])
    ->buildQr('http://www.baidu.com','20%','20%',0,0,8,2)
    ->stream();

echo "<pre>";
print_r($result);