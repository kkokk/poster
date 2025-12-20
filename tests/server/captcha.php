<?php
/**
 * User: lang
 * Date: 2023/8/25
 * Time: 10:15
 */
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require '../../vendor/autoload.php';

use Kkokk\Poster\Facades\Captcha;

try {

    $res = Captcha::extension('gd')->type('slider')
        ->get();
} catch (\Exception $e) {
    echo($e->getMessage());
}

// var_dump($res);
exit;

$redis = new \Redis();
$redis->connect('127.0.0.1');
$redis->select(0);

$redisAdapter = new \Kkokk\Poster\Cache\RedisCacheAdapter($redis);

$key = isset($_GET['key']) ? $_GET['key'] : '';
$code = isset($_GET['code']) ? $_GET['code'] : '';
if ($key && $code) {
    $result = Captcha::extension('imagick')
        ->setCache($redisAdapter)
        ->type('input')
        ->check($key, $code);
    echo $result ? "验证成功" : "验证失败";
    exit;
}

$res = Captcha::extension('imagick')
    ->setCache($redisAdapter)
    ->type('input')
    ->config(['type' => 'math', 'font_size' => 40])
    ->get();
$secret = '';
if (!empty($res['secret'])) {
    $secret = $res['secret'];
}
echo $res['key'];
echo $secret;
echo "<img src='{$res['img']}' />";