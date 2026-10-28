<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
use Kkokk\Poster\Html\Drivers\WkHtmlDriver;
use Kkokk\Poster\Exception\PosterException;
function htmlParameterCheck($ok) { if (!$ok) throw new RuntimeException('HTML parameter regression'); }
function htmlProperty($driver, $name) {
    $property = new ReflectionProperty($driver, $name);
    $property->setAccessible(true);
    return $property->getValue($driver);
}
$d = new WkHtmlDriver();
$d->size('120', 60);
$d->size(80, 40);
htmlParameterCheck(htmlProperty($d, 'size') === '--width 80 --height 40');
$d->crop(30, 20, 4, 5);
$d->crop(10, 0, 0, 0);
htmlParameterCheck(htmlProperty($d, 'crop') === ' --crop-w 10');
$d->setType('pdf');
$d->setType('png');
htmlParameterCheck(htmlProperty($d, 'driver') === 'wkhtmltoimage');
foreach (['1 & echo invalid', -1, 0.5, INF, NAN, [], true] as $value) {
    try { $d->size($value); throw new RuntimeException('Invalid dimension accepted'); }
    catch (PosterException $e) {}
}
$d->quality(150);
htmlParameterCheck(htmlProperty($d, 'quality') === '--quality 100');
echo "PASS: HTML dimensions, replacement and driver selection\n";