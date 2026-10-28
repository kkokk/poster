<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
$loader = new Kkokk\Poster\Image\Resources\RemoteImageLoader();
$im = imagecreatetruecolor(3, 2);
ob_start(); imagepng($im); $blob = ob_get_clean();
if ($loader->validate($blob) !== $blob) throw new RuntimeException('Blob changed');
foreach (['', 'invalid', str_repeat('x', 20 * 1024 * 1024 + 1)] as $bad) {
    try { $loader->validate($bad); throw new RuntimeException('Invalid image accepted'); }
    catch (Kkokk\Poster\Exception\PosterException $e) {}
}
try { $loader->load('file:///example.png'); throw new RuntimeException('Protocol accepted'); }
catch (Kkokk\Poster\Exception\PosterException $e) {}
echo "PASS: remote image validation and scheme policy\n";
$restricted = new Kkokk\Poster\Image\Resources\RemoteImageLoader(2, 1000, 3, ['example.test']);
try { $restricted->load('http://not-allowed.test/image.png'); throw new RuntimeException('Host accepted'); }
catch (Kkokk\Poster\Exception\PosterException $e) {}
try { $restricted->validate($blob); throw new RuntimeException('Pixel limit ignored'); }
catch (Kkokk\Poster\Exception\PosterException $e) {}
$small = new Kkokk\Poster\Image\Resources\RemoteImageLoader(2, 1, 100);
try { $small->validate($blob); throw new RuntimeException('Byte limit ignored'); }
catch (Kkokk\Poster\Exception\PosterException $e) {}
echo "PASS: custom byte/pixel limits and host policy\n";