### **poster**

#### 介绍

**得益于 gd、imagick、phpqrcode、wkhtmltopdf**

**主要为了封装一个生成图片便捷的插件，非常感谢使用到的所有工具背后开发者的贡献**

PHP海报生成插件，极速生成方便快捷。

快速生成海报、生成签到日、生成二维码、合成二维码、图片添加水印

滑块验证图片生成、旋转验证图片生成、点击验证图片生成、输入验证图片生成

**小提示：**

**如果无法更新版本，composer 切换回原镜像**

全局设置

composer config -g repo.packagist composer https://repo.packagist.org

#### authors
lang
732853989@qq.com

群号 590660254 [点击链接加入群聊【海报图片验证交流群】](https://jq.qq.com/?_wv=1027&k=k374FhrR)

#### 使用文档

> 文档地址：http://langlanglang.gitee.io/poster-doc/

#### 安装或更新

1.  composer require kkokk/poster
2.  composer update kkokk/poster

#### 演示效果

##### **输入验证码验证**

数字、算术、中文、字母加数字

##### **点击图片验证**

<img src="tests/1223015613615230151165.gif" alt="输入图片说明" style="zoom: 33%;" />

##### 旋转图片验证

<img src="tests/1223015613610230151165.gif" alt="输入图片说明" style="zoom: 33%;" />

##### 滑块验证图片

<img src="tests/122301561368230151165-1.gif" alt="输入图片说明" style="zoom: 33%;" />

##### 海报生成图片示例

<img src="tests/Kvt1cV5ygB.png" alt="输入图片说明" title="在这里输入图片标题" style="zoom:50%;" />

<img src="tests/YRG3X4WgSZ3lLlwULkxZ3W3LLGgZ4b.jpeg" alt="输入图片说明" style="zoom:50%;" />

<img src="tests/JoJhekR1um.png" alt="输入图片说明" style="zoom:50%;" />

演示地址：暂无

​	生成签到日历海报、邀请海报

#### **生成海报**

注意：没有特别说明，统一都是px。

##### 通过 PosterManager 调用

```php
use Kkokk\Poster\PosterManager; // 使用 PosterManager 调用
$poster = PosterManager::Poster();
```

##### 通过 Facades 调用

```php
use Kkokk\Poster\Facades\Poster; // 使用 Facades\Poster 调用

$result = Poster::config($params)
    ->buildIm($w,$h,$rgba,$alpha) # 创建画布
    ->buildImage($src,$dst_x,$dst_y,$src_x,$src_y,$src_w,$src_h,$alpha,$type) # 合成图片
    ->getPoster(); # 获取合成后图片文件地址
```

> 技巧：也可以分开使用

```php
$Poster = Poster::config($params);
$Poster->buildIm($w,$h,$rgba,$alpha); # 创建画布
$Poster->buildImage($src,$dst_x,$dst_y,$src_x,$src_y,$src_w,$src_h,$alpha,$type); # 合成图片
$result = $Poster->getPoster(); # 获取合成后图片文件地址
```

##### 使用 Gd 拓展

```php
$poster = PosterManager::Poster(); // 使用 PosterManager 调用
$Poster = Poster::config($params); // 使用 Facades\Poster 调用
```

##### 使用 Imagick 拓展

```php
$poster = PosterManager::Poster()->extension('imagick'); // 使用 PosterManager 调用
$Poster = Poster::extension('imagick')->config($params); // 使用 Facades\Poster 调用
```

##### 基础配置

```php
$params = [
    'path'        => $path,        // 设置路径
    'dpi'         => $dpi,         // int[]|int 设置 dpi 只针对 Imagick 有效
    'font_size'   => $fontSize,    // 统一设置文字大小
    'font_rgba'   => $rgba,        // 统一设置文字颜色
    'font_space'  => $angle,       // 统一设置文字间距
    'font_weight' => $angle,       // 统一设置文字粗细
    'font_family' => $fontFamily,  // 统一设置文字字体，字体绝对路径
    'font_angle'  => $angle,       // 统一设置文字旋转角度
    'font_max_w'  => $maxW,        // 统一设置文字最大换行宽度
];
$poster->config($params);
```

##### 设置路径

```php
$poster->path($path); # 设置路径
```

参数说明

| 变量 | 类型          | 必填 | 注释                           |
| ---- | ------------- | ---- | ------------------------------ |
| path | string\|array | 是   | 地址，例如：poster/poster_user |

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

##### **创建背景、遮罩** 

> 注意：Imagick 方式，背景目前支持做圆角，渐变色可以支持多种，方向只支持上下

```php
// 背景 rgba 参数解释
// color 颜色数组取值范围 0-255
// alpha 透明度范围 1-127
// to 颜色渐变方向取值范围 bottom、 top、 left、 right、 left top、 right top、 left bottom、 right bottom 默认 bottom
// 单色：['color'=>[[0-255,0-255,0-255]],'alpha'=>1-127]
// 多色渐变：['color'=>[[0-255,0-255,0-255], [0-255,0-255,0-255]],'alpha'=>1-127, 'to'=>'left']
// radius string|array|integer 圆角 默认0 ( '10 20', [10, 20, 30], 10)
// [20] 四个角
// [20,30] 第一个值 左上 右下 第二个值 右上 左下
// [20,30,20] 第一个值 左上 第二个值 右上 左下 第三个值 右下
// [20,30,20,10]  左上 右上 右下  左下
$poster->buildBg(400,526,[
    'color'=>[
        [0,0,162], 
        [0,255,162], 
        [255,255,162], 
        [255, 0, 0], 
        [0, 255, 0]
    ], 
    'alpha'=>50, 
    'to'=>'bottom', 
    'radius'=>'10'
], true, ['center', -10], ['center', 10], 0, 0 , function($im){         
    $im->buildText('明月几时有，把酒问青天。不知天上宫阙，今夕是何年。','center',100,20,[255, 255, 255, 50]);     
}); # 创建画布
```

参数说明

| 变量  | 类型    | 必填 | 注释                     |
| ----- | ------- | ---- | ------------------------ |
| w     | number  | 是   | 画布宽                   |
| h     | number  | 是   | 画布高                   |
| rgba  | array   | 否   | 详见上方注释 |
| alpha | boolean | 否   | 是否透明，是：true       |
| dst_x | number\|string\|array | 否 | 画布位置x 特殊值 center 居中，居中并向左偏移 ['center',-5]， 居中并向右偏移 ['center',5]； 支持百分比20% 支持自定义  支持正负 |
| dst_y | number\|string\|array | 否 | 画布位置y 特殊值 center 居中，居中并向上偏移 ['center',-5]， 居中并向下偏移 ['center',5]； 支持百分比20% 支持自定义  支持正负 |
| src_x | number | 否 | 图片x轴，默认0 |
| src_y | number | 否 | 图片y轴，默认0 |
| func | closure | 否 | 匿名函数（闭包），可以已当前背景为基础合成相应的内容 |

##### **合成图片**

```php
/**
 * 合成图片
 * @param string|array $src  图片地址，旋转角度
 */
$poster->buildImage($src,$dst_x,$dst_y,$src_x,$src_y,$src_w,$src_h,$alpha,$type); # 合成图片
```

> 注意：旋转角度是顺时针

参数说明

| 变量  | 类型             | 必填 | 注释                                                                                       |
| ----- |----------------|----|------------------------------------------------------------------------------------------|
| src   | string\|array  | 是   | ['src'=> $src, 'angle'=> $angle] <br> $src: 路径，支持网络图片（带http或https）<br>$angle: 旋转角度，顺时针旋转 |
| dst_x | number\|string | 否  | 画布位置x 特殊值 center 居中，居中并向左偏移 ['center',-5]， 居中并向右偏移 ['center',5]； 支持百分比20% 支持自定义  支持正负    |
| dst_y | number\|string | 否  | 画布位置y 特殊值 center 居中，居中并向上偏移 ['center',-5]， 居中并向下偏移 ['center',5]； 支持百分比20% 支持自定义  支持正负    |
| src_x | number         | 否  | 图片x轴，默认0                                                                                 |
| src_y | number         | 否  | 图片y轴，默认0                                                                                 |
| src_w | number         | 否  | 图片自定义宽，默认原宽                                                                              |
| src_h | number         | 否  | 图片自定义高，默认原高                                                                              |
| alpha | boolean        | 否  | 是否透明，true：是                                                                              |
| type  | string         | 否  | 图片变形类型，正常形状：'normal'，圆形：'circle' ，默认normal                                               |

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
| dst_x  | number\|string | 否   | 画布位置x 特殊值 center 居中，居中并向左偏移 ['center',-5]， 居中并向右偏移 ['center',5]； 支持百分比20% 支持自定义  支持正负 |
| dst_y  | number\|string | 否   | 画布位置y 特殊值 center 居中，居中并向上偏移 ['center',-5]， 居中并向下偏移 ['center',5]； 支持百分比20% 支持自定义  支持正负 |
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

##### 合成线段

```php
$poster->buildLine($x1, $y1, $x2, $y2, $rgba, $type, $weight);
```

| 变量   | 类型   | 可选值                                     | 必填 | 注释                                                   |
| ------ | ------ | ------------------------------------------ | ---- | ------------------------------------------------------ |
| x1     | int    |                                            | 是   | 起始点x坐标                                            |
| y1     | int    |                                            | 是   | 起始点y坐标                                            |
| x2     | int    |                                            | 是   | 结束点x坐标                                            |
| y2     | int    |                                            | 是   | 结束点y坐标                                            |
| rgba   | array  |                                            | 否   | 默认透明                                               |
| type   | string | 默认line ( rectangle \| filled_rectangle ) | 否   | 默认线， rectangle  矩形， filled_rectangle 矩形并填充 |
| weight | int    |                                            | 否   | 默认1粗细                                              |

##### 合成圆弧

```php
$poster->buildArc($cx, $cy, $w, $h, $s, $e, $rgba, $type, $style, $weight);
```

| 变量   | 类型   | 可选值                                                       | 必填 | 注释                                |
| ------ | ------ | ------------------------------------------------------------ | ---- | ----------------------------------- |
| cx     | int    |                                                              | 是   | 原点x坐标                           |
| cy     | int    |                                                              | 是   | 原点y坐标                           |
| w      | int    |                                                              | 是   | 圆宽度                              |
| h      | int    |                                                              | 是   | 圆高度                              |
| s      | int    |                                                              | 是   | 起始点角度                          |
| e      | int    |                                                              | 是   | 结束点角度                          |
| rgba   | array  |                                                              | 否否 | 默认透明                            |
| type   | string | 默认圆弧 ( filled_arc )                                      | 否   | 默认圆弧线， filled_arc  圆弧并填充 |
| style  | string | 默认**`IMG_ARC_PIE`**（**`IMG_ARC_PIE`** | **`IMG_ARC_CHORD`** | **`IMG_ARC_NOFILL`** |    **`IMG_ARC_EDGED`**） | 否   | 填充类型才生效                      |
| weight | int    |                                                              | 否   | 默认1粗细                           |

##### 合成文字

```php
// 可以用span标签的style color属性改变文字颜色，可用br标签换行
$content = [
    'type' => 'html',
    'content' => '我是<br><span style="color:#fF8716;">某某</span>，<br/>我在<span style="color:#fF8716;">贵阳</span><br>我为家乡助力<br>我用<span style="color:#fF8716;">poster</span>生成海报图片'
];

$poster->buildText($content,$dst_x,$dst_y,$font,$rgba,$max_w,$font_family,$weight,$space); # 合成文字

// 新增文字宽度定位
$dst_x = "center | left | right"; // 特殊值：center 居中 left 靠左 right 靠右
$dst_x = ['center', 10]; // 居中且向右偏移10 负数向左偏移
$dst_x = ['left', 10]; // 靠左且向右偏移10 负数向左偏移
$dst_x = ['right', 10]; // 靠左且向右偏移10 负数向左偏移
// 自定义宽度定位， center left right 
$dst_x = ['custom', 'center', 100, 200, 0]; // 在图像对象的x坐标100到200之间居中, 偏移0

// 新增文字高度定位
$dst_y = "center | top | bottom"; // 特殊值：center 居中 top 靠顶 bottom 靠底
$dst_y = ['center', 10]; // 居中且向下偏移10 负数向上偏移
$dst_y = ['top', 10]; // 靠顶且向下偏移10 负数向上偏移
$dst_y = ['bottom', 10]; // 靠底且向下偏移10 负数向上偏移
// 自定义高度定位， center top bottom 
$dst_y = ['custom', 'center', 100, 200, 0]; // 在图像对象的y坐标100到200之间居中, 偏移0
```

参数说明

| 变量        | 类型                  | 必填 | 注释                                                         |
| ----------- | --------------------- | ---- | ------------------------------------------------------------ |
| content     | string                | 是   | 内容，例如：http://www.520yummy.com                          |
| dst_x       | number\|string\|array | 否   | 画布位置x ；特殊值 center 居中；居中并向左偏移 ['center',-5]， 居中并向右偏移 ['center',5]，上面注释 |
| dst_y       | number                | 否   | 画布位置y，默认0                                             |
| font        | number                | 否   | 字体大小，默认16                                             |
| rgba        | array                 | 否   | 颜色rbga，[255,255,255,1]                                    |
| max_w       | number                | 否   | 最大换行宽度，默认0不换行。达到换行宽度自动换行              |
| font_family | string                | 否   | 字体，可不填，有默认 (相对路径为项目根目录)                  |
| weight      | integer               | 否   | 字体粗细 默认字体大小                                        |
| space       | integer               | 否   | 字体间距 默认无                                              |
| angle       | integer               | 否   | 旋转角度                                                     |

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
        'space'       => $space,
        'angle'       => $angle,
    ]
];
$poster->buildTextMany($texts); # 批量合成文字
```

参数说明：与**合成文字**参数一致。

##### 获取海报

```php
$poster->getPoster($path = ''); # 获取合成后图片文件地址
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

