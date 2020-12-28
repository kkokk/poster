# poster

#### 介绍
PHP 图片合成、生成海报、图片添加水印、生成二维码，合成二维码

#### authors
lang
732853989@qq.com

#### 安装或更新教程

1.  composer require kkokk/poster

2.  composer update kkokk/poster

#### 使用说明

1.   *
	 * [buildIm 创建画布] 
	 * [number]                  $w     [ 画布宽 ] 
	 * [number]                  $h     [ 画布高 ] 
	 * [array]                   $rgba  [ 颜色rbga，[255,255,255,1] ] 
	 * [boolean]                 $alpha [ 是否透明，是：true ] 
	 
2.   * [buildImDst 创建指定图片为画布] 
	 * [source]                   $src   [ 图像资源 ] 
	 * [integer]                  $w     [ 画布宽，默认原图宽 ]
	 * [integer]                  $h     [ 画布高，默认原图高 ]
	 * [array]                    $rgba  [ 颜色rbga，[255,255,255,1] ]
	 * [boolean]                  $alpha [ 是否透明，是：true ]

3.   * [buildImage 合成图片] 
	 * [string]                   $src   [ 路径，支持网络图片（带http或https） ] 
	 * [number]                   $dst_x [ 目标x轴 特殊值 center 居中 支持百分比20% 支持自定义  支持正负 ] 
	 * [number]                   $dst_y [ 目标y轴 特殊值 center 居中 支持百分比20% 支持自定义  支持正负 ] 
	 * [number]                   $src_x [ 图片x轴，默认0 ] 
	 * [number]                   $src_y [ 图片y轴，默认0 ] 
	 * [number]                   $src_w [ 图片自定义宽，默认原宽 ] 
	 * [number]                   $src_h [ 图片自定义高，默认原高 ] 
	 * [boolean]                  $alpha [ 是否透明，true：是 ] 
     * [string]                   $type  [ 图片变形类型，正常形状：'normal'，圆形：'circle'  ] 

