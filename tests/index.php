<?php

namespace Kkokk\Tests;

use Kkokk\Poster\Exception\Exception;
use Kkokk\Poster\PosterManager;
use Kkokk\Poster\Facades\Poster;
use Kkokk\Poster\Facades\Captcha;

require '../vendor/autoload.php';
/**
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 10:07:58
 * @Last Modified by:   lang
 * @Last Modified time: 2021-09-09 16:54:47
 */

try {
    // $result = Poster::extension()->Qr('http://www.baidu.com','poster/1.png', 'L', 4, 1, 1);
    // var_dump($result);
    // exit;
    // var_dump(Poster::extension()->config(['path'=>'poster/avatar1.png','font_family'=> __DIR__ . '/../src/style/simkai.ttf'])
    //     ->buildIm(400,400,[255,255,255, 1],true)
    //     ->buildText('Poster', ['center', 22], 'center', 108, [52, 52, 52, 1], '', '', 20)
    //     ->getPoster()
    // );
    // exit;
    // echo microtime().PHP_EOL;
    // $json = '[{"x":186,"y":182},{"x":94,"y":157}]';
    // $secret = '[{"contents":"\u7ea2","point":[157,169,185,215,215,197,187,151,-58]},{"contents":"\u70e7","point":[61,150,95,193,123,172,89,129,-51]}]';
    // $data = PosterManager::Captcha()->type('click')->check(0, json_decode($json, true), 0, $secret);
    // var_dump($data);
    // exit;
    // $data = PosterManager::Captcha()->get();
    // $data = Captcha::type()->config(['slider_bg'=>-1])->get();
    // $data = Captcha::type('input')->config(['contents'=>'红烧'])->get();
    // // $data = PosterManager::Captcha()->type('input')->config(['src'=>__DIR__.'/../src/style/slider_bg/layer01.jpg'])->get();
    // $data = PosterManager::Captcha()->type('click')->config(['contents'=>'红烧'])->get();
    // $data = PosterManager::Captcha()->type('input')->config(['type'=>'number'])->get();
    // $data = PosterManager::Captcha()->type('input')->config(['type'=>'alpha_num'])->get();
    // $data = PosterManager::Captcha()->type('input')->config(['type'=>'text'])->get();
    // $data = PosterManager::Captcha()->type('input')->config(['type'=>'math'])->get();
    // $data = PosterManager::Captcha()->type('rotate')->get();

    // print_r($data['img']);

    // echo (memory_get_usage() / 1024 / 1024).'M'.PHP_EOL;
    // echo microtime().PHP_EOL;
    // echo (memory_get_peak_usage() / 1024 / 1024).'M'.PHP_EOL;
    // print_r(getrusage()).PHP_EOL;
    // exit;

    # 静态调用
    // 合成图片
    // $result = PosterManager::Poster('poster/poster_user')
    // ->buildIm(638,826,[255,255,255,127],false)
    // ->buildImage('https://test.acyapi.51acy.com/wechat/poster/top_bg.png')
    // ->buildImage('https://test.acyapi.51acy.com/wechat/poster/half_circle.png',254,321)
    // ->buildImage('https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=2854425629,4097927492&fm=26&gp=0.jpg',253,326,0,0,131,131,false,'circle')
    // ->buildImage('https://test.acyapi.51acy.com/wechat/poster/fengexian.png',0,655)
    // ->buildImage('https://test.acyapi.51acy.com/wechat/qrcode/poster_241.jpg',37,692,0,0,122,122)
    // ->buildText('苏轼','center',477,16,[51, 51, 51, 1])
    // ->buildText('明月几时有，把酒问青天。不知天上宫阙，今夕是何年。','center',515,14,[153, 153, 153, 1])
    // ->buildText('长按识别',497,720,15,[153, 153, 153, 1])
    // ->buildText('查看TA的更多作品',413,757,15,[153, 153, 153, 1])
    // ->buildQr('http://www.baidu.com','20%','20%',0,0,8,2)
    // ->getPoster();


    # 批量合成
    $buildImageManyArr = [
        [
            'src' => 'https://test.acyapi.51acy.com/wechat/poster/top_bg.png'
        ],
        [
            'src' => 'https://test.acyapi.51acy.com/wechat/poster/half_circle.png', 'dst_x' => 254, 'dst_y' => 321
        ],
        [
            'src' => 'https://portrait.gitee.com/uploads/avatars/user/721/2164500_langlanglang_1601019617.png', 'dst_x' => 253, 'dst_y' => 326, 'src_x' => 0, 'src_y' => 0, 'src_w' => 131, 'src_h' => 131, 'alpha' => false, 'type' => 'circle'
        ],
        [
            'src' => 'https://test.acyapi.51acy.com/wechat/poster/fengexian.png', 'dst_x' => 0, 'dst_y' => 655
        ]
    ];
    $buildTextManyArr = [
        [
            'content' => '苏轼', 'dst_x' => 'center', 'dst_y' => 477, 'font' => 16, 'rgba' => [51, 51, 51, 1], 'max_w' => 0, 'font_family' => '', 'weight' => 1, 'space' => 20
        ],
        [
            'content' => '明月几时有，把酒问青天。不知天上宫阙，今夕是何年。', 'dst_x' => 'center', 'dst_y' => 515, 'font' => 16, 'rgba' => [51, 51, 51, 1]
        ],
        [
            'content' => '我欲乘风归去，又恐琼楼玉宇，高处不胜寒。', 'dst_x' => 'center', 'dst_y' => 535, 'font' => 16, 'rgba' => [51, 51, 51, 1]
        ],
        [
            'content' => '起舞弄清影，何似在人间。转朱阁，低绮户，照无眠。', 'dst_x' => 'center', 'dst_y' => 555, 'font' => 16, 'rgba' => [51, 51, 51, 1]
        ],
        [
            'content' => '不应有恨，何事长向别时圆？', 'dst_x' => 'center', 'dst_y' => 575, 'font' => 16, 'rgba' => [51, 51, 51, 1]
        ],
        [
            'content' => '人有悲欢离合，月有阴晴圆缺，此事古难全。', 'dst_x' => 'center', 'dst_y' => 595, 'font' => 16, 'rgba' => [51, 51, 51, 120]
        ],
        [
            'content' => '但愿人长久，千里共婵娟。', 'dst_x' => 'center', 'dst_y' => 615, 'font' => 16, 'rgba' => [51, 51, 51, 1]
        ],
        [
            'content' => '长按识别', 'dst_x' => 'center', 'dst_y' => 720, 'font' => 16, 'rgba' => [51, 51, 51, 1]
        ],
        [
            'content' => '查看TA的更多作品', 'dst_x' => 'center', 'dst_y' => 757, 'font' => 16, 'rgba' => [51, 51, 51, 1], 'max_w' => 0, 'font_family' => '', 'weight' => 1, 'space' => 20
        ]
    ];
    $buildQrManyArr = [
        [
            'text' => 'http://www.520yummy.com', 'dst_x' => 37, 'dst_y' => 692, 'src_x' => 0, 'src_y' => 0, 'src_w' => 0, 'src_h' => 0, 'size' => 4, 'margin' => 1
        ],
        [
            'text' => 'http://www.520yummy.com', 'dst_x' => 481, 'dst_y' => 692, 'src_x' => 0, 'src_y' => 0, 'src_w' => 0, 'src_h' => 0, 'size' => 4, 'margin' => 1
        ]
    ];

    // $result = PosterManager::Poster('poster/poster_user')
    // ->buildIm(638,826,[255,255,255,1],true)
    // ->buildImageMany($buildImageManyArr)
    // ->buildTextMany($buildTextManyArr)
    // ->buildQrMany($buildQrManyArr)
    // ->buildBg(638,826,[0,0,0,50],true)
    // ->buildText('明月几时有，把酒问青天。不知天上宫阙，今夕是何年。','center',100,32,[255, 255, 255, 1])
    // ->getPoster();
    $startAt = microtime(true);
    echo $startAt . PHP_EOL;
    // $result = PosterManager::Poster('poster/poster_user')
    //     ->buildIm(638,826,[255,255,255,1],true)
    //     ->buildImageMany($buildImageManyArr)
    //     ->buildTextMany($buildTextManyArr)
    //     ->buildQrMany($buildQrManyArr)
    //     ->buildBg(638,826,['color'=>[[0,0,162], [0,255,162], [255,255,162], [255, 0, 0], [0, 255, 0]], 'alpha'=>50, 'to'=>'bottom'],true)
    //     ->buildText('明月几时有，把酒问青天。不知天上宫阙，今夕是何年。','center',100,32,[255, 255, 255, 1])
    //     ->getPoster();


    //给图片添加水印
    // $result = PosterManager::poster()
    // ->buildImDst(__DIR__.'/poster/poster_user.png')
    // ->buildImage('https://test.acyapi.51acy.com/wechat/qrcode/poster_241.jpg','25%','65%',0,0,0,0,false)
    // ->setPoster();

    # 实例化调用
    // 合成图片
    // $PosterManager = PosterManager::Poster('poster/test1');//new PosterManager('poster/test1');
    // $result = $PosterManager
    //     // ->buildIm(638,826,[255,255,255,127],false)
    //     // ->buildImageMany($buildImageManyArr)
    //     // ->buildTextMany($buildTextManyArr)
    //     // ->buildQrMany($buildQrManyArr)
    //     // ->config(['path'=>'poster/test1.png','font_family'=> __DIR__ . '/../src/style/simkai.ttf'])

    //     ->buildBg(400, 500, ['color' => [[255, 0, 0],
    //         [255, 125, 0],
    //         [255, 255, 0],
    //         [0, 255, 0],
    //         [0, 255, 255],
    //         [0, 0, 255],
    //         [255, 0, 255]], 'alpha' => 1, 'to' => 'left top', 'radius' => '0'], true, 'center', 'center', 0, 0,
    //         function ($im) {
    //             $im->buildLine(10, 100, 100, 100, [0, 0, 0, 1]);
    //             // $im->buildLine(10, 30, 100, 100, [0, 0, 0, 1], 'rectangle', 10);
    //             // $im->buildLine(120, 10, 220, 100, [0, 0, 0, 1], 'filled_rectangle', 10);
    //             // $im->buildArc(200, 200, 50, 50, 0, 360, [0, 0, 0, 1], 'filled_arc', 1);
    //             $im->buildText('明月几时有，把酒问青天，不知天上宫阙，今夕是何年', 'center', ['custom', 'center', 0, 100, 0], 20, [0, 0, 0, 50], 0, '', 1, 0);
    //             // $im->buildText('明月几时有', ['custom', 'right', 200, 400], ['custom', 'bottom', 200, 500, -20], 20, [0, 0, 0, 50]);
    //         })
    //     ->buildBg(400, 500, ['color' => [[255, 0, 0],
    //         [255, 125, 0],
    //         [255, 255, 0],
    //         [0, 255, 0],
    //         [0, 255, 255],
    //         [0, 0, 255],
    //         [255, 0, 255]], 'alpha' => 1, 'to' => 'left top', 'radius' => '0'], true, 'center', 'center', 0, 0,
    //         function ($im) {
    //             $im->buildLine(10, 100, 100, 100, [0, 0, 0, 1]);
    //             // $im->buildLine(10, 30, 100, 100, [0, 0, 0, 1], 'rectangle', 10);
    //             // $im->buildLine(120, 10, 220, 100, [0, 0, 0, 1], 'filled_rectangle', 10);
    //             // $im->buildArc(200, 200, 50, 50, 0, 360, [0, 0, 0, 1], 'filled_arc', 1);
    //             $im->buildText('明月几时有，把酒问青天，不知天上宫阙，今夕是何年', 'center', ['custom', 'center', 0, 100, 0], 20, [0, 0, 0, 50], 0, '', 1, 0);
    //             // $im->buildText('明月几时有', ['custom', 'right', 200, 400], ['custom', 'bottom', 200, 500, -20], 20, [0, 0, 0, 50]);
    //         })
    //     ->buildBg(400, 500, ['color' => [[255, 0, 0],
    //         [255, 125, 0],
    //         [255, 255, 0],
    //         [0, 255, 0],
    //         [0, 255, 255],
    //         [0, 0, 255],
    //         [255, 0, 255]], 'alpha' => 1, 'to' => 'left top', 'radius' => '0'], true, 'center', 'center', 0, 0,
    //         function ($im) {
    //             $im->buildLine(10, 100, 100, 100, [0, 0, 0, 1]);
    //             // $im->buildLine(10, 30, 100, 100, [0, 0, 0, 1], 'rectangle', 10);
    //             // $im->buildLine(120, 10, 220, 100, [0, 0, 0, 1], 'filled_rectangle', 10);
    //             // $im->buildArc(200, 200, 50, 50, 0, 360, [0, 0, 0, 1], 'filled_arc', 1);
    //             $im->buildText('明月几时有，把酒问青天，不知天上宫阙，今夕是何年', 'center', ['custom', 'center', 0, 100, 0], 20, [0, 0, 0, 50], 0, '', 1, 0);
    //             // $im->buildText('明月几时有', ['custom', 'right', 200, 400], ['custom', 'bottom', 200, 500, -20], 20, [0, 0, 0, 50]);
    //         })
    //     ->buildBg(400, 500, ['color' => [[255, 0, 0],
    //         [255, 125, 0],
    //         [255, 255, 0],
    //         [0, 255, 0],
    //         [0, 255, 255],
    //         [0, 0, 255],
    //         [255, 0, 255]], 'alpha' => 1, 'to' => 'left top', 'radius' => '0'], true, 'center', 'center', 0, 0,
    //         function ($im) {
    //             $im->buildLine(10, 100, 100, 100, [0, 0, 0, 1]);
    //             // $im->buildLine(10, 30, 100, 100, [0, 0, 0, 1], 'rectangle', 10);
    //             // $im->buildLine(120, 10, 220, 100, [0, 0, 0, 1], 'filled_rectangle', 10);
    //             // $im->buildArc(200, 200, 50, 50, 0, 360, [0, 0, 0, 1], 'filled_arc', 1);
    //             $im->buildText('明月几时有，把酒问青天，不知天上宫阙，今夕是何年', 'center', ['custom', 'center', 0, 100, 0], 20, [0, 0, 0, 50], 0, '', 1, 0);
    //             // $im->buildText('明月几时有', ['custom', 'right', 200, 400], ['custom', 'bottom', 200, 500, -20], 20, [0, 0, 0, 50]);
    //         })
    //     ->buildBg(400, 500, ['color' => [[255, 0, 0],
    //         [255, 125, 0],
    //         [255, 255, 0],
    //         [0, 255, 0],
    //         [0, 255, 255],
    //         [0, 0, 255],
    //         [255, 0, 255]], 'alpha' => 1, 'to' => 'left top', 'radius' => '0'], true, 'center', 'center', 0, 0,
    //         function ($im) {
    //             $im->buildLine(10, 100, 100, 100, [0, 0, 0, 1]);
    //             // $im->buildLine(10, 30, 100, 100, [0, 0, 0, 1], 'rectangle', 10);
    //             // $im->buildLine(120, 10, 220, 100, [0, 0, 0, 1], 'filled_rectangle', 10);
    //             // $im->buildArc(200, 200, 50, 50, 0, 360, [0, 0, 0, 1], 'filled_arc', 1);
    //             $im->buildText('明月几时有，把酒问青天，不知天上宫阙，今夕是何年', 'center', ['custom', 'center', 0, 100, 0], 20, [0, 0, 0, 50], 0, '', 1, 0);
    //             // $im->buildText('明月几时有', ['custom', 'right', 200, 400], ['custom', 'bottom', 200, 500, -20], 20, [0, 0, 0, 50]);
    //         })
    //     ->getPoster();

    $result = Poster::extension('gd')
        // ->config(['path'=>'poster/test1.png', 'font_family'=> __DIR__ . '/../src/style/simkai.ttf'])
        // ->buildIm(638,826,[255,255,255,1],true)
        // ->buildImageMany($buildImageManyArr)
        // ->buildTextMany($buildTextManyArr)
        // ->buildText('啊实打实大所大所大所多', 100, 200, 20, [255, 255, 255, 1])
        // ->buildQrMany($buildQrManyArr)
        ->buildBg(638, 826, ['color' => [[255, 0, 0],
            [255, 125, 0],
            [255, 255, 0],
            [0, 255, 0],
            [0, 255, 255],
            [0, 0, 255],
            [255, 0, 255]], 'alpha' => 50, 'to' => 'left top', 'radius' => '40'], true, 'center', 'center', 0, 0,
            function ($im) use($buildImageManyArr, $buildTextManyArr, $buildQrManyArr) {
                $im->buildLine(10, 100, 100, 100, [0, 0, 0, 1]);
                $im->buildImageMany($buildImageManyArr);
                $im->buildTextMany($buildTextManyArr);
                $im->buildQrMany($buildQrManyArr);
                // $im->buildLine(10, 30, 100, 100, [0, 0, 0, 1], 'rectangle', 10);
                // $im->buildLine(120, 10, 220, 100, [0, 0, 0, 1], 'filled_rectangle', 10);
                // $im->buildArc(200, 200, 50, 50, 0, 360, [0, 0, 0, 1], 'filled_arc', 1);
                $im->buildText('明月几时有，把酒问青天，不知天上宫阙，今夕是何年', 'center', ['custom', 'center', 0, 100, 0], 20, [0, 0, 0, 50], 0, '', 10, 25);
                // $im->buildText('明月几时有', ['custom', 'right', 200, 400], ['custom', 'bottom', 200, 500, -20], 20, [0, 0, 0, 50]);
            })
        ->getPoster();
    echo (memory_get_usage() / 1024 / 1024) . 'M' . PHP_EOL;
    echo (memory_get_peak_usage() / 1024 / 1024) . 'M' . PHP_EOL;
    print_r(getrusage()) . PHP_EOL;

    $endAt = microtime(true);
    echo $endAt . PHP_EOL;
    echo $endAt - $startAt . PHP_EOL;

    //给图片添加水印
    // $PosterManager = new PosterManager();
    // $result = $PosterManager->buildImDst(__DIR__.'/test.jpeg')
    // ->buildImage('https://test.acyapi.51acy.com/wechat/qrcode/poster_241.jpg','center','-20%',0,0,0,0,true)
    // ->setPoster();

    //生成二维码
    /**
     * [Qr description]
     * @Author lang
     * @Date   2020-10-14T10:59:28+0800
     * @param  [type]                   $text         [二维码包含的内容，可以是链接、文字、json字符串等等]
     * @param  [type]                   $outfile      [默认为false，不生成文件，只将二维码图片返回输出；否则需要给出存放生成二维码图片的文件名及路径]
     * @param  [type]                   $level        [容错级别，默认为L]
     *      可传递的值分别是L(QR_ECLEVEL_L，7%)、M(QR_ECLEVEL_M，15%)、Q(QR_ECLEVEL_Q，25%)、H(QR_ECLEVEL_H，30%)。
     *      这个参数控制二维码容错率，不同的参数表示二维码可被覆盖的区域百分比，也就是被覆盖的区域还能识别
     * @param  [type]                   $size         [控制生成图片的大小，默认为4]
     * @param  [type]                   $margin       [控制生成二维码的空白区域大小]
     * @param  [type]                   $saveAndPrint [保存二维码图片并显示出来，$outfile必须传递图片路径]
     * @return [type]                                 [description]
     */
    # 静态调用
    // $result = PosterManager::Poster()->Qr('http://www.baidu.com','poster/1.png');
    # 实例化调用
    // $PosterManager = new PosterManager();
    // $result = $PosterManager->Qr('http://www.baidu.com','poster/1.png', 'L', 4, 1, 1);
    print_r($result);
    exit;
} catch (Exception $e) {
    echo $e->getMessage();
}