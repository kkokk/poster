<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
error_reporting(E_ALL);
set_error_handler(function ($s,$m,$f,$l) { throw new ErrorException($m,0,$s,$f,$l); });
function verifyGradient($ok) { if (!$ok) throw new RuntimeException('Gradient mismatch'); }
use Kkokk\Poster\Image\Graphics\Gradient;
verifyGradient(Gradient::color([[0,0,0],[255,255,255]], .5) === [128,128,128]);
foreach ([[1,1],[1,7],[7,1],[13,19],[3,2]] as $size) {
 foreach (['bottom','top','left','right','left top','right bottom','top right','bottom left'] as $direction) {
  foreach ([[[17,31,47]],[[0,0,0],[255,255,255]],[[255,0,0],[255,125,0],[255,255,0],[0,255,0],[0,255,255],[0,0,255],[255,0,255]]] as $colors) {
   $gd = new Kkokk\Poster\Image\Gd\Canvas($size[0],$size[1]);
   $im = new Kkokk\Poster\Image\Imagick\Canvas($size[0],$size[1]);
   $gd->linearGradient($colors,$direction); $im->linearGradient($colors,$direction); $im->getImage()->setImageFormat("png");
   $a=imagecreatefromstring($gd->blob()); $b=imagecreatefromstring($im->blob());
   for ($y=0;$y<$size[1];$y++) for ($x=0;$x<$size[0];$x++) verifyGradient(imagecolorsforindex($a,imagecolorat($a,$x,$y)) === imagecolorsforindex($b,imagecolorat($b,$x,$y)));
   imagedestroy($a); imagedestroy($b);
  }
 }
}
foreach ([0,50,127] as $alpha) {
 $class='Kkokk\\Poster\\Image\\Imagick\\Canvas';
 $canvas=new $class(2,2,[255,0,0,$alpha]);
 $canvas->getImage()->setImageFormat("png"); $im=imagecreatefromstring($canvas->blob());
 verifyGradient(imagecolorsforindex($im,imagecolorat($im,0,0))['alpha'] === $alpha);
 imagedestroy($im);
}
echo "gradient parity passed\n";

foreach ([0,50,127] as $alpha) {
 $canvas=new Kkokk\Poster\Image\Gd\Canvas(3,3);
 $canvas->linearGradient([[255,0,0]],'bottom')->transparent($alpha);
 $decoded=imagecreatefromstring($canvas->blob());
 verifyGradient(imagecolorsforindex($decoded,imagecolorat($decoded,1,1)) === ['red'=>255,'green'=>0,'blue'=>0,'alpha'=>$alpha]);
 imagedestroy($decoded);
}
echo "GD transparency preserves RGB passed\n";
