<?php
require __DIR__ . '/text_fit.php';
foreach (['Gd','Imagick'] as $driver) {
    $prefix='Kkokk\\Poster\\Image\\'.$driver.'\\';
    $tc=$prefix.'Text'; $cc=$prefix.'Canvas'; $rc=$prefix.'ImageText';
    foreach ([0,30,-30] as $angle) {
        $text=(new $tc())->setText('中文Abc')->setFontSize(24)->setFontColor([0,0,0])->setFontAngle($angle)->setMaxWidth(90);
        foreach (['left','center','right'] as $h) foreach (['top','center','bottom'] as $v) {
            $a=new $cc(400,300,[255,255,255]);
            $b=new $cc(400,300,[255,255,255]);
            $m=$text->measure(150,$a); $bounds=$m['bounds'];
            $w=$bounds['right']-$bounds['left']; $height=$bounds['bottom']-$bounds['top'];
            $dx=$h==='center'?(150-$w)/2:($h==='right'?150-$w:0);
            $dy=$v==='center'?(100-$height)/2:($v==='bottom'?100-$height:0);
            $clone=clone $text;
            $clone->setMaxWidth(150)->draw($b,100+$dx-$bounds['left'],80+$dy-$bounds['top']);
            checkTextLayout($text->drawInBox($a,100,80,150,100,$h,$v)===$text, 'Box chaining failed');
            checkTextLayout($a->blob()===$b->blob(), 'Box positioning differs from baseline placement');
            checkTextLayout($text->getMaxWidth()===90, 'Box changed configured width');
        }
    }
    $rich=(new $rc())->addText($text);
    $canvas=new $cc(400,300,[255,255,255]);
    $rich->drawInBox($canvas,50,50,150,100,'center','center');
    checkTextLayout(textInkBounds($canvas->blob())!==null, 'Rich box did not draw');
    echo 'PASS: '.$driver." box alignments, rotation and configuration restore\n";
}