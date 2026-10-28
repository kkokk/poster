<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
$redis = new Redis();
$redis->connect(getenv('POSTER_REDIS_HOST') ?: '127.0.0.1', (int) (getenv('POSTER_REDIS_PORT') ?: 6379), 1);
$key = 'poster_test_pull_' . uniqid('', true);
$adapter = new Kkokk\Poster\Cache\RedisCacheAdapter($redis);
try {
    foreach ([Redis::SERIALIZER_NONE, Redis::SERIALIZER_PHP] as $serializer) {
        $redis->setOption(Redis::OPT_SERIALIZER, $serializer);
        foreach (['0', '', 'answer'] as $value) {
            if (!$adapter->put($key, $value, 30)) throw new RuntimeException('Write failed');
            if ($adapter->pull($key, 'missing') !== $value) throw new RuntimeException('Value changed');
            if ($adapter->pull($key, 'missing') !== 'missing') throw new RuntimeException('Consumed twice');
        }
    }
    $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
    foreach ([0, ['answer' => 0]] as $value) {
        $adapter->put($key, $value, 30);
        if ($adapter->pull($key) !== $value) throw new RuntimeException('Serialized type changed');
    }
    $adapter->put($key, 'expired', 30);
    $redis->expire($key, 0);
    if ($adapter->pull($key, 'missing') !== 'missing') throw new RuntimeException('Expired value accepted');
    $redis->pipeline();
    try {
        $adapter->pull($key);
        throw new RuntimeException('Pipeline accepted');
    } catch (Kkokk\Poster\Exception\PosterException $e) {}
    $redis->discard();
    $redis->setOption(Redis::OPT_PREFIX, 'poster_prefix_');
    $adapter->put($key, 'prefixed', 30);
    if ($adapter->pull($key) !== 'prefixed') throw new RuntimeException('Prefix changed');
    $redis->setOption(Redis::OPT_PREFIX, '');
    $redis->multi();
    try {
        $adapter->pull($key);
        throw new RuntimeException('Nested transaction accepted');
    } catch (Kkokk\Poster\Exception\PosterException $e) {}
    $redis->discard();
    echo "PASS: Redis consumption, serializers and transaction guard\n";
} finally {
    $redis->del($key);
    $redis->close();
}