# poster

#### 介绍
PHP 图片合成、生成海报、图片添加水印

#### authors
lang
732853989@qq.com

#### 安装教程

1.  composer require kkokk\poster

#### 使用说明

1.   *
	 * [buildIm 创建画布] 
	 * @Author   lang
	 * @DateTime 2020-08-14T20:52:41+0800
	 * @param    number                  $w     [ 画布宽 ] 
	 * @param    number                  $h     [ 画布高 ] 
	 * @param    array                   $rgba  [ 颜色rbga ] 
	 * @param    boolean                 $alpha [ 是否透明 ] 
	 
2.   * [buildImDst 创建指定图片为画布] 
	 * @Author   lang
	 * @DateTime 2020-08-15T11:14:48+0800
	 * @param    [src]                    $src   [ 图像资源 ] 
	 * @param    integer                  $w     [description]
	 * @param    integer                  $h     [description]
	 * @param    array                    $rgba  [description]
	 * @param    boolean                  $alpha [description]
	 * @return   [type]                          [description]

3.   * [buildImage 合成图片] 
	 * @Author   lang
	 * @DateTime 2020-08-14T20:52:41+0800
	 * @param    [string]                 $src   [ 路径，支持网络图片（带http或https） ] 
	 * @param    number                   $dst_x [ 目标x轴 特殊值 center 居中 支持百分比20% 支持自定义  支持正负 ] 
	 * @param    number                   $dst_y [ 目标y轴 特殊值 center 居中 支持百分比20% 支持自定义  支持正负 ] 
	 * @param    number                   $src_x [ 图片x轴 ] 
	 * @param    number                   $src_y [ 图片y轴 ] 
	 * @param    number                   $src_w [ 图片自定义宽 ] 
	 * @param    number                   $src_h [ 图片自定义高 ] 
	 * @param    boolean                  $alpha [ 是否透明 是 true ] 
     * @param    string                   $type  [ 图片变形类型 'normal' 正常形状 'circle' 圆形 ] 
	 * @return   array                           [ 返回相对路径,数组 ] 

4.   * [buildText 合成文字] 
	 * @Author   lang
	 * @DateTime 2020-08-14T22:09:20+0800
	 * @param    [type]                   $content     [description]
	 * @param    integer                  $dst_x       [description] 
	 * @param    integer                  $dst_y       [description]
	 * @param    integer                  $font        [ 字体大小 ] 
	 * @param    array                    $rgba        [ 颜色rbga ] 
	 * @param    integer                  $max_w       [ 最大换行宽度 ] 
	 * @param    string                   $font_family [ 字体，可不填，有默认 (相对路径为项目根目录) ]
	 * @return   [type]                                [description]

5.   * [getPoster 获取合成后图片文件地址]
	 * @Author   lang
	 * @DateTime 2020-08-16T15:45:57+0800
	 * @return   [array]                   [返回文件地址] 

6.   * [setPoster 处理图片，需要传原图片]
	 * @Author lang
	 * @Date   2020-08-17T15:55:31+0800

#### 静态调用
	use Kkokk\Poster\PosterManager;
	use Kkokk\Poster\Exception\Exception;
	# 合成图片
	try {
		$result = PosterManager::poster('poster/poster_user') //生成海报，这里写保存路径和文件名，可以指定图片后缀。默认png
		->buildIm(638,826,[255,255,255,127],false)
		->buildImage('https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=2854425629,4097927492&fm=26&gp=0.jpg',253,326,0,0,131,131,false,'circle')
		->buildText('苏轼','center',477,16,[51, 51, 51, 1])
		->buildText('明月几时有，把酒问青天。不知天上宫阙，今夕是何年。','center',515,14,[153, 153, 153, 1])
		->buildText('长按识别',497,720,15,[153, 153, 153, 1])
		->buildText('查看TA的更多作品',413,757,15,[153, 153, 153, 1])
		->getPoster();
		# 给图片添加水印
		$result = PosterManager::poster() //给指定图片添加水印，这里为空就好
		->buildImDst(__DIR__.'/test.jpeg')
		->buildImage('https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=2854425629,4097927492&fm=26&gp=0.jpg','-20%','-20%',0,0,0,0,false)
		->setPoster();
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
		->buildImage('https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=2854425629,4097927492&fm=26&gp=0.jpg',253,326,0,0,131,131,false,'circle')
		->buildText('苏轼','center',477,16,[51, 51, 51, 1])
		->buildText('明月几时有，把酒问青天。不知天上宫阙，今夕是何年。','center',515,14,[153, 153, 153, 1])
		->buildText('长按识别',497,720,15,[153, 153, 153, 1])
		->buildText('查看TA的更多作品',413,757,15,[153, 153, 153, 1])
		->getPoster();
		# 给图片添加水印
		$PosterManager = new PosterManager(); //给指定图片添加水印，这里为空就好
		$result = $PosterManager->buildImDst(__DIR__.'/test.jpeg')
		->buildImage('https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=2854425629,4097927492&fm=26&gp=0.jpg','center','-20%',0,0,0,0,true)
		->setPoster();
	} catch (Exception $e){
		echo $e->getMessage();
	}