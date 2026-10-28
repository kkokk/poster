<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
error_reporting(E_ALL);
set_error_handler(function ($s,$m,$f,$l) { throw new ErrorException($m,0,$s,$f,$l); });
function checkCutout($condition,$message) { if (!$condition) throw new RuntimeException($message); }
$fixture=imagecreatetruecolor(13,9); imagealphablending($fixture,false); imagesavealpha($fixture,true);
for ($x=0;$x<13;$x++) for ($y=0;$y<9;$y++) {
 imagesetpixel($fixture,$x,$y,imagecolorallocatealpha($fixture,$x*19,$y*29,77,($x*17+$y*11)%128));
}
ob_start(); imagepng($fixture); $blob=ob_get_clean(); imagedestroy($fixture);
foreach ([[0,0,13,9],[2,3,5,4],[-3,-2,19,14],[12,8,4,4],[20,20,3,3]] as $box) {
 $source=new Kkokk\Poster\Image\Imagick\Image($blob);
 $native=call_user_func_array([$source,'cutout'],$box);
 // true回调保留原逐点路径，作为相同输入的像素对照。
 $args=$box; $args[]=function ($point) { return true; };
 $legacy=call_user_func_array([$source,'cutout'],$args);
 $a=imagecreatefromstring($native->blob()); $b=imagecreatefromstring($legacy->blob());
 for ($y=0;$y<$box[3];$y++) for ($x=0;$x<$box[2];$x++) {
  $ac=imagecolorsforindex($a,imagecolorat($a,$x,$y)); $bc=imagecolorsforindex($b,imagecolorat($b,$x,$y));
  checkCutout($ac===$bc,'Native/point pixel mismatch '.json_encode([$box,$x,$y,$ac,$bc]));
 }
 imagedestroy($a); imagedestroy($b);
}
// 不假设回调是纯函数，回调修改源像素后应立即读取新颜色。
$source=new Kkokk\Poster\Image\Imagick\Canvas(2,2,[255,0,0]); $calls=[];
$cut=$source->cutout(0,0,2,2,function ($point) use ($source,&$calls) {
 $calls[]=$point;
 if ($point===[0,1]) {
  $draw=new ImagickDraw(); $draw->setFillColor('blue'); $draw->point(0,1); $source->getImage()->drawImage($draw);
 }
 return true;
});
$decoded=imagecreatefromstring($cut->blob()); $color=imagecolorsforindex($decoded,imagecolorat($decoded,0,1));
checkCutout($calls===[[0,0],[0,1],[1,0],[1,1]] && $color['blue']===255 && $color['red']===0,'Callback timing changed');
imagedestroy($decoded);
echo "PASS: Imagick native cutout exact pixels, clipping and callback mutation timing\n";