##### **输出base64**

```php
$poster->baseData(); # 返回base64
```

参数说明：无。

返回说明：返回回base64，不保留在服务器直接使用。

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

#### HTML转图片、PDF

> 需要安装 wkhtmltopdf 工具，下载地址 https://wkhtmltopdf.org/downloads.html
>
> 注意：css 以 -webkit 标准执行

##### **调用Html类**

```php
use Kkokk\Poster\Facades\Html;
```

##### **加载html**

```php
$html = <<<eol
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
eol;
/**
 * @param string $html html文件路径、链接、html字符串 
 */
$html = Html::load($html);
```

##### **输出类型**

```php
/**
 * @param string $type 默认png，值范围为常规图片类型，PDF
 */
$html->type($type);
```

##### **工具原生命令**

```php
/**
 * @param string $command 工具原生命令 如 --version
 */
$html->command($command);
```

##### **设置尺寸**

```php
/**
 * @param int $width 指定宽度
 * @param int $height 指定高度
 */
$html->size($width, $height);
```

##### **剪裁**

```php
/**
 * @param int $crop_w 剪裁宽度
 * @param int $crop_h 剪裁高度
 * @param int $crop_x 从x点开始剪裁
 * @param int $crop_y 从y点开始剪裁
 */
$html->crop($crop_w, $crop_h, $crop_x, $crop_y);
```

