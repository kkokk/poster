<?php
require __DIR__ . '/text_grapheme.php';
foreach (['Gd','Imagick'] as $driver) {
    $prefix='Kkokk\\Poster\\Image\\'.$driver.'\\';
    $tc=$prefix.'Text'; $cc=$prefix.'Canvas'; $rc=$prefix.'ImageText'; $ic=$prefix.'Image';
    foreach ([0,30,-30] as $angle) {
        $canvas=new $cc(320,250,[255,255,255]);
        $text=(new $tc())->setText("Ae\xCC\x81\n\nB")->setTextUnit('grapheme')
            ->setFontSize(28)->setFontAngle($angle)->setMaxWidth(120)->setFontAlign('center');
        $getCanvas=new ReflectionMethod($text,'getCanvas'); $getCanvas->setAccessible(true);
        $metrics=$text->measure(null,$canvas); $previous=$getCanvas->invoke($text);
        $preview=$text->debugLayout($canvas,80,90);
        checkTextLayout($preview !== $canvas && $preview->getImage() !== $canvas->getImage(), 'Preview shares image');
        checkTextLayout($getCanvas->invoke($text)===$previous && $text->measure(null,$canvas)===$metrics, 'Debug changed configuration');
        checkTextLayout(textInkBounds($canvas->blob())===null, 'Debug changed source canvas');
        $layout=$text->layout(null,$canvas); $first=$layout['elements'][0];
        $image=imagecreatefromstring($preview->blob());
        $px=(int)round(80+$first['x']); $py=(int)round(90+$first['y']);
        $color=imagecolorsforindex($image,imagecolorat($image,$px,$py));
        checkTextLayout($color['red']>=140 && $color['red']<=180 && $color['green']<30 && $color['blue']>170, 'Anchor cross misplaced');
        checkTextLayout(imagesx($image)===320 && imagesy($image)===250, 'Preview size changed');
        imagedestroy($image);
    }
    $rich=(new $rc())->addText((new $tc())->setText('AB'))->addImage(new $ic((new $cc(12,16,[0,0,0]))->blob()));
    checkTextLayout(textInkBounds($rich->debugLayout($canvas,40,80)->blob())!==null, 'Rich debug missing');
    $empty=(new $tc())->setText('')->debugLayout($canvas,40,80);
    checkTextLayout(textInkBounds($empty->blob())===null, 'Empty layout has debug marks');
    try { $text->debugLayout($canvas,'center',0); throw new RuntimeException('Symbolic debug coordinate accepted'); }
    catch (Kkokk\Poster\Exception\PosterException $e) {}
    echo 'PASS: '.$driver." layout debug isolation, anchors, rotation and rich content\n";
}
