<?php

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

use Kkokk\Poster\Cache\CacheRepository;
use Kkokk\Poster\Cache\SessionCacheAdapter;
use Kkokk\Poster\Captcha\Strategies\Input\InputCaptcha;
use Kkokk\Poster\Image\Drivers\GdDriver;
use Kkokk\Poster\Image\Gd\Canvas;

error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// 使用内存中的 Session 数据，避免改动开发者已有会话文件。
ini_set('session.use_cookies', '0');
session_cache_limiter('');
session_start();
$cache = new SessionCacheAdapter();

class CoreDefectsFacadeProbe extends Kkokk\Poster\Facades\Facade
{
    public static function resolve($model)
    {
        return parent::setInstance($model);
    }
}

function verifyCoreDefect($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$tests = [];
$tests['GD background'] = function () {
    foreach ([[255, 0, 0], '#ff0000'] as $color) {
        $canvas = new Canvas(8, 8);
        verifyCoreDefect($canvas->background($color) === $canvas, 'Background chaining changed.');
        $pixel = imagecolorsforindex($canvas->getImage(), imagecolorat($canvas->getImage(), 4, 4));
        verifyCoreDefect($pixel['red'] === 255 && $pixel['green'] === 0 && $pixel['blue'] === 0, 'Wrong background color.');
    }
    imagealphablending($canvas->getImage(), false);
    $canvas->background();
    $pixel = imagecolorsforindex($canvas->getImage(), imagecolorat($canvas->getImage(), 0, 0));
    verifyCoreDefect($pixel['alpha'] === 127, 'Default transparent color changed.');
};
$tests['Captcha zero and lifecycle'] = function () use ($cache) {
    $captcha = new InputCaptcha(new GdDriver(), new CacheRepository($cache));
    foreach ([0, '0', '1234', 'AbCd'] as $answer) {
        $cache->put('captcha_probe', $answer, 60);
        verifyCoreDefect($captcha->check('captcha_probe', (string) $answer), 'Correct answer rejected: ' . var_export($answer, true));
        verifyCoreDefect(!$captcha->check('captcha_probe', (string) $answer), 'Consumed answer accepted twice.');
    }
    $cache->put('captcha_probe', 0, 60);
    verifyCoreDefect(!$captcha->check('captcha_probe', '1'), 'Wrong zero answer accepted.');
    foreach ([false, true, null, '', 'wrong', [], new stdClass()] as $invalid) {
        $cache->put('captcha_probe', 0, 60);
        verifyCoreDefect(!$captcha->check('captcha_probe', $invalid), 'Invalid zero answer accepted.');
    }
    $cache->put('captcha_probe', '1234', -2);
    verifyCoreDefect(!$captcha->check('captcha_probe', '1234'), 'Expired answer accepted.');
    verifyCoreDefect(!$captcha->check('missing_probe', '0'), 'Missing answer accepted.');
    verifyCoreDefect($captcha->check('missing_probe', '0', 0, 0), 'Server-side zero secret rejected.');
    $cache->put('captcha_probe', 0, 60);
    verifyCoreDefect($captcha->check('captcha_probe', '0', 0, 'other'), 'Cached zero must take priority over fallback.');
};
$tests['Facade reuse and isolation'] = function () {
    $first = CoreDefectsFacadeProbe::resolve('poster');
    $other = CoreDefectsFacadeProbe::resolve('cache');
    verifyCoreDefect($first === CoreDefectsFacadeProbe::resolve('poster'), 'Poster instance was recreated.');
    verifyCoreDefect($other === CoreDefectsFacadeProbe::resolve('cache'), 'Cache instance was recreated.');
    verifyCoreDefect($first !== $other, 'Facade models share an instance.');
    $marker = new stdClass();
    verifyCoreDefect(CoreDefectsFacadeProbe::resolve($marker) === $marker, 'Object passthrough changed.');
    $adapter = new SessionCacheAdapter();
    Kkokk\Poster\Facades\Cache::setAdapter($adapter);
    Kkokk\Poster\Facades\Cache::put('facade_probe', 'saved', 60);
    verifyCoreDefect(Kkokk\Poster\Facades\Cache::get('facade_probe') === 'saved', 'Facade configuration did not persist.');
};
$tests['Session TTL overwrite'] = function () use ($cache) {
    $cache->put('ttl_probe', 'old', -2);
    $cache->put('ttl_probe', 'new');
    verifyCoreDefect($cache->get('ttl_probe') === 'new', 'Old TTL expired new value.');
    verifyCoreDefect(!isset($_SESSION['ttl_probe_expires']), 'Old TTL metadata remains.');
    $cache->put('ttl_probe', 0, 60);
    verifyCoreDefect($cache->get('ttl_probe') === 0, 'Zero cache value lost.');
    $cache->put('ttl_probe', 'expired', -2);
    verifyCoreDefect($cache->get('ttl_probe', 'missing') === 'missing', 'New TTL not applied.');
    verifyCoreDefect(!isset($_SESSION['ttl_probe_expires']), 'Expired metadata not removed.');
};

$failures = 0;
foreach ($tests as $name => $test) {
    try {
        $test();
        echo 'PASS: ' . $name . "\n";
    } catch (Exception $e) {
        $failures++;
        echo 'FAIL: ' . $name . ': ' . $e->getMessage() . "\n";
    } catch (Throwable $e) {
        $failures++;
        echo 'FAIL: ' . $name . ': ' . $e->getMessage() . "\n";
    }
}
session_abort();
restore_error_handler();
exit($failures ? 1 : 0);
