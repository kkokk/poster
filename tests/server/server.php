<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/27
 * Time: 10:35
 */

use Kkokk\Poster\Facades\Poster;
use Kkokk\Poster\Facades\Captcha;
use Kkokk\Poster\PosterManager;

require '../../vendor/autoload.php';

$startAt = microtime(true);
// echo $startAt . PHP_EOL;

// $msg1 = ['type' => 'html', 'content' => '我是<br><span style="color:#fF8716;">某某</span>，<br/>我在<span style="color:#fF8716;">贵阳</span><br>我为家乡助力<br>我是<br>第<span style="color:#fF8716;font-size: 20px;">0</span>位岑巩好物代言人，海报生成图片验证极速生成方便快捷快速生成海报'];
// // $msg1 = '我是某某，我在贵阳，我为家乡助力，我是第336位岑巩好物代言人，海报生成图片验证极速生成方便快捷快速生成海报';
// // $msg2 = ['type' => 'html', 'content' => '我是某某，我在贵阳，我为家乡助力，我是第336位岑巩好物代言人，海报生成图片验证极速生成方便快捷快速生成海报'];
//
// Poster::extension('gd')
//     ->config([
//         'path' => __DIR__ . '/../poster/text1.png'
//     ])
//     ->buildIm(638, 826, [255, 255, 255, 127], false)
//     ->buildText($msg1, ['center'], 200, 20, [52, 52, 52, 1], 300, '', 1, 0)
//     ->getPoster();
// exit;
// Poster::extension('imagick')
//     ->config([
//         'path' => __DIR__ . '/../poster/text2.png'
//     ])
//     ->buildIm(638, 826, [255, 255, 255, 127], false)
//     ->buildText($msg2, ['center'], 200, 20, [52, 52, 52, 1], 300, '', 1, 0)
//     ->getPoster();

// Poster::extension('gd')
//     ->config([
//         'path' => __DIR__ . '/../poster/text3.png'
//     ])
//     ->buildIm(638, 826, [255, 255, 255, 127], false)
//     ->buildText($msg1, ['center'], 200, 20, [52, 52, 52, 1], 300, '', 1, 0)
//     ->getPoster();

// Poster::extension('gd')
//     ->config([
//         'path' => __DIR__ . '/../poster/text4.png'
//     ])
//     ->buildIm(638, 826, [255, 255, 255, 127], false)
//     ->buildText($msg2, ['center'], 200, 20, [52, 52, 52, 1], 300, '', 1, 0)
//     ->getPoster();
// 验证码
// $result = Captcha::type('click')->get();
// echo <<<EOF
//     <img src="{$result['img']}" >
// EOF;
// exit;

// 加水印
// $result = Poster::extension('imagick')
//     ->config([
//         'path' => 'poster/test1.png',
//         'font' => __DIR__ . '/../../src/style/simkai.ttf',
//     ])
//     ->buildImDst('../poster/click.png')
//     ->buildText('加一个水印', 5, 55, 16, [255, 255, 255, 50], 0, '', 1, 0, 315)
//     ->buildText('加一个水印', -15, 125, 16, [255, 255, 255, 50], 0, '', 1, 0, 315)
//     ->buildText('加一个水印', 55, 55, 16, [255, 255, 255, 50], 0, '', 1, 0, 315)
//     ->stream();
// echo $result;
// exit;


$result = Poster::extension('imagick')
    ->config([
        'path' => __DIR__ . '/../poster/test1.png',
        // 'font' => 'static/simkai.ttf',
        // 'dpi' => 72
    ])
    ->buildIm(638, 826, [41, 43, 48, 127], false)
    // ->buildImage('https://test.acyapi.51acy.com/wechat/poster/top_bg.png')
    // ->buildImage('static/top_bg.png')
    // ->buildImage('https://test.acyapi.51acy.com/wechat/poster/half_circle.png', 254, 321)
    ->buildImage([
        // 'src' => __DIR__ . '/../poster/1689560381.png',
        'src' => 'https://img.zmtc.com/2019/1220/20191220080912614.jpg',
        'angle' => 420
    ], 0,0, 0, 0, 360, 640)
    ->buildImage([
        'src' => 'https://portrait.gitee.com/uploads/avatars/user/721/2164500_langlanglang_1601019617.png',
        'angle' => 80
    ], 253, 326, 0, 0, 131, 131, false, 'circle')
    // ->buildImage('https://test.acyapi.51acy.com/wechat/poster/fengexian.png', 0, 655)
    // ->buildImage('https://test.acyapi.51acy.com/wechat/qrcode/poster_241.jpg',37,692,0,0,122,122)
    // ->buildText('明月几时有，把酒问青天', ['center'], 200, 20, [52, 52, 52, 2], 0, '', 1, 40)
    // ->buildText('明月几时有，把酒问青天', ['center'], 300, 20, [52, 52, 52, 2], 0, '', 1, 40)
    ->buildText('苏轼', 'center', 477, 16, [255, 255, 255, 1])
    ->buildText('明月几时有，把酒问青天。不知天上宫阙，今夕是何年。', 'center', 515, 14, [255, 255, 255, 1])
    // ->buildText('长按识别', 497, 720, 15, [153, 153, 153, 1])
    // ->buildText('查看TA的更多作品', 437, 757, 15, [153, 153, 153, 1])
    // ->buildQr('http://www.baidu.com', 37, 692, 0, 0, 122, 122)
    // ->buildBg(400, 500, ['color' => [
    //     [255, 0, 0],
    //     [255, 125, 0],
    //     [255, 255, 0],
    //     [0, 255, 0],
    //     [0, 255, 255],
    //     [0, 0, 255],
    //     [255, 0, 255]
    // ], 'alpha' => 80, 'to' => 'top', 'radius' => '20 30 40 80'], true, 'center', 'center', 0, 0,
    //     function ($im) {
    //         // $im->buildImage('https://test.acyapi.51acy.com/wechat/poster/top_bg.png');
    //         $im->buildLine(10, 100, 100, 200, [0, 0, 0, 1], '', 10);
    //         // $im->buildLine(10, 30, 100, 100, [0, 0, 0, 1], 'rectangle', 10);
    //         // $im->buildLine(120, 10, 220, 100, [0, 0, 0, 1], 'filled_rectangle', 10);
    //         $im->buildArc(200, 200, 50, 50, 0, 360, [0, 0, 0, 1], '', 1);
    //         $im->buildText('明月几时有，把酒问青天，不知天上宫阙，今夕是何年', 'center', ['custom', 'center', 0, 100, 0], 20, [0, 0, 0, 50], 0, '', 1, 0);
    //         // $im->buildText('明月几时有', ['custom', 'right', 200, 400], ['custom', 'bottom', 200, 500, -20], 20, [0, 0, 0, 50]);
    //     })
    ->stream();

// echo (memory_get_usage() / 1024 / 1024) . 'M' . PHP_EOL;
// echo (memory_get_peak_usage() / 1024 / 1024) . 'M' . PHP_EOL;
// print_r(getrusage()) . PHP_EOL;
//
// $endAt = microtime(true);
// echo $endAt . PHP_EOL;
// echo $endAt - $startAt . PHP_EOL;