<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
error_reporting(E_ALL);
set_error_handler(function ($s,$m,$f,$l) { throw new ErrorException($m,0,$s,$f,$l); });
function checkAlpha($ok,$message) { if (!$ok) throw new RuntimeException($message); }
function alphaFixture($mask = false)
{
    $image=imagecreatetruecolor(3,2); imagealphablending($image,false); imagesavealpha($image,true);
    for ($x=0;$x<3;$x++) for ($y=0;$y<2;$y++) {
        $v=[0,128,255];
        $color=$mask?imagecolorallocatealpha($image,$v[$x],$v[$x],$v[$x],0)
            :imagecolorallocatealpha($image,240,30,60,[0,50,127][$y===0?$x:1]);
        imagesetpixel($image,$x,$y,$color);
    }
    ob_start(); imagepng($image); $blob=ob_get_clean(); imagedestroy($image); return $blob;
}
foreach (['Gd','Imagick'] as $driver) {
    $class='Kkokk\\Poster\\Image\\'.$driver.'\\Image';
    $source=new $class(alphaFixture()); $source->applyMask(alphaFixture(true));
    $decoded=imagecreatefromstring($source->blob());
    for ($x=0;$x<3;$x++) for ($y=0;$y<2;$y++) {
        $sourceAlpha=[0,50,127][$y===0?$x:1]; $gray=[0,128,255][$x];
        $expected=(int)round(127-(127-$sourceAlpha)*$gray/255);
        $c=imagecolorsforindex($decoded,imagecolorat($decoded,$x,$y));
        checkAlpha(abs($c['alpha']-$expected)<=1, $driver.' mask alpha mismatch '.json_encode([$x,$y,$c,$expected]));
        if ($expected<127) checkAlpha($c['red']===240 && $c['green']===30 && $c['blue']===60, $driver.' mask changed RGB');
    }
    imagedestroy($decoded);
    $source=new $class(alphaFixture()); $calls=[];
    $cut=$source->cutout(0,0,3,2,function ($point) use (&$calls) { $calls[]=$point; return $point[0]===1; });
    checkAlpha($calls===[[0,0],[0,1],[1,0],[1,1],[2,0],[2,1]], 'Callback order changed');
    $decoded=imagecreatefromstring($cut->blob());
    for ($x=0;$x<3;$x++) for ($y=0;$y<2;$y++) {
        $c=imagecolorsforindex($decoded,imagecolorat($decoded,$x,$y));
        checkAlpha(abs($c['alpha']-($x===1?50:127))<=1, $driver.' cutout alpha mismatch '.json_encode($c));
        if ($x===1) checkAlpha($c['red']===240 && $c['green']===30 && $c['blue']===60, $driver.' cutout changed RGB');
    }
    imagedestroy($decoded);
    $cut=$source->cutout(1,0,1,2); $decoded=imagecreatefromstring($cut->blob());
    $c=imagecolorsforindex($decoded,imagecolorat($decoded,0,0));
    checkAlpha(abs($c['alpha']-50)<=1, 'Cutout offset alpha lost'); imagedestroy($decoded);
    echo 'PASS: '.$driver." mask multiplicative opacity and cutout alpha/callback/offset\n";
}

// 覆盖所有灰度值和源alpha，允许8位PNG量化带来的1级误差。
$src=imagecreatetruecolor(256,1); $mask=imagecreatetruecolor(256,1);
imagealphablending($src,false); imagesavealpha($src,true);
for ($x=0;$x<256;$x++) {
 imagesetpixel($src,$x,0,imagecolorallocatealpha($src,240,30,60,$x%128));
 imagesetpixel($mask,$x,0,imagecolorallocate($mask,$x,$x,$x));
}
ob_start(); imagepng($src); $srcBlob=ob_get_clean();
ob_start(); imagepng($mask); $maskBlob=ob_get_clean();
imagedestroy($src); imagedestroy($mask);
foreach (['Gd','Imagick'] as $driver) {
 $class='Kkokk\\Poster\\Image\\'.$driver.'\\Image';
 $decoded=imagecreatefromstring((new $class($srcBlob))->applyMask($maskBlob)->blob());
 for ($x=0;$x<256;$x++) {
  $actual=imagecolorsforindex($decoded,imagecolorat($decoded,$x,0));
  $expected=(int)round(127-(127-$x%128)*$x/255);
  checkAlpha(abs($actual['alpha']-$expected)<=1,$driver.' grayscale ramp error at '.$x.': '.$actual['alpha'].' expected '.$expected);
 }
 imagedestroy($decoded);
 echo 'PASS: '.$driver." full grayscale/alpha ramp within one alpha level\n";
}
