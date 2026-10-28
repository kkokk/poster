<?php
/**
 * @author  : lang
 * @email   : 732853989@qq.com
 * @date    : 2024/12/3
 * @time    : 22:12
 * @fileName: TextTrait.php
 */

namespace Kkokk\Poster\Image\Graphics\Traits;

use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Image\Graphics\Interfaces\ImageGraphicsEngineInterface;
use Kkokk\Poster\Image\Graphics\Interfaces\TextGraphicsEngineInterface;

trait TextTrait
{
    /**
     * 测量当前文字或图文内容，不绘制、不改变字号或换行配置。
     * null 继承配置/画布宽度，0 不自动换行；bounds 相对第一行基线。
     *
     * @param int|float|null $maxWidth
     * @param ImageGraphicsEngineInterface|null $canvas 用于匹配绘制宽度及驱动环境
     * @return array width/height 为向外取整的布局框，advanceWidth 为未旋转前进宽度
     * @throws PosterException
     */
    public function measure($maxWidth = null, ImageGraphicsEngineInterface $canvas = null)
    {
        return $this->inspectTextLayout($maxWidth, $canvas, false);
    }

    /**
     * 返回测量汇总及纯数据元素快照，不暴露内部图片资源或样式对象。
     * 坐标相对第一行基线；文字锚点为基线，图片锚点为左下角。
     * @param int|float|null $maxWidth
     * @param ImageGraphicsEngineInterface|null $canvas
     * @return array measure字段及elements（line从0开始，bounds含旋转和模拟加粗）
     * @throws PosterException
     */
    public function layout($maxWidth = null, ImageGraphicsEngineInterface $canvas = null)
    {
        return $this->inspectTextLayout($maxWidth, $canvas, true);
    }

    /**
     * 在独立画布副本绘制内容和调试辅助线，仅接受数值基线坐标。
     * @param ImageGraphicsEngineInterface $canvas 内置GD/Imagick画布，须支持copy
     * @param int|float $x
     * @param int|float $y
     * @return ImageGraphicsEngineInterface 独立预览；不修改输入画布或文字配置
     * @throws PosterException
     */
    public function debugLayout(ImageGraphicsEngineInterface $canvas, $x = 0, $y = 0)
    {
        foreach ([$x, $y] as $coordinate) {
            if ((!is_int($coordinate) && !is_float($coordinate)) || !is_finite((float) $coordinate)) {
                throw new PosterException('Layout debug requires finite numeric baseline coordinates');
            }
        }
        if (!is_callable([$canvas, 'copy'])) throw new PosterException('Layout debug requires a copyable canvas');
        $layout = $this->layout(null, $canvas);
        $preview = $canvas->copy();
        $previousCanvas = $this->getCanvas();
        try {
            $this->draw($preview, $x, $y);
            $lineBounds = [];
            foreach ($layout['elements'] as $element) {
                $b = $element['bounds']; $index = $element['line'];
                if (!isset($lineBounds[$index])) $lineBounds[$index] = $b;
                else {
                    foreach (['left','top'] as $side) $lineBounds[$index][$side] = min($lineBounds[$index][$side], $b[$side]);
                    foreach (['right','bottom'] as $side) $lineBounds[$index][$side] = max($lineBounds[$index][$side], $b[$side]);
                }
                $this->debugLayoutBox($preview, $b, $x, $y, [255,128,0]);
            }
            foreach ($layout['lines'] as $index => $line) {
                $offset = $this->getFontAlign() === 'center' ? ($layout['advanceWidth']-$line['width'])/2
                    : ($this->getFontAlign() === 'right' ? $layout['advanceWidth']-$line['width'] : 0);
                $b = isset($lineBounds[$index]) ? $lineBounds[$index]
                    : ['left'=>$offset,'right'=>$offset,'top'=>$line['baseline']-$line['lineHeight'],'bottom'=>$line['baseline']];
                $this->debugLayoutBox($preview, $b, $x, $y, [0,160,0]);
                // 水平行基线是参考线；各旋转片段的实际锚点另以十字标出。
                $this->debugLayoutPath($preview, [$x+$offset,$y+$line['baseline'], $x+$offset+$line['width'],$y+$line['baseline']], [0,96,255]);
            }
            if ($layout['lineCount']) $this->debugLayoutBox($preview, $layout['bounds'], $x, $y, [255,0,0]);
            foreach ($layout['elements'] as $element) {
                $px = $x+$element['x']; $py = $y+$element['y'];
                $this->debugLayoutPath($preview, [$px-2,$py,$px+2,$py], [160,0,200]);
                $this->debugLayoutPath($preview, [$px,$py-2,$px,$py+2], [160,0,200]);
            }
            return $preview;
        } finally {
            $this->setCanvas($previousCanvas);
        }
    }

