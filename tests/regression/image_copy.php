<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
foreach (['Kkokk\\Poster\\Image\\Gd\\Canvas', 'Kkokk\\Poster\\Image\\Imagick\\Canvas'] as $class) {
    $source = new $class(8, 6, [255, 0, 0, 127]);
    $before = $source->blob();
    $copy = $source->copy();
    if ($copy === $source || $copy->getImage() === $source->getImage()) throw new RuntimeException('Shared resource');
    if ($copy->getWidth() !== 8 || $copy->getHeight() !== 6) throw new RuntimeException('Wrong dimensions');
    $copy->background([0, 255, 0]);
    if ($source->blob() !== $before) throw new RuntimeException('Source mutated');
    unset($copy);
    if ($source->blob() !== $before) throw new RuntimeException('Source destroyed');
    echo 'PASS: ' . $class . " copy isolation and lifetime\n";
}