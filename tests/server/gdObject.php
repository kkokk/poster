<?php
/**
 * User: lang
 * Date: 2024/11/26
 * Time: 11:15
 */

use Kkokk\Poster\Image\Gd\Canvas;
use Kkokk\Poster\Image\Gd\Image;
use Kkokk\Poster\Image\Gd\Text;

require '../../vendor/autoload.php';

$canvas = new Canvas(500, 500);

// $file = '/Users/lang/Documents/image/3e000e09b00c001a6ff3d0ec9fb1e01b.jpeg';
// $file = 'https://img2.baidu.com/it/u=1310029438,409566289&fm=253&fmt=auto&app=138&f=JPEG?w=800&h=1541';

// $canvas->readImage($file);

// $image = new Image('https://portrait.gitee.com/uploads/avatars/user/721/2164500_langlanglang_1601019617.png');
// $image = new Image('https://img2.baidu.com/it/u=1310029438,409566289&fm=253&fmt=auto&app=138&f=JPEG?w=800&h=1541');
// $canvas->addImage($image->scale(100, 100)->circle(), 'center', 'center');
// $canvas->addImage((clone $image)->scale(50, 50)->circle(), 0, 0);
// $canvas->addImage($image->scale(100, 100)->circle(), 'center', 0);

// $canvas->addImage($image->crop('center', 'center', 500, 500));

// $qr = new \Kkokk\Poster\Image\Gd\Qr('http://www.baidu.com');
//
// $canvas->addImage($qr, 'center', 'center');

$text = (new Text())
    ->setText("床前明月光，疑似地上霜。举头望明月，低头思故乡。床前明月光，疑似地上霜。举头望明月，低头思故乡。")
    ->setAlign('center');

// $canvas->getData(__DIR__ . '/../poster/test7.png');
// $canvas->setData();
$text->draw($canvas, 20, 50);




// $canvas->getStream();


class Texts
{
    private $image;
    private $width;
    private $height;
    private $fontFile;

    // 存储每个字的属性
    private $characters = [];

    // 当前的 X 坐标
    private $currentX = 0;

    // 基准 Y 坐标
    private $baseY = 40;

    // 行间距
    private $lineSpacing = 5;

    // 当前 Y 坐标（用于多行文本）
    private $currentY;

    public function __construct($width, $height, $fontFile)
    {
        $this->width = $width;
        $this->height = $height;
        $this->fontFile = $fontFile;

        // 创建画布
        $this->image = imagecreatetruecolor($width, $height);
        // 设置背景为白色
        $white = imagecolorallocate($this->image, 255, 255, 255);
        imagefill($this->image, 0, 0, $white);

        $this->currentY = $this->baseY;
    }

    // 添加单个字符的属性
    public function addCharacter($char, $size, $color, $font = null, $spacing = 0)
    {
        $this->characters[] = [
            'char'    => $char,
            'size'    => $size,
            'color'   => $color,
            'font'    => $font ?: $this->fontFile,
            'spacing' => $spacing
        ];
    }

    // 渲染文本
    public function render()
    {
        foreach ($this->characters as $charData) {
            // 提取每个字的属性
            $char = $charData['char'];
            $size = $charData['size'];
            $color = $charData['color'];
            $font = $charData['font'];
            $spacing = $charData['spacing'];

            // 分配颜色
            $rgb = sscanf($color, "#%02x%02x%02x");
            $textColor = imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]);

            // 获取字符的宽度
            $bbox = imagettfbbox($size, 0, $font, $char); // 获取字符的边框
            $charWidth = $bbox[2] - $bbox[0]; // 宽度

            // 渲染文本
            imagettftext($this->image, $size, 0, intval($this->currentX), intval($this->currentY), $textColor, $font,
                $char);

            // 更新 X 坐标：前一个字符的宽度 + 间距
            $this->currentX += $charWidth + $spacing;
        }
    }

    // 输出图像
    public function output()
    {
        header('Content-Type: image/png');
        imagepng($this->image);
        imagedestroy($this->image);
    }
}

// 使用示例
$fontFile = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'style/simkai.ttf'; // 请确保这里的字体文件路径正确

$text = new Texts(500, 100, $fontFile);
$text->addCharacter('你好', 12, '#FF0000', $fontFile); // 字母 H, 红色, 字体大小 30
$text->addCharacter('哈哈哈', 16, '#00FF00'); // 字母 e, 绿色
$text->addCharacter('我的名字', 38, '#0000FF'); // 字母 l, 蓝色
$text->addCharacter('是', 30, '#FFFF00'); // 字母 l, 黄色
$text->addCharacter('李白', 30, '#FF00FF'); // 字母 o, 紫色

$text->render();
$text->output();