    protected function debugLayoutBox($canvas, $bounds, $x, $y, $color)
    {
        $this->debugLayoutPath($canvas, [$x+$bounds['left'],$y+$bounds['top'],
            $x+$bounds['right'],$y+$bounds['top'], $x+$bounds['right'],$y+$bounds['bottom'],
            $x+$bounds['left'],$y+$bounds['bottom']], $color);
    }

    protected function debugLayoutPath($canvas, $points, $color)
    {
        $points = array_map(function ($point) { return (int) round($point); }, $points);
        // 两点线段补回起点，兼容GD多边形最少三个顶点的要求。
        if (count($points) === 4) $points = array_merge($points, array_slice($points, 0, 2));
        $canvas->drawImagePolygon($points, $color, 1);
    }

    protected function inspectTextLayout($maxWidth, $canvas, $includeElements)
    {
        if ($maxWidth !== null && ((!is_int($maxWidth) && !is_float($maxWidth)) || !is_finite($maxWidth) || $maxWidth < 0)) {
            throw new PosterException('Text measurement maxWidth must be a finite non-negative number or null');
        }
        $previousCanvas = $this->getCanvas();
        $this->setCanvas($canvas);
        try {
            $characters = [];
            $contents = $this instanceof TextGraphicsEngineInterface ? [$this] : $this->contents;
            foreach ($contents as $content) {
                // 颜色不影响度量，使用占位值避免分配画布或绘图资源。
                $characters = array_merge($characters, $this->singleImageTextSplit($content, 0));
            }
            $width = $maxWidth === null ? ($this->getMaxWidth() ?: ($canvas ? $canvas->getWidth() : 0)) : $maxWidth;
            $layout = $this->createTextLayout($characters, $width);
            $bounds = $layout['bounds'];
            $lines = [];
            foreach ($layout['lines'] as $line) {
                $lines[] = ['width' => isset($line['width']) ? $line['width'] : 0,
                    'lineHeight' => isset($line['height']) ? $line['height'] : 0,
                    'baseline' => $line['baseline']];
            }
            $result = ['width' => (int) (ceil($bounds[2]) - floor($bounds[0])),
                'height' => (int) (ceil($bounds[3]) - floor($bounds[1])),
                'bounds' => ['left' => $bounds[0], 'top' => $bounds[1], 'right' => $bounds[2], 'bottom' => $bounds[3]],
                'advanceWidth' => $layout['width'], 'lineCount' => $characters ? count($lines) : 0,
                'lines' => $characters ? $lines : []];
            if ($includeElements) {
                $result['textUnit'] = $this->textUnit;
                $result['elements'] = [];
                foreach ($layout['items'] as $item) {
                    $b = $item['bounds'];
                    $element = ['type' => $item['type'], 'line' => $item['line'],
                        'x' => $item['x'], 'y' => $item['y'],
                        'advanceWidth' => $item['metrics']['advance'],
                        'bounds' => ['left' => $b[0], 'top' => $b[1], 'right' => $b[2], 'bottom' => $b[3]]];
                    if ($item['type'] === 'text') {
                        $element['text'] = $item['text'];
                        $element['angle'] = $item['angle'];
                    } else {
                        $element['width'] = $item['width'];
                        $element['height'] = $item['height'];
                    }
                    $result['elements'][] = $element;
                }
            }
            return $result;
        } finally {
            $this->setCanvas($previousCanvas);
        }
    }

