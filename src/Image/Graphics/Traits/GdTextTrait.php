<?php
/**
 * @author  : lang
 * @email   : 732853989@qq.com
 * @date    : 2024/12/3
 * @time    : 22:10
 * @fileName: TextTrait.php
 */

namespace Kkokk\Poster\Image\Graphics\Traits;

trait GdTextTrait
{
    use TextTrait;

    protected function measureLayoutCharacter($char)
    {
        $box = imagettfbbox($char['size'], 0, $char['font'], $char['text']);
        $withSpace = imagettfbbox($char['size'], 0, $char['font'], $char['text'] . ' ');
        $space = imagettfbbox($char['size'], 0, $char['font'], ' ');
        // 后缀空格使右边界落在排版前进位置，避免用窄笔画宽度代替字距。
        $advance = $withSpace[2] - $space[2];
        return ['left' => min($box[0], $box[6]), 'top' => min($box[5], $box[7]),
            'right' => max($box[2], $box[4]), 'bottom' => max($box[1], $box[3]), 'advance' => $advance];
    }

    protected function paintLayoutCharacter($canvas, $char, $x, $y)
    {
        imagettftext($canvas->getImage(), $char['size'], $char['angle'], (int) round($x),
            (int) round($y), $char['color'], $char['font'], $char['text']);
    }
}