##### **设置背景透明**

```php
$html->transparent();
```

##### **设置图片质量**

```php
/**
 * @param int $quality 0-100
 */
$html->quality($quality)
```

##### **设置输出地址**

```php
/**
 * @param string $path 指定保存文件路径，包含文件名
 * @param string $type 默认png 这里和type方法一致
 */
$html->output($path, $type);
```

##### **渲染**

```php
/**
 * @return Html Html对象
 */
$htmlObj = $html->render();
```

##### **获取二进制流**

```php
$blob = $htmlObj->getImageBlob();
```

##### **获取保存文件**

```php
$file = $htmlObj->getFilePath();
```

##### **完整示例**

```php
use Kkokk\Poster\Facades\Html;

$htmlObj = Html::load($html)->transparent()->size(338, 426)->render();

// 流文件
$blob = $htmlObj->getImageBlob();
// 文件地址
$file = $htmlObj->getFilePath();

```

##### 和生成海报的合成图片配合使用

```php
use Kkokk\Poster\Facades\Poster;
use Kkokk\Poster\Facades\Html;

// $html 用上面的代码这里省略...
Poster::extension('gd')
    ->buildIm(638, 826, [41, 43, 48, 127], false)
    ->buildImage([
        'src' => Html::load($html)->transparent()->size(338, 426)->render()->getImageBlob(),
        'angle' => 0
    ], 'center', 'center')
    ->buildImage([
        'src' => 'https://portrait.gitee.com/uploads/avatars/user/721/2164500_langlanglang_1601019617.png',
        'angle' => 80
    ], 253, 326, 0, 0, 131, 131, false, 'circle')
    ->buildText('苏轼', 'center', 477, 16, [255, 255, 255, 1])
    ->buildText('明月几时有，把酒问青天。不知天上宫阙，今夕是何年。', 'center', 515, 14, [255, 255, 255, 1])
    ->stream();

// 以上将把 wkhtmltopdf 生成的图片合成到海报中，并输出为流文件
```

