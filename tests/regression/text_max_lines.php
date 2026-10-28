<?php
require __DIR__ . '/text_measure.php';
foreach (['Kkokk\\Poster\\Image\\Gd\\Text','Kkokk\\Poster\\Image\\Imagick\\Text'] as $class) {
    $text = new $class();
    $text->setText('一二三四五六七八九十')->setFontSize(24)->setMaxWidth(60);
    if ($text->measure()['lineCount'] < 3) throw new RuntimeException('Fixture does not wrap');
    $text->setMaxLines(2, '...');
    $size = $text->measure();
    if ($size['lineCount'] !== 2 || $size['advanceWidth'] > 60) throw new RuntimeException('Line clamp failed');
    $text->setMaxLines(1)->setMaxWidth(1);
    if ($text->measure()['advanceWidth'] > 1) throw new RuntimeException('Narrow marker overflows');
    echo 'PASS: ' . $class . " line clamp\n";
}
foreach (['Gd', 'Imagick'] as $driver) {
    $prefix = 'Kkokk\\Poster\\Image\\' . $driver . '\\';
    $tc = $prefix . 'Text'; $cc = $prefix . 'Canvas'; $rc = $prefix . 'ImageText';
    $text = (new $tc())->setText("A\nB\nC")->setFontSize(24)->setFontColor([0,0,0])->setMaxLines(2);
    $expected = (new $tc())->setText("A\nB…")->setFontSize(24)->setFontColor([0,0,0]);
    $actualCanvas = new $cc(200,200,[255,255,255]);
    $expectedCanvas = new $cc(200,200,[255,255,255]);
    $text->draw($actualCanvas, 30, 50);
    $expected->draw($expectedCanvas, 30, 50);
    checkTextLayout($actualCanvas->blob() === $expectedCanvas->blob(), 'Clamped render differs from explicit expected text: ' . $driver);
    $rich = (new $rc())->addText((new $tc())->setText("A\nB\nC")->setFontSize(24)->setFontColor([0,0,0]))->setMaxLines(2);
    $richCanvas = new $cc(200,200,[255,255,255]);
    $rich->draw($richCanvas,30,50);
    checkTextLayout($richCanvas->blob() === $expectedCanvas->blob(), 'Rich clamp rendering differs: ' . $driver);
    checkTextLayout($rich->measure() === $text->measure(), 'Rich clamp measurement differs');
    $text->setText('')->setMaxLines(1);
    checkTextLayout($text->measure()['lineCount'] === 0, 'Empty clamp changed');
    $text->setText("A\nB")->setMaxLines(1, '');
    checkTextLayout($text->measure()['lineCount'] === 1, 'Empty marker failed');
    $text->setMaxLines(0);
    checkTextLayout($text->measure()['lineCount'] === 2, 'Unlimited reset failed');
    foreach ([-1, '2', 1.5] as $invalid) {
        try { $text->setMaxLines($invalid); throw new RuntimeException('Invalid line count accepted'); }
        catch (Kkokk\Poster\Exception\PosterException $e) {}
    }
    echo 'PASS: ' . $driver . " exact clamp rendering, rich text and edge cases\n";
}
foreach (['Gd', 'Imagick'] as $driver) {
    $prefix = 'Kkokk\\Poster\\Image\\' . $driver . '\\';
    $tc = $prefix.'Text'; $cc = $prefix.'Canvas'; $rc = $prefix.'ImageText'; $ic = $prefix.'Image';
    $blank = (new $tc())->setText("A\n\nB")->setFontSize(24)->setLineHeight(40)->setMaxLines(2, '');
    $metrics = $blank->measure();
    checkTextLayout($metrics['lineCount'] === 2 && $metrics['lines'][1]['baseline'] == 40 && $metrics['lines'][1]['lineHeight'] == 40, 'Blank line height lost');
    $iconCanvas = new $cc(15,15,[0,0,0]);
    $icon = new $ic($iconCanvas->blob());
    $rich = (new $rc())->setMaxWidth(85)->setMaxLines(2, '...')
        ->addText((new $tc())->setText('Abcd')->setFontSize(18)->setFontColor([0,0,0]))
        ->addImage($icon)
        ->addText((new $tc())->setText('中文中文中文中文中文')->setFontSize(28)->setFontColor([0,0,0]));
    $canvas = new $cc(250,250,[255,255,255]);
    $m = $rich->measure(null,$canvas);
    checkTextLayout($m['lineCount'] === 2 && $m['advanceWidth'] <= 85, 'Mixed image line clamp overflow');
    $rich->draw($canvas,50,80);
    $ink = textInkBounds($canvas->blob());
    checkTextLayout($ink !== null && $ink[2] <= ceil(50+$m['bounds']['right'])+3 && $ink[3] <= ceil(80+$m['bounds']['bottom'])+3, 'Mixed image render exceeds bounds');
    echo 'PASS: ' . $driver . " blank line height and mixed image/size clamp\n";
}