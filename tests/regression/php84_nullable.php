<?php

error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

// 加载所有受影响类；PHP 8.4 的声明弃用在加载时发生。
$cases = [
    ['Kkokk\\Poster\\Image\\Drivers\\Driver', 'execute', [[], new stdClass()]],
    ['Kkokk\\Poster\\Image\\Gd\\Image', '__construct', ['', new stdClass()]],
    ['Kkokk\\Poster\\Image\\Imagick\\Image', '__construct', ['', new stdClass()]],
    ['Kkokk\\Poster\\Image\\Graphics\\GdImageGraphicsEngine', 'cutout', [0, 0, 1, 1, 'strlen']],
    ['Kkokk\\Poster\\Image\\Graphics\\ImagickImageGraphicsEngine', 'cutout', [0, 0, 1, 1, 'strlen']],
    ['Kkokk\\Poster\\Image\\Gd\\Text', 'measure', [null, new stdClass()]],
    ['Kkokk\\Poster\\Image\\Gd\\Text', 'layout', [null, new stdClass()]],
    ['Kkokk\\Poster\\Image\\Gd\\Text', 'fitFontSize', [100, 100, 8, 72, new stdClass()]],
    ['Kkokk\\Poster\\Image\\Builder', 'buildBg', [10, 10, [], false, 0, 0, 0, 0, 'strlen']],
    ['Kkokk\\Poster\\Image\\Extension', 'buildBg', [10, 10, [], false, 0, 0, 0, 0, 'strlen']],
];
foreach ($cases as $case) {
    $class = new ReflectionClass($case[0]);
    $object = $class->newInstanceWithoutConstructor();
    $rejected = false;
    try {
        call_user_func_array([$object, $case[1]], $case[2]);
    } catch (TypeError $e) {
        $rejected = true;
    } catch (InvalidArgumentException $e) {
        $rejected = true;
    }
    if (!$rejected) throw new RuntimeException('Invalid argument accepted: ' . $case[0] . '::' . $case[1]);
}

$driver = new Kkokk\Poster\Image\Drivers\Driver();
if ($driver->execute() !== $driver || $driver->execute([], null) !== $driver || $driver->execute([], $driver) !== $driver) {
    throw new RuntimeException('Driver chaining changed');
}
$previous = new Exception('previous');
$exception = new Kkokk\Poster\Exception\Exception('message', 1, $previous);
if ($exception->getPrevious() !== $previous) throw new RuntimeException('Exception chain changed');
$rejected = false;
try {
    new Kkokk\Poster\Exception\Exception('', 0, new stdClass());
} catch (TypeError $e) {
    $rejected = true;
} catch (InvalidArgumentException $e) {
    $rejected = true;
}
if (!$rejected) throw new RuntimeException('Invalid previous exception accepted');

// 合法实例、子类和 null 均接受；不用 Imagick 编码模块也可验证类型边界。
Kkokk\Poster\Support\ParameterType::nullable(null, 'Closure', '$callback');
Kkokk\Poster\Support\ParameterType::nullable(function () {}, 'Closure', '$callback');
$canvas = new Kkokk\Poster\Image\Gd\Canvas(20, 20);
$image = $canvas->cutout(0, 0, 10, 10, function ($point) { return true; });
if ($image->getWidth() !== 10 || $image->getHeight() !== 10) throw new RuntimeException('Cutout dimensions changed');
$text = new Kkokk\Poster\Image\Gd\Text();
$text->setText('test');
$text->measure();
$text->measure(null, $canvas);
$text->layout(null, $canvas);
$text->fitFontSize(100, 100, 8, 12, $canvas);

$extension = (new Kkokk\Poster\Image\ExtensionFactory())->make('gd');
$query = new Kkokk\Poster\Image\Queries\Query();
$builder = new Kkokk\Poster\Image\Builder($extension, $query);
if ($builder->buildBg(10, 10) !== $builder) throw new RuntimeException('Background chaining changed');
$builder->buildBg(10, 10, [], false, 0, 0, 0, 0, null);
$commands = $query->getQuery();
if ($commands[1] !== $commands[2]) throw new RuntimeException('Default/null background differs');
$calls = 0;
$built = $extension->buildBg(10, 10, [], false, 0, 0, 0, 0, function ($nested) use (&$calls) {
    $calls++;
    $nested->buildIm(4, 4);
});
$commands = $built->query->getQuery();
$background = end($commands);
if ($calls !== 1 || count($background['params'][8]) !== 1 || $background['params'][8][0]['type'] !== 'im') {
    throw new RuntimeException('Background callback query isolation changed');
}
restore_error_handler();
echo "PASS: nullable parameter loading, rejection and valid calls\n";
