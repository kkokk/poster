<?php

// 复用真实像素边界扫描，并先验证测量所依赖的共享布局。
require __DIR__ . '/text_layout.php';

foreach (['gd', 'imagick'] as $driver) {
    $prefix = 'Kkokk\\Poster\\Image\\' . ($driver === 'gd' ? 'Gd' : 'Imagick') . '\\';
    $textClass = $prefix . 'Text'; $canvasClass = $prefix . 'Canvas'; $richClass = $prefix . 'ImageText';
    foreach (['simkai.ttf', 'zhankukuheiti.ttf'] as $font) {
        foreach ([0, 30, -30] as $angle) {
            $text = (new $textClass())->setText('中文Agjp123')->setFont(POSTER_BASE_PATH . '/style/' . $font)
                ->setFontSize(32)->setFontColor([0, 0, 0])->setFontAngle($angle)->setFontSpace(2)->setFontWeight(2);
            $standalone = $text->measure();
            $canvas = new $canvasClass(500, 400, [255, 255, 255]);
            $before = $canvas->blob();
            $metrics = $text->measure(null, $canvas);
            checkTextLayout($canvas->blob() === $before, 'Measurement painted or changed canvas.');
            checkTextLayout(abs($standalone['width'] - $metrics['width']) <= 1 && abs($standalone['height'] - $metrics['height']) <= 1, 'Standalone context differs from default canvas.');
            $text->draw($canvas, 100, 200);
            $ink = textInkBounds($canvas->blob());
            $b = $metrics['bounds'];
            checkTextLayout($ink[0] >= floor(100 + $b['left']) - 3 && $ink[1] >= floor(200 + $b['top']) - 3
                && $ink[2] <= ceil(100 + $b['right']) + 3 && $ink[3] <= ceil(200 + $b['bottom']) + 3, 'Measured bounds do not enclose render: ' . $driver . ' ' . $font);
            if ($angle === 0) {
                // 字体度量可能包含侧边承留白，不能以墨迹像素尺寸充当布局契约。
                checkTextLayout(abs($metrics['width'] - ($ink[2] - $ink[0] + 1)) <= 8
                    && abs($metrics['height'] - ($ink[3] - $ink[1] + 1)) <= 8, 'Unrotated measurement is not close to ink bounds: ' . $driver . ' ' . $font . ' ' . json_encode([$metrics, $ink]));
            }
            checkTextLayout((new $richClass())->addText($text)->measure() === $standalone, 'Rich/plain measurement differ.');
            checkTextLayout($text->getFontSize() === 32 && $text->getMaxWidth() === 0, 'Measurement mutated text configuration.');
        }
    }
    $text = (new $textClass())->setText('中文中文中文')->setFontSize(32)->setMaxWidth(65);
    checkTextLayout($text->measure()['lineCount'] === 3, 'Configured wrapping not inherited.');
    checkTextLayout($text->measure(0)['lineCount'] === 1 && $text->getMaxWidth() === 65, 'Unlimited override mutated configuration.');
    $text->setMaxWidth(0);
    $narrow = new $canvasClass(65, 300, [255, 255, 255]);
    checkTextLayout($text->measure(null, $narrow)['lineCount'] === 3, 'Canvas width fallback does not match drawing.');
    $text->setText("A\r\nB\nC")->setLineHeight(40);
    $metrics = $text->measure();
    checkTextLayout($metrics['lineCount'] === 3 && $metrics['lines'][2]['baseline'] == 80, 'Newline or baseline metadata incorrect.');
    $text->setText('');
    $metrics = $text->measure();
    checkTextLayout($metrics['width'] === 0 && $metrics['height'] === 0 && $metrics['lineCount'] === 0 && $metrics['lines'] === [], 'Empty metrics incorrect.');
    $text->setText('A ');
    $space = $text->measure();
    $text->setText('A');
    checkTextLayout($space['advanceWidth'] > $text->measure()['advanceWidth'], 'Trailing space has no advance.');
    foreach ([-1, '100', INF, NAN] as $invalid) {
        $thrown = false;
        try {
            $text->measure($invalid);
        } catch (Kkokk\Poster\Exception\PosterException $e) {
            $thrown = true;
        }
        checkTextLayout($thrown, 'Invalid measurement width was accepted.');
    }
}
echo "PASS: text measurement, standalone/canvas contexts, actual render bounds, width overrides, newlines, empty and trailing spaces, no mutation.\n";
