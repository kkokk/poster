<?php
require __DIR__ . '/text_layout_result.php';

$samples = ["e\xCC\x81", "\xE2\x9D\xA4\xEF\xB8\x8F", "\xF0\x9F\x91\x8D\xF0\x9F\x8F\xBD",
    "\xF0\x9F\x91\xA8\xE2\x80\x8D\xF0\x9F\x91\xA9\xE2\x80\x8D\xF0\x9F\x91\xA7",
    "\xF0\x9F\x87\xA8\xF0\x9F\x87\xB3"];
foreach (['Gd', 'Imagick'] as $driver) {
    $prefix = 'Kkokk\\Poster\\Image\\' . $driver . '\\';
    $tc = $prefix . 'Text'; $rc = $prefix . 'ImageText'; $cc = $prefix . 'Canvas';
    foreach ($samples as $sample) {
        $text = (new $tc())->setText($sample . "\n" . $sample)->setTextUnit('grapheme');
        $result = $text->layout(1);
        checkTextLayout(count($result['elements']) === 2 && $result['lineCount'] === 2, 'Cluster split on narrow line');
        foreach ($result['elements'] as $e) checkTextLayout($e['text'] === $sample, 'Incomplete cluster');
        // 缺字字体可能返回零advance；用确定可测的ASCII正文触发截断。
        $text->setText('AA')->setMaxLines(1, "e\xCC\x81");
        $result = $text->layout(1);
        checkTextLayout($result['elements'] === [], 'Oversized cluster marker partially retained');
    }
    $text = (new $tc())->setText("e\xCC\x81\r\n\r\nA")->setTextUnit('grapheme')->setFontColor([0,0,0]);
    $narrow = (new $tc())->setText("e\xCC\x81e\xCC\x81")->setTextUnit('grapheme')->layout(1);
    checkTextLayout($narrow['lineCount'] === 2 && count($narrow['elements']) === 2, 'Automatic wrap split a combining sequence');
    $canvas = new $cc(200,200,[255,255,255]); $replica = new $cc(200,200,[255,255,255]);
    $result = $text->layout(null, $canvas);
    checkTextLayout($result['textUnit'] === 'grapheme' && $result['lineCount'] === 3, 'CRLF normalization failed');
    $text->draw($canvas, 40, 60);
    foreach ($result['elements'] as $e) {
        $glyph = clone $text; $glyph->setText($e['text']);
        $glyph->draw($replica, 40+$e['x'], 60+$e['y']);
    }
    checkTextLayout($canvas->blob() === $replica->blob(), 'Grapheme anchors differ from rendering');
    $rich = (new $rc())->setTextUnit('grapheme')->addText((new $tc())->setText($samples[0]));
    checkTextLayout(count($rich->layout()['elements']) === 1, 'Outer rich text unit ignored');
    $split = (new $rc())->setTextUnit('grapheme')->addText((new $tc())->setText('e'))
        ->addText((new $tc())->setText("\xCC\x81"));
    checkTextLayout(count($split->layout()['elements']) === 2, 'Fragment boundary unexpectedly merged');
    checkTextLayout(count($text->setText($samples[0])->setTextUnit('codepoint')->layout()['elements']) === 2, 'Default unit not restored');
    try { $text->setTextUnit('unknown'); throw new RuntimeException('Invalid unit accepted'); }
    catch (Kkokk\Poster\Exception\PosterException $e) {}
    try { $text->setTextUnit('grapheme')->setText("\xFF")->measure(); throw new RuntimeException('Invalid UTF-8 accepted'); }
    catch (Kkokk\Poster\Exception\PosterException $e) {}
    echo 'PASS: ' . $driver . " grapheme grouping, clamp, rendering and boundaries\n";
}
