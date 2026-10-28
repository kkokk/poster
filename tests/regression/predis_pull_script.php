<?php
namespace Predis {
    // 测试桥仅核对适配器的命令参数和脚本执行，不替代真实 Predis 客户端集成。
    class Client {
        private $redis;
        public function __construct($redis) { $this->redis = $redis; }
        public function eval($script, $count, $key) {
            if ($count !== 1) throw new \RuntimeException('Wrong key count');
            return $this->redis->eval($script, [$key], 1);
        }
    }
}
namespace {
    require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
    $redis = new Redis();
    $redis->connect('127.0.0.1', (int) (getenv('POSTER_REDIS_PORT') ?: 6379), 1);
    $key = 'poster_predis_script_' . uniqid();
    $adapter = new Kkokk\Poster\Cache\PredisCacheAdapter(new Predis\Client($redis));
    try {
        foreach (['0', '', 'answer'] as $value) {
            $redis->setex($key, 30, $value);
            if ($adapter->pull($key, 'missing') !== $value) throw new RuntimeException('Changed value');
            if ($adapter->pull($key, 'missing') !== 'missing') throw new RuntimeException('Consumed twice');
        }
        echo "PASS: Predis argument bridge and real Redis Lua consumption (not real Predis integration)\n";
    } finally { $redis->del($key); }
}