4.   * [buildQr description] 合成二维码
	 * [string]                   $text   [ 内容，例如：http://www.520yummy.com ]
	 * [integer]                  $dst_x  [ 画布位置x 特殊值 center 居中 支持百分比20% 支持自定义  支持正负 ] 
	 * [integer]                  $dst_y  [ 画布位置y 特殊值 center 居中 支持百分比20% 支持自定义  支持正负 ] 
	 * [integer]                  $src_x  [ 图片x轴，默认0 ]
	 * [integer]                  $src_y  [ 图片y轴，默认0 ]
	 * [integer]                  $src_w  [ 图片自定义宽，默认原宽 ]
	 * [integer]                  $src_h  [ 图片自定义高，默认原高 ]
	 * [integer]                  $size   [ 大小，默认4 ]
	 * [integer]                  $margin [ 百变大小，默认1 ]

5.   * [buildText 合成文字] 
	 * [string]                   $content     [ 文字内容]
	 * [integer]                  $dst_x       [ 画布位置x，默认0 ] 
	 * [integer]                  $dst_y       [ 画布位置y，默认0 ]
	 * [integer]                  $font        [ 字体大小，默认16 ] 
	 * [array]                    $rgba        [ 颜色rbga，[255,255,255,1] ] 
	 * [integer]                  $max_w       [ 最大换行宽度，默认0 ] 
	 * [string]                   $font_family [ 字体，可不填，有默认 (相对路径为项目根目录) ]

6.   * [getPoster 获取合成后图片文件地址]
	 * @return   [array]                   [返回文件地址] 

7.   * [setPoster 处理图片，需要传原图片]

8.   * [Qr 生成二维码]
     * [string]                   $text         [ 二维码包含的内容，可以是链接、文字、json字符串等等 ]
     * [boolean|string]           $outfile      [ 默认为false，不生成文件，只将二维码图片返回输出；否则需要给出存放生成二维码图片的文件名及路径 ]
     * [string]                   $level        [ 容错级别，默认为L， 可传递的值分别是L(QR_ECLEVEL_L，7%)、M(QR_ECLEVEL_M，15%)、Q(QR_ECLEVEL_Q，25%)、H(QR_ECLEVEL_H，30%)。这个参数控制二维码容错率，不同的参数表示二维码可被覆盖的区域百分比，也就是被覆盖的区域还能识别 ] 
     * [integer]                  $size         [ 控制生成图片的大小，默认为4 ]
     * [integer]                  $margin       [ 控制生成二维码的空白区域大小，默认4 ]
     * [boolean]                  $saveandprint [ 保存二维码图片并显示出来，$outfile必须传递图片路径，默认false ]
	 
#### 静态调用
	use Kkokk\Poster\PosterManager;
	use Kkokk\Poster\Exception\Exception;
	# 合成图片
	try {
		$result = PosterManager::Poster('poster/poster_user') //生成海报，这里写保存路径和文件名，可以指定图片后缀。默认png
		->buildIm(638,826,[255,255,255,127],false)
		->buildImage('https://test.acyapi.51acy.com/wechat/poster/top_bg.png')
		->buildImage('https://test.acyapi.51acy.com/wechat/poster/half_circle.png',254,321)
		->buildImage('https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=2854425629,4097927492&fm=26&gp=0.jpg',253,326,0,0,131,131,false,'circle')
		->buildImage('https://test.acyapi.51acy.com/wechat/poster/fengexian.png',0,655)
		->buildText('苏 轼','center',477,16,[51, 51, 51, 1])
		->buildText('明月几时有，把酒问青天。不知天上宫阙，今夕是何年。','center',515,14,[153, 153, 153, 1])
		->buildText('我欲乘风归去，又恐琼楼玉宇，高处不胜寒。','center',535,14,[153, 153, 153, 1])
		->buildText('起舞弄清影，何似在人间。转朱阁，低绮户，照无眠。','center',555,14,[153, 153, 153, 1])
		->buildText('不应有恨，何事长向别时圆？','center',575,14,[153, 153, 153, 1])
		->buildText('人有悲欢离合，月有阴晴圆缺，此事古难全。','center',595,14,[153, 153, 153, 1])
		->buildText('但愿人长久，千里共婵娟。','center',615,14,[153, 153, 153, 1])
		->buildText('长按识别',497,720,15,[153, 153, 153, 1])
		->buildText('查看TA的更多作品',413,757,15,[153, 153, 153, 1])
		->buildQr('http://www.520yummy.com',37,692,0,0,0,0,4,1)
		->getPoster();

		# 给图片添加水印
		$result = PosterManager::Poster() //给指定图片添加水印，这里为空就好
		->buildImDst(__DIR__.'/test.jpeg')
		->buildImage('https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=2854425629,4097927492&fm=26&gp=0.jpg','-20%','-20%',0,0,0,0,false)
		->setPoster();

		# 生成二维码
		$result = PosterManager::Poster()->Qr('http://www.baidu.com','poster/1.png');
	} catch (Exception $e){
		echo $e->getMessage();
	}
#### 实例化调用
	use Kkokk\Poster\PosterManager;
	use Kkokk\Poster\Exception\Exception;
	# 合成图片
	try {
		$PosterManager = new PosterManager('poster/poster_user'); //生成海报，这里写保存路径和文件名，可以指定图片后缀。默认png
		$result = $PosterManager->buildIm(638,826,[255,255,255,127],false)
		->buildIm(638,826,[255,255,255,127],false)
		->buildImage('https://test.acyapi.51acy.com/wechat/poster/top_bg.png')
		->buildImage('https://test.acyapi.51acy.com/wechat/poster/half_circle.png',254,321)
		->buildImage('https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=2854425629,4097927492&fm=26&gp=0.jpg',253,326,0,0,131,131,false,'circle')
		->buildImage('https://test.acyapi.51acy.com/wechat/poster/fengexian.png',0,655)
		->buildText('苏 轼','center',477,16,[51, 51, 51, 1])
		->buildText('明月几时有，把酒问青天。不知天上宫阙，今夕是何年。','center',515,14,[153, 153, 153, 1])
		->buildText('我欲乘风归去，又恐琼楼玉宇，高处不胜寒。','center',535,14,[153, 153, 153, 1])
		->buildText('起舞弄清影，何似在人间。转朱阁，低绮户，照无眠。','center',555,14,[153, 153, 153, 1])
		->buildText('不应有恨，何事长向别时圆？','center',575,14,[153, 153, 153, 1])
		->buildText('人有悲欢离合，月有阴晴圆缺，此事古难全。','center',595,14,[153, 153, 153, 1])
		->buildText('但愿人长久，千里共婵娟。','center',615,14,[153, 153, 153, 1])
		->buildText('长按识别',497,720,15,[153, 153, 153, 1])
		->buildText('查看TA的更多作品',413,757,15,[153, 153, 153, 1])
		->buildQr('http://www.520yummy.com',37,692,0,0,0,0,4,1)
		->getPoster();

		# 给图片添加水印
		$PosterManager = new PosterManager(); //给指定图片添加水印，这里为空就好
		$result = $PosterManager->buildImDst(__DIR__.'/test.jpeg')
		->buildImage('https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=2854425629,4097927492&fm=26&gp=0.jpg','center','-20%',0,0,0,0,true)
		->setPoster();

		# 生成二维码
		$result = $PosterManager->Qr('http://www.baidu.com','poster/1.png');

	} catch (Exception $e){
		echo $e->getMessage();
	}