#### 生成验证码图片

##### 滑块图片验证

```php
# 自定义参数
$params = [
    'src'           => '',  // 背景图片，尺寸 340 * 191
    'im_width'      => 340, // 画布宽度
    'im_height'     => 251, // 画布高度
    'im_type'       => 'png', // png 默认 jpg quality 质量
    'quality'       => 80,    // jpg quality 质量
    'bg_width'      => 340, // 背景宽度
    'bg_height'     => 191, // 背景高度
    'slider_width'  => 50,  // 滑块宽度
    'slider_height' => 50,  // 滑块高度
    'slider_border' => 2,   // 滑块边框
    'slider_bg'     => 1,   // 滑块背景数量
];

$type = 'slider';

/**
  * 获取验证参数
  * 内部使用了 laravel 的 cache 缓存，返回的是图片的 base64 、 缓存key 、滑块高度
  * @param string $type   验证码类型
  * @param array  $params 验证码自定义参数
  * @return arary
  */
$data = PosterManager::Captcha()->type($type)->config($params)->get();

/** 
  * 验证
  * 前端根据相关滑块操作进行处理, 返回x坐标，返回 true 则验证成功
  * @param string     $key     缓存key
  * @param string|int $value   前端传回来的x坐标
  * @param int        $leeway  误差值
  * @return boolean
  */
$res = PosterManager::Captcha()->type($type)->check($key, $value, $leeway);
```

