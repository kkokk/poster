<?php
require __DIR__ . '/text_wrap.php';

foreach (['Gd', 'Imagick'] as $driver) {
    $prefix = 'Kkokk\\Poster\\Image\\' . $driver . '\\';
    $tc = $prefix . 'Text'; $cc = $prefix . 'Canvas'; $rc = $prefix . 'ImageText'; $ic = $prefix . 'Image';
    foreach ([0, 30, -30] as $angle) {
        $canvas = new $cc(400, 350, [255,255,255]);
        $replica = new $cc(400, 350, [255,255,255]);
        $text = (new $tc())->setText("AA BBB\n\nCCC DDD")
            ->setFontSize(24)->setFontColor([0,0,0])->setFontAngle($angle)
            ->setFontAlign('center')->setMaxWidth(90)->setWrapMode('word')->setMaxLines(3, '...');
        $before = $canvas->blob();
        $result = $text->layout(null, $canvas);
        $metrics = $result; unset($metrics['elements'], $metrics['textUnit']);
        checkTextLayout($metrics === $text->measure(null, $canvas), 'Layout and measure disagree');
        checkTextLayout($canvas->blob() === $before && $text->getMaxWidth() === 90, 'Layout changed canvas or configuration');
        $text->draw($canvas, 100, 140);
        foreach ($result['elements'] as $element) {
            checkTextLayout($element['line'] < $result['lineCount'] && !isset($element['image']), 'Invalid element projection');
            $glyph = clone $text;
            $glyph->setText($element['text'])->setFontAlign('left')->setMaxLines(0);
            $glyph->draw($replica, 100 + $element['x'], 140 + $element['y']);
            $b = $element['bounds']; $outer = $result['bounds'];
            checkTextLayout($b['left'] >= $outer['left'] && $b['right'] <= $outer['right']
                && $b['top'] >= $outer['top'] && $b['bottom'] <= $outer['bottom'], 'Element outside layout bounds');
        }
        checkTextLayout($canvas->blob() === $replica->blob(), 'Published element anchors do not reconstruct rendering');
    }
    $icon = new $ic((new $cc(12, 17, [0,0,0]))->blob());
    $rich = (new $rc())->addText((new $tc())->setText("A\n\n"))->addImage($icon);
    $result = $rich->layout(100);
    $last = end($result['elements']);
    checkTextLayout($last['type'] === 'image' && $last['line'] === 2 && $last['width'] === 12
        && $last['height'] === 17 && $last['bounds']['top'] === $last['y'] - 17, 'Image anchor or blank line incorrect');
    checkTextLayout(json_decode(json_encode($result), true) !== null, 'Layout is not serializable data');
    checkTextLayout((new $tc())->setText('')->layout()['elements'] === [], 'Empty text has elements');
    echo 'PASS: ' . $driver . " public layout snapshot and rendering reconstruction\n";
}
