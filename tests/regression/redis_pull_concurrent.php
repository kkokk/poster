<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
function redisTestConnection() {
    $r = new Redis();
    $r->connect(getenv('POSTER_REDIS_HOST') ?: '127.0.0.1', (int) (getenv('POSTER_REDIS_PORT') ?: 6379), 1);
    return $r;
}
if (isset($argv[1]) && $argv[1] === 'worker') {
    $r = redisTestConnection();
    while (microtime(true) < (float) $argv[4]) usleep(1000);
    $adapter = new Kkokk\Poster\Cache\RedisCacheAdapter($r);
    file_put_contents($argv[3], $adapter->pull($argv[2], 'missing'));
    exit;
}
$r = redisTestConnection();
$key = 'poster_concurrent_' . uniqid();
$children = [];
$files = [];
try {
    $r->setex($key, 30, 'winner');
    $start = microtime(true) + 1;
    for ($i=0; $i<8; $i++) {
        $file = tempnam(sys_get_temp_dir(), 'poster_consume_');
        $files[] = $file;
        $command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__FILE__) . ' worker ' . escapeshellarg($key) . ' ' . escapeshellarg($file) . ' ' . $start;
        $pipes = [];
        $child = proc_open($command, [0=>['pipe','r'],1=>['file',$file.'.log','a'],2=>['file',$file.'.log','a']], $pipes, null, null, ['bypass_shell'=>true]);
        if (!is_resource($child)) throw new RuntimeException('Cannot start worker');
        fclose($pipes[0]);
        $children[] = $child;
    }
    foreach ($children as $child) if (proc_close($child) !== 0) throw new RuntimeException('Worker failed');
    $children = [];
    $values = array_map('file_get_contents', $files);
    if (count(array_keys($values, 'winner', true)) !== 1 || count(array_keys($values, 'missing', true)) !== 7) throw new RuntimeException('Consumption is not exclusive');
    echo "PASS: eight Redis consumers, exactly one winner\n";
} finally {
    foreach ($children as $child) if (is_resource($child)) { proc_terminate($child); proc_close($child); }
    foreach ($files as $file) { @unlink($file); @unlink($file.'.log'); }
    $r->del($key);
}