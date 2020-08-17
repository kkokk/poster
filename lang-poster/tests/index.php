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
 * @Last Modified time: 2020-08-17 14:06:54
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
	// $result = PosterManager::poster('poster/poster_user')
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
	// ->getPoster();
	
	//给图片添加水印
	// $result = PosterManager::poster()
	// ->buildImDst(__DIR__.'/test.jpeg')
	// ->buildImage('https://test.acyapi.51acy.com/wechat/qrcode/poster_241.jpg','-20%','-20%',0,0,0,0,false)
	// ->getPoster();

	# 实例化调用 
	// 合成图片
	// $PosterManager = new PosterManager('poster/poster_user');
	// $result = $PosterManager->buildIm(638,826,[255,255,255,127],false)
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
	// ->getPoster();

	//给图片添加水印
	$PosterManager = new PosterManager();
	$result = $PosterManager->buildImDst(__DIR__.'/test.jpeg')
	->buildImage('https://test.acyapi.51acy.com/wechat/qrcode/poster_241.jpg','center','-20%',0,0,0,0,true)
	->getPoster();
	print_r($result);exit;
} catch (Exception $e) {
	echo $e->getMessage();
}