<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
$dir = sys_get_temp_dir() . '/poster_http_' . uniqid();
mkdir($dir);
$router = $dir . '/router.php';
file_put_contents($router, '<?php file_put_contents(__DIR__."/requests", "1", FILE_APPEND); if ($_SERVER["REQUEST_URI"] === "/redirect") { header("Location: /image.png"); exit; } if ($_SERVER["REQUEST_URI"] === "/slow") { header("Content-Type: image/png"); echo "x"; flush(); usleep(400000); exit; } header("Content-Type: image/png"); $im=imagecreatetruecolor(7,5); imagepng($im);');
$socket = stream_socket_server('tcp://127.0.0.1:0', $errno, $error);
if (!$socket) throw new RuntimeException($error);
$address = stream_socket_get_name($socket, false);
fclose($socket);
$pipes = [];
$process = proc_open(escapeshellarg(PHP_BINARY) . ' -S ' . $address . ' ' . escapeshellarg($router), [0=>['pipe','r'],1=>['file',$dir.'/log','a'],2=>['file',$dir.'/log','a']], $pipes, null, null, ['bypass_shell'=>true]);
if (!is_resource($process)) throw new RuntimeException('Cannot start local server');
fclose($pipes[0]);
try {
    $ready = false;
    for ($i=0;$i<100;$i++) {
        $connection = @stream_socket_client('tcp://' . $address, $errno, $error, 0.05);
        if ($connection) { fclose($connection); $ready=true; break; }
        usleep(20000);
    }
    if (!$ready) throw new RuntimeException('Server not ready');
    foreach (['Kkokk\\Poster\\Image\\Gd\\Image','Kkokk\\Poster\\Image\\Imagick\\Image'] as $class) {
        file_put_contents($dir . '/requests', '');
        $image = new $class('http://' . $address . '/image.png');
        if ($image->getWidth() !== 7 || $image->getHeight() !== 5) throw new RuntimeException('Wrong image dimensions');
        if (file_get_contents($dir . '/requests') !== '1') throw new RuntimeException('More than one download');
        $policy = new Kkokk\Poster\Image\Resources\RemoteImageLoader(2, 10000, 100, ['127.0.0.1']);
        $image = new $class('http://' . $address . '/image.png', $policy);
        if ($image->getWidth() !== 7) throw new RuntimeException('Injected policy failed');
        $default = new $class('http://' . $address . '/redirect');
        if ($default->getWidth() !== 7) throw new RuntimeException('Default redirect failed');
        try {
            new $class('http://' . $address . '/redirect', $policy);
            throw new RuntimeException('Restricted redirect accepted');
        } catch (Kkokk\Poster\Exception\PosterException $e) {}
        $limited = new Kkokk\Poster\Image\Resources\RemoteImageLoader(2, 10000, 1);
        try {
            new $class('http://' . $address . '/image.png', $limited);
            throw new RuntimeException('Injected pixel policy ignored');
        } catch (Kkokk\Poster\Exception\PosterException $e) {}
        $tiny = new Kkokk\Poster\Image\Resources\RemoteImageLoader(2, 1, 100);
        try {
            new $class('http://' . $address . '/image.png', $tiny);
            throw new RuntimeException('HTTP byte limit ignored');
        } catch (Kkokk\Poster\Exception\PosterException $e) {
            if (strpos($e->getMessage(), 'byte limit') === false) throw $e;
        }
        $fast = new Kkokk\Poster\Image\Resources\RemoteImageLoader(0.1, 10000, 100);
        $started = microtime(true);
        try {
            new $class('http://' . $address . '/slow', $fast);
            throw new RuntimeException('Slow response accepted');
        } catch (Kkokk\Poster\Exception\PosterException $e) {
            if (microtime(true) - $started > 2) throw new RuntimeException('Timeout exceeded test budget');
        }
        usleep(450000); // 单线程测试服务器完成慢请求后再开始下一轮。
        echo "PASS: HTTP byte limit and slow response rejection\n";
        echo "PASS: constructor policy, redirects and pixel rejection\n";
        echo 'PASS: ' . $class . " HTTP image, single request\n";
    }
} finally {
    proc_terminate($process);
    proc_close($process);
    foreach (glob($dir . '/*') as $file) unlink($file);
    rmdir($dir);
}