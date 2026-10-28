<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

use Kkokk\Poster\Facades\Poster;
use Kkokk\Poster\Image\PosterManager;

error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});
function verifyIsolation($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
}
class IsolationFacadeProbe extends Kkokk\Poster\Facades\Facade {
    protected static function getFacadeModel() { return 'poster'; }
    public static function resolve($model) { return parent::setInstance($model); }
}
$cached = IsolationFacadeProbe::resolve('poster');
$other = IsolationFacadeProbe::resolve('cache');
$isolated = Poster::fresh();
verifyIsolation($isolated !== $cached, 'Fresh manager reused cached instance.');
verifyIsolation(IsolationFacadeProbe::resolve('poster') === $cached, 'Fresh replaced cached manager.');
Poster::reset();
verifyIsolation(IsolationFacadeProbe::resolve('poster') !== $cached, 'Reset retained cached manager.');
verifyIsolation(IsolationFacadeProbe::resolve('cache') === $other, 'Reset changed another facade.');

foreach (['gd', 'imagick'] as $name) {
    $manager = new PosterManager();
    $shared = $manager->extension($name);
    $first = $shared->config(['quality' => 21])->buildIm(12, 9)->getCanvas();
    $oldBuilder = $shared->buildIm(7, 6);
    // 默认复用行为继续兼容；独立任务必须显式使用 fresh。
    verifyIsolation($shared->buildIm(8, 5)->getCanvas()->getQuantity() === 21, 'Legacy config persistence changed.');
    $fresh = $manager->fresh($name);
    $second = $fresh->buildIm(10, 11)->getCanvas();
    verifyIsolation($second->getQuantity() === 75, 'Fresh inherited quality.');
    verifyIsolation($second->getWidth() === 10 && $second->getHeight() === 11, 'Wrong fresh dimensions.');
    verifyIsolation($first->getWidth() === 12 && $first->getHeight() === 9, 'Earlier canvas mutated.');
    verifyIsolation($manager->extension($name) === $shared, 'Fresh replaced cached extension.');
    verifyIsolation($manager->reset($name) === $manager, 'Reset chaining broken.');
    verifyIsolation($manager->extension($name) !== $shared, 'Reset retained extension.');
    verifyIsolation($manager->extension($name)->buildIm(8, 5)->getCanvas()->getQuantity() === 75, 'Reset retained quality.');
    verifyIsolation($oldBuilder->getCanvas()->getQuantity() === 21, 'Reset invalidated existing builder.');
    $before = $manager->extension($name);
    $manager->reset();
    verifyIsolation($manager->extension($name) !== $before, 'Reset all retained extension.');
    echo 'PASS: ' . $name . " task isolation\n";
}
restore_error_handler();
echo "PASS: facade isolation and reset\n";