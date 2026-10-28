<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

use Kkokk\Poster\Image\Graphics\TextLayout;

error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

function checkTextLayout($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function textInkBounds($blob)
{
    $image = imagecreatefromstring($blob);
    imagepalettetotruecolor($image);
    $bounds = null;
    for ($y = 0; $y < imagesy($image); $y++) {
        for ($x = 0; $x < imagesx($image); $x++) {
            $rgb = imagecolorat($image, $x, $y);
            if ((($rgb >> 16) & 255) < 180 && (($rgb >> 8) & 255) < 180 && ($rgb & 255) < 180) {
                $bounds = $bounds === null ? [$x, $y, $x, $y] : [min($x, $bounds[0]), min($y, $bounds[1]), max($x, $bounds[2]), max($y, $bounds[3])];
            }
        }
    }
    imagedestroy($image);
    return $bounds;
}

// 确定性度量验证布局本身，避免用某个驱动的输出代替换行规则。
$engine = new TextLayout();
$char = ['type' => 'text', 'text' => 'A', 'font' => 'test', 'size' => 12, 'weight' => 1,
    'lineHeight' => 20, 'space' => 3, 'angle' => 0];
$measure = function ($entry) { return ['left' => 0, 'top' => -10, 'right' => 10, 'bottom' => 2, 'advance' => 10]; };
$layout = $engine->position($engine->build([$char, $char, $char], 10, $measure), 'left');
checkTextLayout(count($layout['lines']) === 3 && $layout['bounds'][3] - $layout['bounds'][1] == 52, 'Wrapped baseline height is incorrect.');
$layout = $engine->position($engine->build([$char], 1, $measure), 'left');
checkTextLayout(count($layout['lines']) === 1, 'Oversized first glyph created an empty line.');
$layout = $engine->position($engine->build([], 10, $measure), 'left');
checkTextLayout($layout['items'] === [] && $layout['bounds'] === [0, 0, 0, 0], 'Empty text layout is not empty.');
$layout = $engine->position($engine->build([$char, $char], 30, $measure), 'left');
checkTextLayout($layout['width'] == 23 && $layout['items'][1]['x'] === 13.0, 'Spacing is not included exactly once between glyphs.');
$newline = $char; $newline['text'] = "\n";
$layout = $engine->position($engine->build([$char, $newline, $char], 100, $measure), 'left');
checkTextLayout(count($layout['lines']) === 2 && $layout['items'][1]['y'] === 20.0, 'Explicit newline not respected.');
$imageItem = ['type' => 'image', 'width' => 15, 'height' => 30, 'lineHeight' => 30];
$layout = $engine->position($engine->build([$char, $imageItem, $char], 20, $measure), 'right');
checkTextLayout(count($layout['lines']) === 3 && $layout['items'][1]['y'] === 30.0 && $layout['bounds'][3] >= 62, 'Mixed image baseline or wrapping is incorrect.');

$results = [];
foreach (['simkai.ttf', 'zhankukuheiti.ttf'] as $font) {
    foreach (['gd', 'imagick'] as $driver) {
        $prefix = 'Kkokk\\Poster\\Image\\' . ($driver === 'gd' ? 'Gd' : 'Imagick') . '\\';
        $canvasClass = $prefix . 'Canvas'; $textClass = $prefix . 'Text'; $richClass = $prefix . 'ImageText';
        foreach ([0, 30, -30] as $angle) {
            foreach ([false, true] as $rich) {
                $canvas = new $canvasClass(500, 300, [255, 255, 255]);
                $text = (new $textClass())->setText('中文Agjp123')->setFont(POSTER_BASE_PATH . '/style/' . $font)
                    ->setFontSize(32)->setFontColor([0, 0, 0])->setFontAngle($angle);
                checkTextLayout($text->resolveFontSize() == ($driver === 'gd' ? 24 : 32), 'GD 3/4 conversion changed.');
                if ($rich) {
                    (new $richClass())->addText($text)->draw($canvas, 100, 160);
                } else {
                    $text->draw($canvas, 100, 160);
                }
                $results[$font][$driver][$angle][$rich ? 'rich' : 'plain'] = textInkBounds($canvas->blob());
            }
            checkTextLayout($results[$font][$driver][$angle]['plain'] === $results[$font][$driver][$angle]['rich'], 'Plain and rich text diverge.');
        }
        // 多行居中应落在画布中心，不得将整个文本块推向底部。
        $canvas = new $canvasClass(500, 300, [255, 255, 255]);
        $text = (new $textClass())->setText("中文Agjp\n123中文")->setFont(POSTER_BASE_PATH . '/style/' . $font)
            ->setFontSize(32)->setFontColor([0, 0, 0])->setLineHeight(48);
        $text->draw($canvas, 'center', 'center');
        $bounds = textInkBounds($canvas->blob());
        checkTextLayout(abs(($bounds[0] + $bounds[2]) / 2 - 250) <= 4 && abs(($bounds[1] + $bounds[3]) / 2 - 150) <= 4, 'Multiline centering is inaccurate.');
        $text->setText('');
        $empty = new $canvasClass(100, 100, [255, 255, 255]);
        $text->draw($empty, 0, 0);
        checkTextLayout(textInkBounds($empty->blob()) === null, 'Empty text painted pixels.');
        // 字间距和加粗应在两条入口产生相同输出，避免富文本另走一套规则。
        foreach ([1, 2, 3] as $weight) {
            $pair = [];
            foreach ([false, true] as $rich) {
                $canvas = new $canvasClass(500, 300, [255, 255, 255]);
                $text = (new $textClass())->setText('A g中')->setFont(POSTER_BASE_PATH . '/style/' . $font)
                    ->setFontSize(32)->setFontColor([0, 0, 0])->setFontSpace(5)->setFontWeight($weight);
                if ($rich) {
                    (new $richClass())->addText($text)->draw($canvas, 100, 160);
                } else {
                    $text->draw($canvas, 100, 160);
                }
                $pair[] = textInkBounds($canvas->blob());
            }
            checkTextLayout($pair[0] === $pair[1], 'Spacing or weight differs between plain and rich text.');
        }
        $imageClass = $prefix . 'Image';
        $tile = new $canvasClass(16, 24, [0, 0, 0]);
        $icon = new $imageClass($tile->blob());
        $small = (new $textClass())->setText('A')->setFont(POSTER_BASE_PATH . '/style/' . $font)->setFontSize(16)->setFontColor([0, 0, 0]);
        $large = (new $textClass())->setText('中')->setFont(POSTER_BASE_PATH . '/style/' . $font)->setFontSize(40)->setFontColor([0, 0, 0]);
        $mixed = new $canvasClass(500, 300, [255, 255, 255]);
        (new $richClass())->addText($small)->addImage($icon)->addText($large)->draw($mixed, 100, 160);
        $results[$font][$driver]['mixed'] = textInkBounds($mixed->blob());
    }
    foreach ([0, 30, -30] as $angle) {
        $gd = $results[$font]['gd'][$angle]['plain'];
        $imagick = $results[$font]['imagick'][$angle]['plain'];
        foreach ($gd as $axis => $value) {
            checkTextLayout(abs($value - $imagick[$axis]) <= 4, 'Driver bounds differ: ' . $font . ', angle ' . $angle . ', ' . json_encode([$gd, $imagick]));
        }
    }
    foreach ($results[$font]['gd']['mixed'] as $axis => $value) {
        checkTextLayout(abs($value - $results[$font]['imagick']['mixed'][$axis]) <= 4, 'Mixed sizes and image baseline diverge.');
    }
}
restore_error_handler();
echo "PASS: shared layout, wrapping, spacing, newlines, empty text; two fonts, both drivers, plain/rich, centering and +/-30 degree bounds.\n";
