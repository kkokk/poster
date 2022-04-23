### **poster**

#### 介绍

基于gd库、phpqrcode

PHP海报生成插件，极速生成方便快捷。

快速生成海报、生成签到日、生成二维码、合成二维码、图片添加水印

提示：

1.新增批量处理方法

2.新增字体加粗参数

3.新增字体间隔参数

4.保存路径支持绝对路径和相对路径（相对路径默认项目根目录，当不存在时，默认保存到tests/poster）

#### authors
lang
732853989@qq.com

#### 安装或更新教程

1.  composer require kkokk/poster
2.  composer update kkokk/poster

#### 使用说明

注意：没有特别说明，统一都是px。

文档地址：http://www.520yummy.com/composer/poster/doc.html

演示效果

![输入图片说明](https://cdn.learnku.com/uploads/images/202110/20/54036/Kvt1cV5ygB.png!large "在这里输入图片标题")

![输入图片说明](http://img.520yummy.com/images/2/2022/04/YRG3X4WgSZ3lLlwULkxZ3W3LLGgZ4b.jpeg "在这里输入图片标题")

演示地址：暂无

​	生成签到日历海报、邀请海报

##### **引用海报类**

```php
use Kkokk\Poster\PosterManager;
use Kkokk\Poster\Exception\Exception;

$poster = PosterManager::Poster('poster/poster_user'); # 设置保存路径（项目根目录的相对路径）和文件名
```

##### **创建画布** 

```php
$poster->buildIm($w,$h,$rgba,$alpha); # 创建画布
```

参数说明

| 变量  | 类型    | 必填 | 注释                     |
| ----- | ------- | ---- | ------------------------ |
| w     | number  | 是   | 画布宽                   |
| h     | number  | 是   | 画布高                   |
| rgba  | array   | 否   | 颜色rbga,[255,255,255,1] |
| alpha | boolean | 否   | 是否透明，是：true       |

##### **创建指定图片为画布**

```php
$poster->buildImDst($src,$w,$h,$rgba,$alpha); # 创建指定图片为画布
```

参数说明

| 变量  | 类型    | 必填 | 注释                      |
| ----- | ------- | ---- | ------------------------- |
| src   | source  | 是   | 图像资源                  |
| w     | number  | 否   | 画布宽，默认原图宽        |
| h     | number  | 否   | 画布高，默认原图高        |
| rgba  | array   | 否   | 颜色rbga，[255,255,255,1] |
| alpha | boolean | 否   | 是否透明，默认false       |

##### **合成图片**

```php
$poster->buildImage($src,$dst_x,$dst_y,$src_x,$src_y,$src_w,$src_h,$alpha,$type); # 合成图片
```

参数说明

| 变量  | 类型           | 必填 | 注释                                                         |
| ----- | -------------- | ---- | ------------------------------------------------------------ |
| src   | string         | 是   | 路径，支持网络图片（带http或https）                          |
| dst_x | number\|string | 否   | 目标x轴 特殊值 center 居中 支持百分比20% 支持自定义  支持正负 |
| dst_y | number\|string | 否   | 目标y轴 特殊值 center 居中 支持百分比20% 支持自定义  支持正负 |
| src_x | number         | 否   | 图片x轴，默认0                                               |
| src_y | number         | 否   | 图片y轴，默认0                                               |
| src_w | number         | 否   | 图片自定义宽，默认原宽                                       |
| src_h | number         | 否   | 图片自定义高，默认原高                                       |
| alpha | boolean        | 否   | 是否透明，true：是                                           |
| type  | string         | 否   | 图片变形类型，正常形状：'normal'，圆形：'circle' ，默认normal |

##### **批量合成图片**

```php
$images = [
    [
        'src'   => $src,
        'dst_x' => $dst_x,
        'dst_y' => $dst_y,
        'src_x' => $src_x,
        'src_y' => $src_y,
        'src_w' => $src_w,
        'src_h' => $src_h,
        'alpha' => $alpha,
        'type'  => $type
    ]
];
$poster->buildImageMany($images); # 批量合成图片
```

参数说明：与**合成图片**参数一致。

##### **合成二维码**

```php
$poster->buildQr($text,$dst_x,$dst_y,$src_x,$src_y,$src_w,$src_h,$size,$margin); # 合成二维码
```

参数说明

| 变量   | 类型           | 必填 | 注释                                                         |
| ------ | -------------- | ---- | ------------------------------------------------------------ |
| text   | string         | 是   | 内容，例如：http://www.520yummy.com                          |
| dst_x  | number\|string | 否   | 画布位置x 特殊值 center 居中 支持百分比20% 支持自定义  支持正负 |
| dst_y  | number\|string | 否   | 画布位置y 特殊值 center 居中 支持百分比20% 支持自定义  支持正负 |
| src_x  | number         | 否   | 图片x轴，默认0                                               |
| src_y  | number         | 否   | 图片y轴，默认0                                               |
| src_w  | number         | 否   | 图片自定义宽，默认原宽                                       |
| src_h  | number         | 否   | 图片自定义高，默认原高                                       |
| alpha  | integer        | 否   | 大小，默认4                                                  |
| margin | integer        | 否   | 白边大小，默认1                                              |

##### **批量合成二维码**

```php
$qrs = [
    [
        'text'   => $text,
        'dst_x'  => $dst_x,
        'dst_y'  => $dst_y,
        'src_x'  => $src_x,
        'src_y'  => $src_y,
        'src_w'  => $src_w,
        'src_h'  => $src_h,
        'size'   => $size,
        'margin' => $margin
    ]
];
$poster->buildQrMany($qrs); # 批量合成二维码
```

参数说明：与**合成二维码**参数一致。

##### 合成文字

```php
$poster->buildText($content,$dst_x,$dst_y,$font,$rgba,$max_w,$font_family,$weight,$space); # 合成文字
```

参数说明

| 变量        | 类型                  | 必填 | 注释                                                         |
| ----------- | --------------------- | ---- | ------------------------------------------------------------ |
| content     | string                | 是   | 内容，例如：http://www.520yummy.com                          |
| dst_x       | number\|string\|array | 否   | 画布位置x ；特殊值 center 居中；居中并向左偏移 ['center',-5]， 居中并向右偏移 ['center',5] |
| dst_y       | number                | 否   | 画布位置y，默认0                                             |
| font        | number                | 否   | 字体大小，默认16                                             |
| rgba        | number                | 否   | 颜色rbga，[255,255,255,1]                                    |
| max_w       | number                | 否   | 最大换行宽度，默认0不换行。达到换行宽度自动换行              |
| font_family | number                | 否   | 字体，可不填，有默认 (相对路径为项目根目录)                  |
| weight      | integer               | 否   | 字体粗细 默认字体大小                                        |
| space       | integer               | 否   | 字体间距 默认无                                              |

##### 批量合成文字

```php
$texts = [
    [
        'content'     => $content,
        'dst_x'       => $dst_x,
        'dst_y'       => $dst_y,
        'font'        => $font,
        'rgba'        => $rgba,
        'max_w'       => $max_w,
        'font_family' => $font_family,
        'weight'      => $weight,
        'space'       => $space
    ]
];
$poster->buildQrMany($texts); # 批量合成文字
```

参数说明：与**合成文字**参数一致。

##### 获取海报

```php
$poster->getPoster(); # 获取合成后图片文件地址
```

参数说明：无。

返回说明：返回数组，返回文件地址。

##### 处理海报、图片

```php
$poster->setPoster(); # 处理图片，需要传原图片
```

参数说明：无。

返回说明：处理原图片资源，无返回。

##### 输出图片流

```php
$poster->stream(); # 输出图片流
```

参数说明：无。

返回说明：返回文件流，可输出到浏览器或img标签。

##### 生成二维码

```php
$qr = PosterManager::Poster()->Qr('http://www.520yummy.com','poster/1.png'); # 生成二维码
```

参数说明

| 变量         | 类型            | 必填 | 注释                                                         |
| ------------ | --------------- | ---- | ------------------------------------------------------------ |
| text         | string          | 是   | 二维码包含的内容，可以是链接、文字、json字符串等等，例如：http://www.520yummy.com |
| outfile      | boolean\|string | 否   | 默认为false，不生成文件，只将二维码图片返回输出；否则需要给出存放生成二维码图片的文件名及路径 |
| level        | string          | 否   | 容错级别，默认为L， 可传递的值分别是L(QR_ECLEVEL_L，7%)、M(QR_ECLEVEL_M，15%)、Q(QR_ECLEVEL_Q，25%)、H(QR_ECLEVEL_H，30%)。这个参数控制二维码容错率，不同的参数表示二维码可被覆盖的区域百分比，也就是被覆盖的区域还能识别 |
| size         | integer         | 否   | 控制生成图片的大小，默认为4                                  |
| margin       | integer         | 否   | 控制生成二维码的空白区域大小，默认4                          |
| saveandprint | boolean         | 否   | 保存二维码图片并显示出来，outfile 必须传递图片路径，默认false |

返回说明：outfile 为空，输出二维码图片，不生成文件；否则返回图片路径。

#### 示例

##### 静态调用
```php
use Kkokk\Poster\PosterManager;
use Kkokk\Poster\Exception\Exception;
# 合成图片
try {
    $addImage = "https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=2854425629,4097927492&fm=26&gp=0.jpg";
	$result = PosterManager::Poster('poster/poster_user') //生成海报，这里写保存路径和文件名，可以指定图片后缀。默认png
	->buildIm(638,826,[255,255,255,127],false)
	->buildImage('https://test.acyapi.51acy.com/wechat/poster/top_bg.png')
	->buildImage('https://test.acyapi.51acy.com/wechat/poster/half_circle.png',254,321)
	->buildImage($addImage,253,326,0,0,131,131,false,'circle')
	->buildImage('https://test.acyapi.51acy.com/wechat/poster/fengexian.png',0,655)
	->buildText('苏 轼','center',477,16,[51, 51, 51,1])
	->buildText('明月几时有，把酒问青天。不知天上宫阙，今夕是何年。','center',515,14,[53, 53, 53, 1])
	->buildText('我欲乘风归去，又恐琼楼玉宇，高处不胜寒。','center',535,14,[53, 153, 153, 1])
	->buildText('起舞弄清影，何似在人间。转朱阁，低绮户，照无眠。','center',555,14,[53, 153, 153, 1])
	->buildText('不应有恨，何事长向别时圆？','center',575,14,[53, 153, 153, 1])
	->buildText('人有悲欢离合，月有阴晴圆缺，此事古难全。','center',595,14,[53, 153, 153, 1])
	->buildText('但愿人长久，千里共婵娟。','center',615,14,[53, 153, 153, 1])
	->buildText('长按识别',497,720,15,[53, 153, 153, 1])
	->buildText('查看TA的更多作品',413,757,15,[53, 153, 153, 1])
	->buildQr('http://www.520yummy.com',37,692,0,0,0,0,4,1)
	->getPoster();

	# 批量合成
	$buildImageManyArr = [
		[
        	'src' => 'https://test.acyapi.51acy.com/wechat/poster/top_bg.png'
        ],
        [
            'src' => 'https://test.acyapi.51acy.com/wechat/poster/half_circle.png',
            'dst_x' => 254,
            'dst_y' => 321
        ],
        [
            'src' => 'https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=2854425629,4097927492&fm=26&gp=0.jpg',
            'dst_x' => 253,
            'dst_y' => 326,
            'src_x' => 0,
            'src_y' => 0,
            'src_w' => 131,
            'src_h' => 131,
            'alpha' => false,
            'type'  => 'circle'
        ],
        [
            'src'   => 'https://test.acyapi.51acy.com/wechat/poster/fengexian.png',
            'dst_x' => 0,
            'dst_y' => 655
        ]
	];
	$buildTextManyArr  = [
		[
            'content'=> '苏轼',
            'dst_x' => 'center',
            'dst_y' => 477,
            'font' => 16,
            'rgba' => [51, 51, 51, 1],
            'max_w'=> 0,
            'font_family' => '',
            'weight' => 1,
            'space'=>20
        ],
        [
            'content'=> '明月几时有，把酒问青天。不知天上宫阙，今夕是何年。',
            'dst_x' => 'center',
            'dst_y' => 515,
            'font' => 16,
            'rgba' => [51, 51, 51, 1],
            'max_w'=> 0,
            'font_family' => '',
            'weight' => 1,
            'space'=>20
        ],
        [
            'content'=> '我欲乘风归去，又恐琼楼玉宇，高处不胜寒。',
            'dst_x' => 'center',
            'dst_y' => 535,
            'font' => 16,
            'rgba' => [51, 51, 51, 1],
            'max_w'=> 0,
            'font_family' => '',
            'weight' => 1,
            'space'=>20
        ],
        [
            'content'=> '起舞弄清影，何似在人间。转朱阁，低绮户，照无眠。',
            'dst_x' => 'center',
            'dst_y' => 555,
            'font' => 16,
            'rgba' => [51, 51, 51, 1],
            'max_w'=> 0,
            'font_family' => '',
            'weight' => 1,
            'space'=>20
        ],
        [
            'content'=> '不应有恨，何事长向别时圆？',
            'dst_x' => 'center',
            'dst_y' => 575,
            'font' => 16,
            'rgba' => [51, 51, 51, 1],
            'max_w'=> 0,
            'font_family' => '',
            'weight' => 1,
            'space'=>20
        ],
        [
            'content'=> '人有悲欢离合，月有阴晴圆缺，此事古难全。',
            'dst_x' => 'center',
            'dst_y' => 595,
            'font' => 16,
            'rgba' => [51, 51, 51, 1],
            'max_w'=> 0,
            'font_family' => '',
            'weight' => 1,
            'space'=>20
        ],
        [
            'content'=> '但愿人长久，千里共婵娟。',
            'dst_x' => 'center',
            'dst_y' => 615,
            'font' => 16,
            'rgba' => [51, 51, 51, 1],
            'max_w'=> 0,
            'font_family' => '',
            'weight' => 1,
            'space'=>20
        ],
        [
            'content'=> '长按识别',
            'dst_x' => 'center',
            'dst_y' => 720,
            'font' => 16,
            'rgba' => [51, 51, 51, 1],
            'max_w'=> 0,
            'font_family' => '',
            'weight' => 1,
            'space'=>20
        ],
        [
            'content'=> '查看TA的更多作品',
            'dst_x' => 'center',
            'dst_y' => 757,
            'font' => 16,
            'rgba' => [51, 51, 51, 1],
            'max_w'=> 0,
            'font_family' => '',
            'weight' => 1,
            'space'=>20
        ]
	];
	$buildQrManyArr    = [
		[
			'text'=>'http://www.520yummy.com',
            'dst_x'=>37,
            'dst_y'=>692,
            'src_x'=>0,
            'src_y'=>0,
            'src_w'=>0,
            'src_h'=>0,
            'size'=>4,
            'margin'=>1
		],
		[
			'text'=>'http://www.520yummy.com',
            'dst_x'=>74,
            'dst_y'=>692,
            'src_x'=>0,
            'src_y'=>0,
            'src_w'=>0,
            'src_h'=>0,
            'size'=>4,
            'margin'=>1
		]
	];

	$result = PosterManager::Poster('poster/poster_user')
	->buildIm(638,826,[255,255,255,127],false)
	->buildImageMany($buildImageManyArr)
	->buildTextMany($buildImageManyArr)
	->buildQrMany($buildQrManyArr)
	->getPoster();
    
    # 给图片添加水印
    $setImage = "https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=2854425629,4097927492&fm=26&gp=0.jpg";
	$result = PosterManager::Poster() //给指定图片添加水印，这里为空就好
	->buildImDst(__DIR__.'/test.jpeg')
	->buildImage($setImage,'-20%','-20%',0,0,0,0,false)
	->setPoster();

	# 生成二维码
	$result = PosterManager::Poster()->Qr('http://www.baidu.com','poster/1.png');
} catch (Exception $e){
	echo $e->getMessage();
}
```

##### 实例化调用
```php
use Kkokk\Poster\PosterManager;
use Kkokk\Poster\Exception\Exception;
# 合成图片
try {
    $addImage = "https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=2854425629,4097927492&fm=26&gp=0.jpg";
	$PosterManager = new PosterManager('poster/poster_user'); //生成海报，这里写保存路径和文件名，可以指定图片后缀。默认png
	$result = $PosterManager->buildIm(638,826,255,255,255,1]27,false)
	->buildIm(638,826,[255,255,255,127],false)
	->buildImage('https://test.acyapi.51acy.com/wechat/poster/top_bg.png')
	->buildImage('https://test.acyapi.51acy.com/wechat/poster/half_circle.png',254,321)
	->buildImage($addImage,253,326,0,0,131,131,false,'circle')
	->buildImage('https://test.acyapi.51acy.com/wechat/poster/fengexian.png',0,655)
	->buildText('苏 轼','center',477,16,[51, 51, 51,1])
	->buildText('明月几时有，把酒问青天。不知天上宫阙，今夕是何年。','center',515,14,[53, 53, 53, 1])
	->buildText('我欲乘风归去，又恐琼楼玉宇，高处不胜寒。','center',535,14,[53, 153, 153, 1])
	->buildText('起舞弄清影，何似在人间。转朱阁，低绮户，照无眠。','center',555,14,[53, 153, 153, 1])
	->buildText('不应有恨，何事长向别时圆？','center',575,14,[53, 153, 153, 1])
	->buildText('人有悲欢离合，月有阴晴圆缺，此事古难全。','center',595,14,[53, 153, 153, 1])
	->buildText('但愿人长久，千里共婵娟。','center',615,14,[53, 153, 153, 1])
	->buildText('长按识别',497,720,15,[53, 153, 153, 1])
	->buildText('查看TA的更多作品',413,757,15,[53, 153, 153, 1])
	->buildQr('http://www.520yummy.com',37,692,0,0,0,0,4,1)
	->getPoster();

	# 给图片添加水印
    $setImage = 'https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=2854425629,4097927492&fm=26&gp=0.jpg';
	$PosterManager = new PosterManager(); //给指定图片添加水印，这里为空就好
	$result = $PosterManager->buildImDst(__DIR__.'/test.jpeg')
	->buildImage($setImage,'center','-20%',0,0,0,0,true)
	->setPoster();

	# 生成二维码
	$result = $PosterManager->Qr('http://www.baidu.com','poster/1.png');

} catch (Exception $e){
	echo $e->getMessage();
}
```
