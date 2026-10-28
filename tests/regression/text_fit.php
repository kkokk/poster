<?php
require __DIR__ . '/text_max_lines.php';
foreach (['Gd','Imagick'] as $driver) {
    $class = 'Kkokk\\Poster\\Image\\'.$driver.'\\Text';
    $text = (new $class())->setText('中文文字自动字号测试')->setFontSize(32)->setMaxWidth(80)->setMaxLines(1);
    $before = $text->measure();
    $result = $text->fitFontSize(100,60,8,40);
    checkTextLayout($result['fits'] && $result['fontSize'] >= 8, 'No fitting size');
    checkTextLayout($text->getFontSize() === 32 && $text->getMaxWidth() === 80 && $text->measure() === $before, 'Fitting mutated original');
    $probe = clone $text;
    $probe->setMaxLines(0)->setFontSize($result['fontSize']);
    checkTextLayout($probe->measure(100) === $result['metrics'], 'Result does not match full text measure');
    if ($result['fontSize'] < 40) {
        $probe->setFontSize($result['fontSize']+1);
        $larger=$probe->measure(100);
        checkTextLayout($larger['width']>100 || $larger['height']>60, 'Result is not largest fitting size');
    }
    checkTextLayout(!$text->fitFontSize(1,1,20,22)['fits'], 'Impossible box accepted');
    echo 'PASS: '.$driver." font fitting, maximal size and no mutation\n";
}
foreach (['Gd','Imagick'] as $driver) {
    $prefix='Kkokk\\Poster\\Image\\'.$driver.'\\';
    $tc=$prefix.'Text'; $cc=$prefix.'Canvas'; $rc=$prefix.'ImageText';
    foreach ([0,30,-30] as $angle) {
        $text=(new $tc())->setText('自动字号Abcd')->setFontColor([0,0,0])->setFontAngle($angle)->setFontSpace(2);
        $canvas=new $cc(400,300,[255,255,255]);
        $before=$canvas->blob();
        $fit=$text->fitFontSize(150,100,8,40,$canvas);
        checkTextLayout($fit['fits'] && $canvas->blob()===$before, 'Fit failed or painted');
        $text->setFontSize($fit['fontSize'])->setMaxWidth(150)->setMaxLines(0);
        $metrics=$text->measure(null,$canvas);
        checkTextLayout($metrics===$fit['metrics'], 'Applied size differs');
        $text->draw($canvas,100,150);
        $ink=textInkBounds($canvas->blob());
        $b=$metrics['bounds'];
        checkTextLayout($ink[0]>=floor(100+$b['left'])-3 && $ink[1]>=floor(150+$b['top'])-3 && $ink[2]<=ceil(100+$b['right'])+3 && $ink[3]<=ceil(150+$b['bottom'])+3, 'Fit render exceeds measured bounds');
    }
    $empty=(new $tc())->setText('')->fitFontSize(10,10,8,40);
    checkTextLayout($empty['fits'] && $empty['fontSize']===40, 'Empty text should use maximum');
    foreach ([[0,10,8,40],[10,-1,8,40],[INF,10,8,40],[10,10,20,8],[10,10,8.5,40],[10,10,1,1002]] as $args) {
        try { call_user_func_array([(new $tc()),'fitFontSize'],$args); throw new RuntimeException('Invalid fit accepted'); }
        catch (Kkokk\Poster\Exception\PosterException $e) {}
    }
    try { (new $rc())->fitFontSize(10,10); throw new RuntimeException('Rich fitting accepted'); }
    catch (Kkokk\Poster\Exception\PosterException $e) {}
    echo 'PASS: '.$driver." fitting render/rotation, canvas isolation and validation\n";
}