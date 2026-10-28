<?php
require __DIR__ . '/text_box.php';
$engine = new Kkokk\Poster\Image\Graphics\TextLayout();
$chars=[];
foreach (str_split('AA BBB') as $char) $chars[]=['type'=>'text','text'=>$char,'font'=>'test','size'=>10,'weight'=>1,'space'=>0,'lineHeight'=>10];
$measure=function ($char) { return ['advance'=>10,'left'=>0,'top'=>-10,'right'=>10,'bottom'=>0]; };
$layout=$engine->build($chars,40,$measure,'word');
$line=''; foreach ($layout['lines'][1]['items'] as $char) $line.=$char['text'];
checkTextLayout($line==='BBB', 'Word split despite fitting on next line');
$layout=$engine->build($chars,20,$measure,'word');
checkTextLayout($layout['width']<=20, 'Long word overflow');
foreach (['Gd','Imagick'] as $driver) {
    $prefix='Kkokk\\Poster\\Image\\'.$driver.'\\'; $tc=$prefix.'Text'; $cc=$prefix.'Canvas';
    $text=(new $tc())->setText('Hello world poster')->setFontSize(24)->setFontColor([0,0,0])->setMaxWidth(95)->setWrapMode('word');
    $canvas=new $cc(300,300,[255,255,255]);
    $m=$text->measure(null,$canvas);
    $text->draw($canvas,40,80);
    checkTextLayout($m['lineCount']>=2 && $m['advanceWidth']<=95 && textInkBounds($canvas->blob())!==null, 'Word wrap rendering failed');
    echo 'PASS: '.$driver." optional word wrapping\n";
}
foreach (['Gd','Imagick'] as $driver) {
    $prefix='Kkokk\\Poster\\Image\\'.$driver.'\\'; $tc=$prefix.'Text'; $rc=$prefix.'ImageText'; $cc=$prefix.'Canvas';
    $plain=(new $tc())->setText('AA BBB CCC DDD')->setFontSize(24)->setMaxWidth(90)->setWrapMode('word');
    $rich=(new $rc())->setMaxWidth(90)->setWrapMode('word')
        ->addText((new $tc())->setText('AA B')->setFontSize(24))
        ->addText((new $tc())->setText('BB CCC DDD')->setFontSize(24));
    checkTextLayout($plain->measure()===$rich->measure(), 'Word wrapping changes across text segments');
    $rich->setMaxLines(2,'...');
    $canvas=new $cc(250,250,[255,255,255]);
    $m=$rich->measure(null,$canvas);
    checkTextLayout($m['lineCount']===2 && $m['advanceWidth']<=90, 'Word/clamp combination overflow');
    $rich->draw($canvas,30,80);
    checkTextLayout(textInkBounds($canvas->blob())!==null, 'Word/clamp combination did not render');
    $rich=(new $rc())->setMaxWidth(100)->setWrapMode('word')
        ->addText((new $tc())->setText('AA B')->setFontSize(18))
        ->addText((new $tc())->setText('BB中文')->setFontSize(28));
    checkTextLayout($rich->measure()['advanceWidth']<=100, 'Mixed font word overflow');
    $plain->setText("中文\nABC")->setMaxWidth(300);
    checkTextLayout($plain->measure()['lineCount']===2, 'Explicit newline lost');
    try { $plain->setWrapMode('unknown'); throw new RuntimeException('Unknown strategy accepted'); }
    catch (Kkokk\Poster\Exception\PosterException $e) {}
    echo 'PASS: '.$driver." word segments, mixed fonts, clamp and explicit newline\n";
}