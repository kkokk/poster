<?php
/**
 * @author  : lang
 * @email   : 732853989@qq.com
 * @date    : 2024/12/3
 * @time    : 22:10
 * @fileName: TextTrait.php
 */

namespace Kkokk\Poster\Image\Graphics\Traits;

trait ImagickTextTrait
{
    use TextTrait;

    protected function measureLayoutCharacter($char)
    {
        $draw = $this->createImagickDraw();
        $draw->setFont($char['font']);
        $draw->setFontSize($char['size']);
        // 无画布测量使用轻量 Imagick 上下文，无需创建任何像素图。
        $image = $this->getCanvas() ? $this->getCanvas()->getImage() : $this->createImagick();
        $metrics = $image->queryFontMetrics($draw, $char['text'], false);
        $box = $metrics['boundingBox'];
        // ImageMagick 的字形 y 轴向上，布局坐标 y 轴向下。
        return ['left' => $box['x1'], 'top' => -$box['y2'], 'right' => $box['x2'],
            'bottom' => -$box['y1'], 'advance' => $metrics['textWidth']];
    }

    protected function paintLayoutCharacter($canvas, $char, $x, $y)
    {
        $draw = $this->createImagickDraw();
        $draw->setFont($char['font']);
        $draw->setFontSize($char['size']);
        $draw->setFillColor($char['color']);
        // 对外正角度统一为逆时针，坐标取整与 GD 保持相同策略。
        $canvas->getImage()->annotateImage($draw, round($x), round($y), -$char['angle'], $char['text']);
    }
}
