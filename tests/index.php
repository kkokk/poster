<?php
namespace Kkokk\Tests;
use Kkokk\Poster\Exception\Exception;
use Kkokk\Poster\PosterManager;

require '../vendor/autoload.php';
/**
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 10:07:58
 * @Last Modified by:   lang
 * @Last Modified time: 2021-09-09 16:54:47


 */

try {
		
	/**
	 * [buildIm description] 创建画布
	 * @Author   lang
	 * @DateTime 2020-08-14T20:52:41+0800
	 * @param    number                  $w [description] 画布宽
	 * @param    number                  $h [description] 画布高
	 * @param    array                   $rgba [description] 颜色rbga
	 * @param    boolean                 $alpha [description] 是否透明
	 */
	
	/**
	 * [buildImDst description] 创建指定图片为画布
	 * @Author   lang
	 * @DateTime 2020-08-15T11:14:48+0800
	 * @param    [src]                    $src   [description] 图像资源
	 * @param    integer                  $w     [description]
	 * @param    integer                  $h     [description]
	 * @param    array                    $rgba  [description]
	 * @param    boolean                  $alpha [description]
	 * @return   [type]                          [description]
	 */
	/**
	 * [buildImage description] 合成图片
	 * @Author   lang
	 * @DateTime 2020-08-14T20:52:41+0800
	 * @param    [string]                 $src   [description] 路径，支持网络图片（带http或https）
	 * @param    number                   $dst_x [description] 目标x轴 特殊值 center 居中 支持百分比20% 支持自定义  支持正负
	 * @param    number                   $dst_y [description] 目标y轴 特殊值 center 居中 支持百分比20% 支持自定义  支持正负
	 * @param    number                   $src_x [description] 图片x轴
	 * @param    number                   $src_y [description] 图片y轴
	 * @param    number                   $src_w [description] 图片自定义宽
	 * @param    number                   $src_h [description] 图片自定义高
	 * @param    string                   $type  [description] 图片变形类型 'normal' 正常形状 'circle' 圆形
	 * @param    boolean                  $alpha [description] 是否透明 是 true
	 * @return   array                           [description] 返回相对路径,数组
	 */
	
	/**
	 * [buildQr description] 合成二维码
	 * @Author lang
	 * @Date   2020-10-14T12:14:06+0800
	 * @param  [type]                   $text   [内容]
	 * @param  integer                  $dst_x  [目标位置x] 特殊值 center 居中 支持百分比20% 支持自定义  支持正负
	 * @param  integer                  $dst_y  [目标位置y] 特殊值 center 居中 支持百分比20% 支持自定义  支持正负
	 * @param  integer                  $src_x  [图片x轴]
	 * @param  integer                  $src_y  [图片y轴]
	 * @param  integer                  $size   [大小]
	 * @param  integer                  $margin [百变大小]
	 * @return [type]                           [description]
	 */
	
	/**
	 * [buildText description] 合成文字
	 * @Author   lang
	 * @DateTime 2020-08-14T22:09:20+0800
	 * @param    [type]                   $content     [description]
	 * @param    integer                  $dst_x       [description] 
	 * @param    integer                  $dst_y       [description]
	 * @param    integer                  $font        [description] 字体大小
	 * @param    array                    $rgba        [description] 
	 * @param    integer                  $max_w       [description] 最大换行高度
	 * @param    string                   $font_family [description] 字体，可不填，有默认 (相对路径为项目根目录)
	 * @return   [type]                                [description]
	 */
	
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
		   'src' => 'https://test.acyapi.51acy.com/wechat/poster/half_circle.png','dst_x' => 254,'dst_y' => 321
		],
		[
		   'src' => 'https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=2854425629,4097927492&fm=26&gp=0.jpg','dst_x' => 253,'dst_y' => 326,'src_x' => 0,'src_y' => 0,'src_w' => 131,'src_h' => 131,'alpha' => false,'type'  => 'circle'
		],
		[
		   'src'   => 'https://test.acyapi.51acy.com/wechat/poster/fengexian.png','dst_x' => 0,'dst_y' => 655
		]
	];
	$buildTextManyArr  = [
		[
           'content'=> '苏轼','dst_x' => 'center','dst_y' => 477,'font' => 16,'rgba' => [51, 51, 51, 1],'max_w'=> 0,'font_family' => '','weight' => 1,'space'=>20
       ],
       [
           'content'=> '明月几时有，把酒问青天。不知天上宫阙，今夕是何年。','dst_x' => 'center','dst_y' => 515,'font' => 16,'rgba' => [51, 51, 51, 1]
       ],
       [
           'content'=> '我欲乘风归去，又恐琼楼玉宇，高处不胜寒。','dst_x' => 'center','dst_y' => 535,'font' => 16,'rgba' => [51, 51, 51, 1]
       ],
       [
           'content'=> '起舞弄清影，何似在人间。转朱阁，低绮户，照无眠。','dst_x' => 'center','dst_y' => 555,'font' => 16,'rgba' => [51, 51, 51, 1]
       ],
       [
           'content'=> '不应有恨，何事长向别时圆？','dst_x' => 'center','dst_y' => 575,'font' => 16,'rgba' => [51, 51, 51, 1]
       ],
       [
           'content'=> '人有悲欢离合，月有阴晴圆缺，此事古难全。','dst_x' => 'center','dst_y' => 595,'font' => 16,'rgba' => [51, 51, 51, 1]
       ],
       [
           'content'=> '但愿人长久，千里共婵娟。','dst_x' => 'center','dst_y' => 615,'font' => 16,'rgba' => [51, 51, 51, 1]
       ],
       [
           'content'=> '长按识别','dst_x' => 'center','dst_y' => 720,'font' => 16,'rgba' => [51, 51, 51, 1]
       ],
       [
           'content'=> '查看TA的更多作品','dst_x' => 'center','dst_y' => 757,'font' => 16,'rgba' => [51, 51, 51, 1],'max_w'=> 0,'font_family' => '','weight' => 1,'space'=>20
       ]
	];
	$buildQrManyArr    = [
		[
			'text'=>'http://www.520yummy.com','dst_x'=>37,'dst_y'=>692,'src_x'=>0,'src_y'=>0,'src_w'=>0,'src_h'=>0,'size'=>4,'margin'=>1
		],
		[
			'text'=>'http://www.520yummy.com','dst_x'=>481,'dst_y'=>692,'src_x'=>0,'src_y'=>0,'src_w'=>0,'src_h'=>0,'size'=>4,'margin'=>1
		]
	];

	$result = PosterManager::Poster('poster/poster_user')
	->buildIm(638,826,[255,255,255,127],false)
	->buildImageMany($buildImageManyArr)
	->buildTextMany($buildTextManyArr)
	->buildQrMany($buildQrManyArr)
	->getPoster();


	//给图片添加水印
	// $result = PosterManager::poster()
	// ->buildImDst(__DIR__.'/test.jpeg')
	// ->buildImage('https://test.acyapi.51acy.com/wechat/qrcode/poster_241.jpg','-20%','-20%',0,0,0,0,false)
	// ->setPoster();

	# 实例化调用 
	// 合成图片
	// $PosterManager = new PosterManager();
	// $result = $PosterManager->buildIm(638,826,[255,255,255,127],false)
	// ->buildIm(638,826,[255,255,255,127],false)
	// ->buildImage('https://test.acyapi.51acy.com/wechat/poster/top_bg.png')
	// ->buildImage('https://test.acyapi.51acy.com/wechat/poster/half_circle.png',254,321)
	// ->buildImage('https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=2854425629,4097927492&fm=26&gp=0.jpg',253,326,0,0,131,131,false,'circle')
	// ->buildImage('https://test.acyapi.51acy.com/wechat/poster/fengexian.png',0,655)
	// ->buildText('苏轼','center',477,16,[51, 51, 51, 1])
	// ->buildText('明月几时有，把酒问青天。不知天上宫阙，今夕是何年。','center',515,14,[153, 153, 153, 1])
	// ->buildText('长按识别',497,720,15,[153, 153, 153, 1])
	// ->buildText('查看TA的更多作品',413,757,15,[153, 153, 153, 1])
	// ->buildQr('http://www.baidu.com',37,692,0,0,4,1)
	// ->getPoster();

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
     * @param  [type]                   $saveandprint [保存二维码图片并显示出来，$outfile必须传递图片路径]
     * @return [type]                                 [description]
	 */
	# 静态调用
	// $result = PosterManager::Poster()->Qr('http://www.baidu.com','poster/1.png');
	# 实例化调用
	// $PosterManager = new PosterManager();
	// $result = $PosterManager->Qr('http://www.baidu.com','poster/1.png');
	print_r($result);exit;
} catch (Exception $e) {
	echo $e->getMessage();
}