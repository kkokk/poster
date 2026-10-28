<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
error_reporting(E_ALL);
set_error_handler(function ($s,$m,$f,$l) { throw new ErrorException($m,0,$s,$f,$l); });
foreach (['Gd','Imagick'] as $driver) {
 foreach ([0,50,127] as $alpha) {
  foreach ([10,[10,8,6,4]] as $radius) {
   $class='Kkokk\\Poster\\Image\\'.$driver.'\\Canvas';
   $canvas=new $class(60,80,[255,0,0]);
   $canvas->transparent($alpha)->borderRadius($radius);
   $im=imagecreatefromstring($canvas->blob());
   $center=imagecolorsforindex($im,imagecolorat($im,30,40));
   $corner=imagecolorsforindex($im,imagecolorat($im,0,0));
   if ($center['alpha']!==$alpha || $corner['alpha']!==127) throw new RuntimeException($driver.json_encode([$alpha,$center,$corner]));
   if ($alpha<127 && ($center['red']!==255 || $center['green']!==0 || $center['blue']!==0)) throw new RuntimeException('RGB changed');
   imagedestroy($im);
  }
 }
 echo $driver." rounded transparency passed\n";
}

// 全透明黑色会触发自动灰度PNG编码，覆盖所有公开编码出口。
$canvas=new Kkokk\Poster\Image\Imagick\Canvas(4,4,[0,0,0,127]);
$temporary=$canvas->tmp();
$path=tempnam(sys_get_temp_dir(),'poster_alpha_').'.png';
// tempnam创建的无后缀占位文件不作为输出保留。
unlink(substr($path,0,-4));
try {
 $canvas->getData($path);
 $base=$canvas->getBaseData();
 $blobs=[$canvas->blob(),$canvas->getStream(),file_get_contents($temporary),file_get_contents($path),base64_decode(substr($base,strpos($base,',')+1))];
 foreach ($blobs as $blob) {
  $im=imagecreatefromstring($blob);
  $pixel=imagecolorsforindex($im,imagecolorat($im,1,1));
  if ($pixel['alpha']!==127) throw new RuntimeException('PNG output lost full transparency');
  imagedestroy($im);
 }
} finally {
 unlink($temporary); if (is_file($path)) unlink($path);
}
echo "Imagick transparent PNG export paths passed\n";