##### 旋转图片验证

```php
# 自定义参数
$params = [
        'src'           => '',  // 背景图片，尺寸 350 * 350 正方形都可 
        'im_width'      => 350, // 画布宽度
        'im_height'     => 350, // 画布高度
    	'im_type'       => 'png', // png 默认 jpg quality 质量
        'quality'       => 80,    // jpg quality 质量
    ];

$type = 'rotate';

/**
  * 获取验证参数
  * 内部使用了 laravel 的 cache 缓存，返回的是图片的 base64 、 缓存key
  * @param string $type   验证码类型
  * @param array  $params 验证码自定义参数
  * @return arary
  */
$data = PosterManager::Captcha()->type($type)->config($params)->get();

/** 
  * 验证
  * 前端根据相关滑块操作进行处理, 返回x坐标，返回 true 则验证成功
  * @param string     $key     缓存key
  * @param string|int $value   前端传回来的旋转角度
  * @param int        $leeway  误差值
  * @return boolean
  */
$res = PosterManager::Captcha()->type($type)->check($key, $value, $leeway);
```

##### 点击图片验证

```php
# 自定义参数
$params = [
        'src'         => '',
        'im_type'     => 'png', // png 默认 jpg quality 质量
        'quality'     => 80,    // jpg quality 质量
        'font_family' => '', // 感谢站酷提供免费商用站酷库黑体、可自定义炫酷字体文件（绝对路径）
        'contents'    => '', // 自定义文字
        'font_count'  => 0,  // 文字长度
        'font_size'   => 42, // 字体大小
        'line_count'  => 0,  // 干扰线数量
        'char_count'  => 0,  // 干扰字符数量
    ];

$type = 'click';

/**
  * 获取验证参数
  * 内部使用了 laravel 的 cache 缓存，返回的是图片的 base64 、 缓存key
  * @param string $type   验证码类型
  * @param array  $params 验证码自定义参数
  * @return arary
  */
$data = PosterManager::Captcha()->type($type)->config($params)->get();

/** 
  * 验证
  * 前端根据相关点击操作进行处理, 返回点击坐标数组，返回 true 则验证成功
  * @param string            $key     缓存key
  * @param string|int|array  $value   前端传回来的坐标数组
  * @return boolean
  */
$res = PosterManager::Captcha()->type($type)->check($key, $value);
```