    /**
     * 查找容纳完整文本的最大整数字号，不修改原对象。
     * 仅支持 Text；混排字号比例需要单独的适配契约。
     * @return array fontSize/ fits/ metrics；无可用字号时返回最小字号及 fits=false
     */
    public function fitFontSize($width, $height, $minSize = 8, $maxSize = 72, ImageGraphicsEngineInterface $canvas = null)
    {
        if (!$this instanceof TextGraphicsEngineInterface) {
            throw new PosterException('Automatic font size currently supports Text only');
        }
        foreach ([$width, $height] as $value) {
            if ((!is_int($value) && !is_float($value)) || !is_finite((float) $value) || $value <= 0) {
                throw new PosterException('Text fitting dimensions must be finite positive numbers');
            }
        }
        if (!is_int($minSize) || !is_int($maxSize) || $minSize <= 0 || $maxSize < $minSize || $maxSize - $minSize > 1000) {
            throw new PosterException('Invalid font size range (maximum span 1000)');
        }
        $probe = clone $this;
        $probe->setMaxLines(0);
        // 逐级检查避免字体hinting/换行导致的尺寸非单调，不能盲目二分。
        for ($size = $maxSize; $size >= $minSize; $size--) {
            $probe->setFontSize($size);
            $metrics = $probe->measure($width, $canvas);
            $fits = $metrics['width'] <= $width && $metrics['height'] <= $height;
            if ($fits) return ['fontSize' => $size, 'fits' => true, 'metrics' => $metrics];
        }
        return ['fontSize' => $minSize, 'fits' => false, 'metrics' => $metrics];
    }
    /**
     * 在矩形内按布局边界定位；宽度用于本次换行，高度不裁剪内容。
     * @return $this
     */
    public function drawInBox(ImageGraphicsEngineInterface $canvas, $x, $y, $width, $height, $horizontal = 'left', $vertical = 'top')
    {
        foreach ([$x, $y, $width, $height] as $value) {
            if ((!is_int($value) && !is_float($value)) || !is_finite((float) $value)) {
                throw new PosterException('Text box coordinates must be finite numbers');
            }
        }
        if ($width <= 0 || $height <= 0 || !in_array($horizontal, ['left','center','right'], true)
            || !in_array($vertical, ['top','center','bottom'], true)) {
            throw new PosterException('Invalid text box dimensions or alignment');
        }
        $previousWidth = $this->getMaxWidth();
        $previousCanvas = $this->getCanvas();
        try {
            $this->setMaxWidth($width);
            $metrics = $this->measure($width, $canvas);
            $b = $metrics['bounds'];
            $dx = $horizontal === 'center' ? ($width - ($b['right']-$b['left']))/2 : ($horizontal === 'right' ? $width-($b['right']-$b['left']) : 0);
            $dy = $vertical === 'center' ? ($height - ($b['bottom']-$b['top']))/2 : ($vertical === 'bottom' ? $height-($b['bottom']-$b['top']) : 0);
            $this->draw($canvas, $x+$dx-$b['left'], $y+$dy-$b['top']);
            return $this;
        } finally {
            $this->setMaxWidth($previousWidth);
            $this->setCanvas($previousCanvas);
        }
    }
    protected $textUnit = 'codepoint';

    /** 字素模式仅在单个Text片段内分组，不提供shaping或跨样式合并。 */
    public function setTextUnit($unit)
    {
        if (!in_array($unit, ['codepoint', 'grapheme'], true)) {
            throw new PosterException('Unknown text unit');
        }
        $this->textUnit = $unit;
        return $this;
    }

