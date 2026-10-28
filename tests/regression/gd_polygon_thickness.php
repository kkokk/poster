<?php

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

use Kkokk\Poster\Image\Gd\Canvas;

error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

$points = [5, 5, 25, 5, 25, 25, 5, 25];
$cases = [0.5, 1.5, 2.9, 1, 3, 2.0, '0.5', '2'];

foreach ($cases as $thickness) {
    $canvas = new Canvas(32, 32);
    $reference = new Canvas(32, 32);
    $result = $canvas->drawImagePolygon($points, [0, 0, 0], $thickness);
    if ($result !== $canvas) {
        throw new RuntimeException('Polygon drawing must remain chainable.');
    }

    imagesetthickness($reference->getImage(), (int) $thickness);
    $black = imagecolorallocate($reference->getImage(), 0, 0, 0);
    if (version_compare(PHP_VERSION, '8.1.0', '<')) {
        imagepolygon($reference->getImage(), $points, 4, $black);
    } else {
        imagepolygon($reference->getImage(), $points, $black);
    }

    for ($x = 0; $x < 32; $x++) {
        for ($y = 0; $y < 32; $y++) {
            if (imagecolorat($canvas->getImage(), $x, $y) !== imagecolorat($reference->getImage(), $x, $y)) {
                throw new RuntimeException('Unexpected pixel output for thickness ' . var_export($thickness, true));
            }
        }
    }
    unset($canvas, $reference, $result);
}

$default = new Canvas(32, 32);
$explicit = new Canvas(32, 32);
$default->drawImagePolygon($points, [0, 0, 0]);
$explicit->drawImagePolygon($points, [0, 0, 0], 1);
if ($default->blob() !== $explicit->blob()) {
    throw new RuntimeException('Default thickness must remain 1.');
}
unset($default, $explicit);

restore_error_handler();
echo "PASS: GD polygon thickness regression (8 cases, default, chaining, pixel comparison).\n";