##### 手动输入验证

```php
# 自定义参数
$params = [
        'src'         => '',
        'im_width'    => 256,
        'im_height'   => 64,
        'im_type'     => 'png', // png 默认 jpg quality 质量
        'quality'     => 80,    // jpg quality 质量
        'type'        => 'number', // type = number 数字 alpha_num 字母和数字 math 计算 text 文字
        'font_family' => '', // 可自定义炫酷字体文件
        'font_size'   => 32, // 字体大小
        'font_count'  => 4,  // 字体长度
        'line_count'  => 5,  // 干扰线数量
        'char_count'  => 10,  // 干扰字符数量
    ];

$type = 'click';

/**
  * 获取验证参数
  * 内部使用了 laravel 的 cache 缓存，返回的是图片的 base64 、 缓存key
  * @param string $type   验证码类型
  * @param array  $params 验证码自定义参数
  * @return arary
  */
$data = PosterManager::Captcha()->type($type)->config($params)->get();

/** 
  * 验证
  * 前端根据相关输入, 返回输入结果，返回 true 则验证成功
  * @param string            $key     缓存key
  * @param string|int|array  $value   输入结果
  * @return boolean
  */
$res = PosterManager::Captcha()->type($type)->check($key, $value);
```

#### 示例

##### 图片验证

```php
	use Kkokk\Poster\PosterManager;
	use Kkokk\Poster\Exception\PosterException;
	
	try {
        # 滑块验证
		$type = 'slider';
		$data = PosterManager::Captcha()->type($type)->get();
		$res = PosterManager::Captcha()->type($type)->check($key, $value);
        
        # 旋转图片验证
		$type = 'rotate';
		$data = PosterManager::Captcha()->type($type)->get();
		$res = PosterManager::Captcha()->type($type)->check($key, $value);
        
        # 点击验证
		$type = 'click';
		$data = PosterManager::Captcha()->type($type)->get();
		$res = PosterManager::Captcha()->type($type)->check($key, $value);
        
        # 输入验证
		$type = 'input';
		$data = PosterManager::Captcha()->type($type)->get();
		$res = PosterManager::Captcha()->type($type)->check($key, $value);
		
	} catch (PosterException $e) {
		print_r($e->getMessage())
	}
```

##### 海报类门面调用