    protected function splitTextUnits($text)
    {
        if ($this->textUnit === 'grapheme') {
            // 使用当前PCRE的Unicode规则，不引入必需intl；失败不能退回拆分组合序列。
            if (preg_match_all('/\X/u', $text, $matches) === false) {
                throw new PosterException('Grapheme segmentation requires valid UTF-8 and Unicode PCRE support');
            }
            return $matches[0];
        }
        $units = [];
        for ($i = 0, $length = mb_strlen($text, 'UTF-8'); $i < $length; $i++) {
            $units[] = mb_substr($text, $i, 1, 'UTF-8');
        }
        return $units;
    }

    protected $wrapMode = 'character';

    /** word仅保护ASCII字母/数字/下划线单词，超长词回退字符换行。 */
    public function setWrapMode($mode)
    {
        if (!in_array($mode, ['character', 'word'], true)) {
            throw new PosterException('Unknown text wrapping mode');
        }
        $this->wrapMode = $mode;
        return $this;
    }
    protected $maxLines = 0;
    protected $ellipsis = '…';

    /** 0 表示不限行；省略符使用最后可见文字的样式。 */
    public function setMaxLines($maxLines, $ellipsis = '…')
    {
        if (!is_int($maxLines) || $maxLines < 0 || !is_string($ellipsis) || strpos($ellipsis, "\n") !== false || strpos($ellipsis, "\r") !== false) {
            throw new PosterException('Invalid maximum lines or ellipsis');
        }
        $this->maxLines = $maxLines;
        $this->ellipsis = $ellipsis;
        return $this;
    }
    protected function createTextLayout($characters, $maxWidth)
    {
        $engine = new \Kkokk\Poster\Image\Graphics\TextLayout();
        $measure = function ($char) { return $this->measureLayoutCharacter($char); };
        $layout = $engine->build($characters, $maxWidth, $measure, $this->wrapMode);
        if ($this->maxLines && count($layout['lines']) > $this->maxLines) {
            $layout['lines'] = array_slice($layout['lines'], 0, $this->maxLines);
            $index = $this->maxLines - 1;
            $items = isset($layout['lines'][$index]['items']) ? $layout['lines'][$index]['items'] : [];
            $style = null;
            foreach ($layout['lines'] as $line) {
                foreach (isset($line['items']) ? $line['items'] : [] as $char) {
                    if ($char['type'] === 'text') $style = $char;
                }
            }
            $marker = [];
            if ($style) {
                foreach ($this->splitTextUnits($this->ellipsis) as $unit) {
                    $char = $style;
                    $char['text'] = $unit;
                    $marker[] = $char;
                }
            }
            // 正文与省略符共同度量；极窄区域先移除正文，再缩短省略符。
            do {
                $last = $engine->build(array_merge($items, $marker), $maxWidth, $measure);
                if (count($last['lines']) === 1 && (!$maxWidth || $last['width'] <= $maxWidth)) break;
                if ($items) array_pop($items);
                elseif ($marker) array_pop($marker);
                else break;
            } while (true);
            // 截断成空行时仍保留原来的行高，避免显式换行丢失垂直空间。
            if (empty($last['lines'][0]['items']) && isset($layout['lines'][$index]['height'])) {
                $last['lines'][0]['height'] = $layout['lines'][$index]['height'];
            }
            $layout['lines'][$index] = $last['lines'][0];
            $layout['width'] = 0;
            foreach ($layout['lines'] as $line) $layout['width'] = max($layout['width'], isset($line['width']) ? $line['width'] : 0);
        }
        return $engine->position($layout, $this->getFontAlign());
    }
    protected function drawTextLayout($canvas, $characters, $x, $y)
    {
        $this->setCanvas($canvas);
        $layout = $this->createTextLayout($characters, $this->getMaxWidth() ?: $canvas->getWidth());
        $bounds = $layout['bounds'];
        $originX = calc_text_dst_x($x, ['max_width' => $bounds[2] - $bounds[0]], $canvas->getWidth());
        $originY = calc_text_dst_y($y, ['max_height' => $bounds[3] - $bounds[1]], $canvas->getHeight());
        // 数值和百分比保留基线语义；符号对齐使用排版边界而非字体行高。
        if (is_array($x) || $x === 'center') {
            $originX -= $bounds[0];
        }
        if (is_array($y) || $y === 'center') {
            $mode = is_array($y) ? ($y[0] === 'custom' ? $y[1] : $y[0]) : $y;
            $originY -= $mode === 'center' ? $bounds[3] : $bounds[1];
        }
        foreach ($layout['items'] as $char) {
            if ($char['type'] === 'image') {
                $canvas->addImage($char['image'], (int) round($originX + $char['x']), (int) round($originY + $char['y'] - $char['height']));
                continue;
            }
            for ($index = 0; $index < $char['weight']; $index++) {
                $dx = $char['weight'] % 2 == 0 ? $index : 0;
                $dy = $char['weight'] % 2 == 0 ? 0 : $index;
                $this->paintLayoutCharacter($canvas, $char, $originX + $char['x'] + $dx, $originY + $char['y'] + $dy);
            }
        }
    }


