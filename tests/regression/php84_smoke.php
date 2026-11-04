<?php

// E_ALL 下验证常用入口，避免只做语法检查遗漏运行时弃用。
error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
ini_set('session.use_cookies', '0');
session_cache_limiter('');
session_start();

function php84Check($condition, $message)
{
    if (!$condition) throw new RuntimeException($message);
}

$root = realpath(dirname(dirname(__DIR__)) . '/src');
$loaded = 0;
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)) as $file) {
    $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
    if (substr($relative, -4) !== '.php' || strpos($relative, 'PHPQrcode/') === 0 || $relative === 'helpers.php') continue;
    $name = 'Kkokk\\Poster\\' . str_replace('/', '\\', substr($relative, 0, -4));
    php84Check(class_exists($name) || interface_exists($name) || trait_exists($name), 'Cannot autoload ' . $name);
    $loaded++;
}
echo 'PASS: autoload ' . $loaded . " project classes/interfaces/traits\n";

foreach (['ASCII 123', '中文二维码'] as $content) {
    $qr = new Kkokk\Poster\Image\Gd\Qr($content, 'M', 3, 2);
    $bytes = $qr->blob();
    $info = getimagesizefromstring($bytes);
    php84Check($info[2] === IMAGETYPE_PNG && $info[0] === $info[1] && $info[0] > 0, 'Invalid QR PNG');
    $image = imagecreatefromstring($bytes);
    php84Check(imagecolorat($image, 0, 0) !== imagecolorat($image, 6, 6), 'QR finder pattern missing');
}
echo "PASS: ASCII/Chinese QR PNG and finder pixels\n";

// 测试不依赖编码模块的情况下也必须显式报告缺失的可选驱动。
$drivers = ['Gd'];
if (extension_loaded('imagick')) $drivers[] = 'Imagick';
else echo "SKIP: Imagick rendering (extension unavailable)\n";
foreach ($drivers as $driver) {
    $prefix = 'Kkokk\\Poster\\Image\\' . $driver . '\\';
    $canvasClass = $prefix . 'Canvas';
    $imageClass = $prefix . 'Image';
    $canvas = new $canvasClass(24, 18, [255, 0, 0, 60]);
    $path = tempnam(sys_get_temp_dir(), 'poster84_');
    try {
        $bytes = $canvas->blob();
        file_put_contents($path, $bytes);
        $loadedImage = new $imageClass($path, new Kkokk\Poster\Image\Resources\RemoteImageLoader());
        php84Check($loadedImage->getWidth() === 24 && $loadedImage->getHeight() === 18, $driver . ' image size changed');
        $decoded = imagecreatefromstring($bytes);
        $pixel = imagecolorsforindex($decoded, imagecolorat($decoded, 12, 9));
        php84Check($pixel['red'] === 255 && abs($pixel['alpha'] - 60) <= 1, $driver . ' PNG alpha changed');
        $cut = $canvas->cutout(0, 0, 8, 8, function ($point) { return $point[0] < 4; });
        $decoded = imagecreatefromstring($cut->blob());
        $selected = imagecolorsforindex($decoded, imagecolorat($decoded, 1, 1));
        $excluded = imagecolorsforindex($decoded, imagecolorat($decoded, 6, 1));
        php84Check(abs($selected['alpha'] - 60) <= 1 && $excluded['alpha'] === 127, $driver . ' cutout alpha changed');
    } finally {
        unlink($path);
    }
    echo 'PASS: ' . $driver . " loader, PNG dimensions, source/cutout alpha\n";
}

// 使用真实缓存适配器，固定答案，避免随机素材或 sleep 影响结果。
$adapter = new Kkokk\Poster\Cache\SessionCacheAdapter();
$repository = new Kkokk\Poster\Cache\CacheRepository($adapter);
$cases = [
    ['Input\\InputCaptcha', '1234', '1234', 'wrong'],
    ['Slider\\SliderCaptcha', 100, 100, 200],
    ['Rotate\\RotateCaptcha', 90, 270, 90],
    ['Click\\ClickCaptcha', [['point' => [0, 0, 10, 0, 10, 10, 0, 10]]], [['x' => 5, 'y' => 5]], [['x' => 20, 'y' => 20]]],
];
foreach ($cases as $case) {
    $class = 'Kkokk\\Poster\\Captcha\\Strategies\\' . $case[0];
    $captcha = new $class(new Kkokk\Poster\Image\Drivers\GdDriver(), $repository);
    $adapter->put('php84_probe', $case[1], 60);
    php84Check($captcha->check('php84_probe', $case[2]), $class . ' correct answer rejected');
    php84Check(!$captcha->check('php84_probe', $case[2]), $class . ' answer reused');
    $adapter->put('php84_probe', $case[1], 60);
    php84Check(!$captcha->check('php84_probe', $case[3]), $class . ' wrong answer accepted');
    $adapter->put('php84_probe', $case[1], -2);
    php84Check(!$captcha->check('php84_probe', $case[2]), $class . ' expired answer accepted');
}
$input = new Kkokk\Poster\Captcha\Strategies\Input\InputCaptcha(new Kkokk\Poster\Image\Drivers\GdDriver(), $repository);
$generated = $input->get(60);
$answer = $adapter->get($generated['key']);
php84Check(is_string($generated['img']) && strlen($generated['img']) > 0, 'Input captcha image missing');
php84Check($input->check($generated['key'], $answer), 'Generated input captcha answer rejected');
session_abort();
restore_error_handler();
echo "PASS: four captcha strategies correct/wrong/consumed/expired and input generation\n";