```php
use Kkokk\Poster\PosterManager;
use Kkokk\Poster\Exception\Exception;
# 合成图片
try {
    $result = Poster::extension('gd')
    ->config([
        'path' => __DIR__ . '/../poster/test1.png',
        // 'font' => 'static/simkai.ttf',
        // 'dpi' => 72
    ])
    ->buildIm(638, 826, [255, 255, 255, 127], false)
    // ->buildImage('https://test.acyapi.51acy.com/wechat/poster/top_bg.png')
    // ->buildImage('static/top_bg.png')
    // ->buildImage('https://test.acyapi.51acy.com/wechat/poster/half_circle.png', 254, 321)
    // ->buildImage('https://portrait.gitee.com/uploads/avatars/user/721/2164500_langlanglang_1601019617.png', 253, 326, 0, 0, 131, 131, false, 'circle')
    // ->buildImage('https://test.acyapi.51acy.com/wechat/poster/fengexian.png', 0, 655)
    // ->buildImage('https://test.acyapi.51acy.com/wechat/qrcode/poster_241.jpg',37,692,0,0,122,122)
    ->buildText('明月几时有，把酒问青天', ['center'], 200, 20, [52, 52, 52, 2], 0, '', 1, 40)
    ->buildText('明月几时有，把酒问青天', ['center'], 300, 20, [52, 52, 52, 2], 0, '', 1, 40)
    ->buildText('苏轼', 'center', 477, 16, [51, 51, 51, 1])
    ->buildText('明月几时有，把酒问青天。不知天上宫阙，今夕是何年。', 'center', 515, 14, [153, 153, 153, 1])
    ->buildText('长按识别', 497, 720, 15, [153, 153, 153, 1])
    ->buildText('查看TA的更多作品', 437, 757, 15, [153, 153, 153, 1])
    ->buildQr('http://www.baidu.com', 37, 692, 0, 0, 122, 122)
    ->buildBg(400, 500, ['color' => [
        [255, 0, 0],
        [255, 125, 0],
        [255, 255, 0],
        [0, 255, 0],
        [0, 255, 255],
        [0, 0, 255],
        [255, 0, 255]
    ], 'alpha' => 80, 'to' => 'top', 'radius' => '20 30 40 80'], true, 'center', 'center', 0, 0,
        function ($im) {
            // $im->buildImage('https://test.acyapi.51acy.com/wechat/poster/top_bg.png');
            $im->buildLine(10, 100, 100, 200, [0, 0, 0, 1], '', 10);
            // $im->buildLine(10, 30, 100, 100, [0, 0, 0, 1], 'rectangle', 10);
            // $im->buildLine(120, 10, 220, 100, [0, 0, 0, 1], 'filled_rectangle', 10);
            $im->buildArc(200, 200, 50, 50, 0, 360, [0, 0, 0, 1], '', 1);
            $im->buildText('明月几时有，把酒问青天，不知天上宫阙，今夕是何年', 'center', ['custom', 'center', 0, 100, 0], 20, [0, 0, 0, 50], 0, '', 1, 0);
            // $im->buildText('明月几时有', ['custom', 'right', 200, 400], ['custom', 'bottom', 200, 500, -20], 20, [0, 0, 0, 50]);
        })
    ->getPoster();
} catch (Exception $e){
	echo $e->getMessage();
}
```



##### 海报类静态调用

```php
use Kkokk\Poster\PosterManager;
use Kkokk\Poster\Exception\Exception;
# 合成图片
try {
    $addImage = "https://portrait.gitee.com/uploads/avatars/user/721/2164500_langlanglang_1601019617.png";
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
	];
	$buildTextManyArr  = [
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
	];

	$result = PosterManager::Poster('poster/poster_user')
        ->buildIm(638,826,[255,255,255,127],false)
        ->buildImageMany($buildImageManyArr)
        ->buildTextMany($buildImageManyArr)
        ->buildQrMany($buildQrManyArr)
        ->buildBg(400,526,
            ['color'=>[[0,0,162], [0,255,162], [255,255,162], [255, 0, 0], [0, 255, 0]], 
             'alpha'=>50, 
             'to'=>'bottom'
            ],
            true, 
            ['center', -10], 
            ['center', 10], 
            0, 
            0 ,
            function($im){
              $im->buildText('明月几时有，把酒问青天。不知天上宫阙，今夕是何年。','center',100,20,[255, 255, 255, 50]);
        	}
        )->getPoster();
    
    # 给图片添加水印
    $setImage = "https://portrait.gitee.com/uploads/avatars/user/721/2164500_langlanglang_1601019617.png";
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

##### 海报类实例化调用
```php
use Kkokk\Poster\PosterManager;
use Kkokk\Poster\Exception\Exception;
# 合成图片
try {
    $addImage = "https://portrait.gitee.com/uploads/avatars/user/721/2164500_langlanglang_1601019617.png";
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
    $setImage = 'https://portrait.gitee.com/uploads/avatars/user/721/2164500_langlanglang_1601019617.png';
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
