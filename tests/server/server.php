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



// 验证码
// $result = Captcha::get();
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

$gif = __DIR__ . '/../poster/1223015613610230151165.gif';


Poster::extension('imagick')->buildImDst($gif)
    // ->buildLine(100, 100, 300, 200, [255, 255, 0, 1], '', 10)
    // ->buildImage('https://portrait.gitee.com/uploads/avatars/user/721/2164500_langlanglang_1601019617.png', 253, 126, 0, 0, 131, 131, false, 'circle')
    ->buildText('明月几时有，把酒问青天', ['center'], 20, 20, [52, 52, 52, 2], 0, '', 1, 40)
    ->setPoster();


// $result = Poster::extension('gd')
//     ->config([
//         'path' => __DIR__ . '/../poster/test1.png',
//         // 'font' => 'static/simkai.ttf',
//         // 'dpi' => 72
//     ])
//     ->buildIm(638, 826, [255, 255, 255, 127], false)
//     // ->buildImage('https://test.acyapi.51acy.com/wechat/poster/top_bg.png')
//     // ->buildImage('static/top_bg.png')
//     // ->buildImage('https://test.acyapi.51acy.com/wechat/poster/half_circle.png', 254, 321)
//     // ->buildImage('https://portrait.gitee.com/uploads/avatars/user/721/2164500_langlanglang_1601019617.png', 253, 326, 0, 0, 131, 131, false, 'circle')
//     // ->buildImage('https://test.acyapi.51acy.com/wechat/poster/fengexian.png', 0, 655)
//     // ->buildImage('https://test.acyapi.51acy.com/wechat/qrcode/poster_241.jpg',37,692,0,0,122,122)
//     ->buildText('明月几时有，把酒问青天', ['center'], 200, 20, [52, 52, 52, 2], 0, '', 1, 40)
//     ->buildText('明月几时有，把酒问青天', ['center'], 300, 20, [52, 52, 52, 2], 0, '', 1, 40)
//     ->buildText('苏轼', 'center', 477, 16, [51, 51, 51, 1])
//     ->buildText('明月几时有，把酒问青天。不知天上宫阙，今夕是何年。', 'center', 515, 14, [153, 153, 153, 1])
//     ->buildText('长按识别', 497, 720, 15, [153, 153, 153, 1])
//     ->buildText('查看TA的更多作品', 437, 757, 15, [153, 153, 153, 1])
//     ->buildQr('http://www.baidu.com', 37, 692, 0, 0, 122, 122)
//     ->buildBg(400, 500, ['color' => [
//         [255, 0, 0],
//         [255, 125, 0],
//         [255, 255, 0],
//         [0, 255, 0],
//         [0, 255, 255],
//         [0, 0, 255],
//         [255, 0, 255]
//     ], 'alpha' => 80, 'to' => 'top', 'radius' => '20 30 40 80'], true, 'center', 'center', 0, 0,
//         function ($im) {
//             // $im->buildImage('https://test.acyapi.51acy.com/wechat/poster/top_bg.png');
//             $im->buildLine(10, 100, 100, 200, [0, 0, 0, 1], '', 10);
//             // $im->buildLine(10, 30, 100, 100, [0, 0, 0, 1], 'rectangle', 10);
//             // $im->buildLine(120, 10, 220, 100, [0, 0, 0, 1], 'filled_rectangle', 10);
//             $im->buildArc(200, 200, 50, 50, 0, 360, [0, 0, 0, 1], '', 1);
//             $im->buildText('明月几时有，把酒问青天，不知天上宫阙，今夕是何年', 'center', ['custom', 'center', 0, 100, 0], 20, [0, 0, 0, 50], 0, '', 1, 0);
//             // $im->buildText('明月几时有', ['custom', 'right', 200, 400], ['custom', 'bottom', 200, 500, -20], 20, [0, 0, 0, 50]);
//         })
//     ->getPoster();

