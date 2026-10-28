<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) { throw new ErrorException($message, 0, $severity, $file, $line); });
function checkFit($condition, $message) { if (!$condition) throw new RuntimeException($message); }
$source = imagecreatetruecolor(60, 20);
imagealphablending($source, false); imagesavealpha($source, true);
imagefilledrectangle($source, 0, 0, 19, 19, imagecolorallocatealpha($source, 255, 0, 0, 0));
imagefilledrectangle($source, 20, 0, 39, 19, imagecolorallocatealpha($source, 0, 255, 0, 0));
imagefilledrectangle($source, 40, 0, 59, 19, imagecolorallocatealpha($source, 0, 0, 255, 0));
imagesetpixel($source, 30, 10, imagecolorallocatealpha($source, 0, 0, 0, 100));
ob_start(); imagepng($source); $blob = ob_get_clean(); imagedestroy($source);
foreach (['Gd', 'Imagick'] as $driver) {
    $class = 'Kkokk\\Poster\\Image\\' . $driver . '\\Image';
    $original = new $class($blob); $before = $original->blob();
    foreach (['contain' => [30,10], 'cover' => [30,30], 'fill' => [30,30]] as $mode => $dimensions) {
        $image = $original->copy();
        checkFit($image->fit(30,30,$mode) === $image, 'Not chainable');
        $size = getimagesizefromstring($image->blob());
        checkFit([$image->getWidth(),$image->getHeight()] === $dimensions && [$size[0],$size[1]] === $dimensions, 'Wrong fit size');
    }
    foreach ([0 => 0, 1 => 2] as $focus => $channel) {
        $image = $original->copy()->fit(20,20,'cover',$focus,0.5);
        $decoded = imagecreatefromstring($image->blob());
        $color = imagecolorsforindex($decoded,imagecolorat($decoded,10,10));
        checkFit($color[$channel === 0 ? 'red' : 'blue'] > 240, 'Crop focus incorrect');
        imagedestroy($decoded);
    }
    $image = $original->copy()->fit(60,20,'fill');
    $decoded = imagecreatefromstring($image->blob());
    $color = imagecolorsforindex($decoded,imagecolorat($decoded,30,10));
    checkFit(abs($color['alpha']-100)<=1, 'Alpha lost'); imagedestroy($decoded);
    $portrait = $original->copy()->fit(20,60,'fill')->fit(30,30,'contain');
    checkFit($portrait->getWidth()===10 && $portrait->getHeight()===30, 'Portrait ratio incorrect');
    $tiny = $original->copy()->fit(1,1);
    checkFit($tiny->getWidth()===1 && $tiny->getHeight()===1, 'Tiny output failed');
    $decoded = imagecreatefromstring($blob); $vertical = imagerotate($decoded, 90, 0);
    ob_start(); imagepng($vertical); $verticalBlob = ob_get_clean();
    imagedestroy($decoded); imagedestroy($vertical);
    foreach ([0,1] as $focus) {
        $image = (new $class($verticalBlob))->fit(20,20,'cover',0.5,$focus);
        $decoded = imagecreatefromstring($image->blob());
        $color = imagecolorsforindex($decoded,imagecolorat($decoded,10,10));
        checkFit($color[$focus === 0 ? 'blue' : 'red'] > 240, 'Vertical focus incorrect');
        imagedestroy($decoded);
    }
    $alpha = imagecreatetruecolor(10,10); imagealphablending($alpha,false); imagesavealpha($alpha,true);
    imagefill($alpha,0,0,imagecolorallocatealpha($alpha,20,40,60,80));
    ob_start(); imagepng($alpha); $alphaBlob=ob_get_clean(); imagedestroy($alpha);
    foreach (['contain','cover','fill'] as $mode) {
        $decoded=imagecreatefromstring((new $class($alphaBlob))->fit(20,30,$mode)->blob());
        $color=imagecolorsforindex($decoded,imagecolorat($decoded,5,5));
        checkFit(abs($color['alpha']-80)<=1, 'Resizing alpha changed'); imagedestroy($decoded);
    }
    foreach ([[0,20], [20.5,20], [20,20,'bad'], [20,20,'cover',-0.1], [20,20,'cover',0.5,INF]] as $args) {
        try { call_user_func_array([$original,'fit'],$args); throw new RuntimeException('Invalid arguments accepted'); }
        catch (Kkokk\Poster\Exception\PosterException $e) {}
    }
    // 编码元数据可能含时间，比较解码后像素与尺寸而非PNG字节。
    $a = imagecreatefromstring($before); $b = imagecreatefromstring($original->blob());
    checkFit(imagesx($a) === imagesx($b) && imagesy($a) === imagesy($b), 'Original dimensions changed');
    for ($y=0; $y<imagesy($a); $y++) for ($x=0; $x<imagesx($a); $x++) {
        checkFit(imagecolorsforindex($a,imagecolorat($a,$x,$y)) === imagecolorsforindex($b,imagecolorat($b,$x,$y)), 'Copy fitting mutated original');
    }
    imagedestroy($a); imagedestroy($b);
    echo 'PASS: '.$driver." fit dimensions, focus, alpha, isolation and validation\n";
}