    protected function singleImageTextSplit($graphics, $textColor = null)
    {
        $singleGraphics = [];

        if ($graphics instanceof TextGraphicsEngineInterface) {
            if (is_null($textColor)) {
                throw new PosterException('TextGraphicsEngineInterface must set textColor');
            }
            $textFont = $graphics->getFont();
            $textFontSize = $graphics->resolveFontSize();
            $textOriginFontSize = $graphics->getFontSize();
            $textFontAngle = $graphics->getFontAngle();
            $textFontSpace = $graphics->getFontSpace();
            $textFontWeight = $graphics->getFontWeight();
            $textLineHeight = $graphics->getLineHeight();
            $textTextAlign = $graphics->getFontAlign();
            $content = str_replace(["\r\n", "\r"], "\n", (string) $graphics->getText());
            foreach ($this->splitTextUnits($content) as $unit) {
                $singleGraphics[] = [
                    'type'        => 'text',
                    'text'        => $unit,
                    'color'       => $textColor,
                    'font'        => $textFont,
                    'size'        => $textFontSize,
                    'origin_size' => $textOriginFontSize,
                    'angle'       => $textFontAngle,
                    'space'       => $textFontSpace,
                    'weight'      => $textFontWeight,
                    'lineHeight'  => $textLineHeight,
                    'align'       => $textTextAlign,
                ];
            }
        } elseif ($graphics instanceof ImageGraphicsEngineInterface) {
            $singleGraphics[] = [
                'type'       => 'image',
                'image'      => $graphics,
                'width'      => $graphics->getWidth(),
                'height'     => $graphics->getHeight(),
                'lineHeight' => $graphics->getHeight(),
            ];
        }

        return $singleGraphics;
    }

    /** 兼容内部旧调用签名，排版不再使用经验补偿或原始字号重测。 */
    protected function autoWrap($characters, $maxWidth, $isSpring = true, $space = true, $calcOriginSize = false)
    {
        $engine = new \Kkokk\Poster\Image\Graphics\TextLayout();
        $layout = $engine->position($engine->build($characters, $maxWidth, function ($char) {
            return $this->measureLayoutCharacter($char);
        }), $this->getFontAlign());
        $lines = [];
        $widths = [];
        foreach ($layout['lines'] as $index => $line) {
            $widths[$index] = isset($line['width']) ? $line['width'] : 0;
            foreach (isset($line['items']) ? $line['items'] : [] as $char) {
                $lines = $this->addLineCharacters($lines, $index, $char);
            }
        }
        return [$lines, $layout['width'], $layout['bounds'][3] - $layout['bounds'][1], $widths];
    }

    protected function addLineCharacters($lines, $line, $char)
    {
        if (!isset($lines[$line])) {
            $lines[$line] = [];
        }
        $lines[$line][] = $char;
        return $lines;
    }
}
