<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
ini_set('session.use_cookies', '0');
session_cache_limiter('');
$base = new Kkokk\Poster\Cache\SessionCacheAdapter();
$a = new Kkokk\Poster\Cache\NamespacedCacheAdapter($base, 'a');
$b = new Kkokk\Poster\Cache\NamespacedCacheAdapter($base, 'b');
function namespaceCheck($condition) { if (!$condition) throw new RuntimeException('Namespace regression'); }
try {
    $base->put('host_key', 'host');
    $a->put('same', 0, 30);
    $b->put('same', 'other', 30);
    namespaceCheck($a->has('same') && $a->pull('same') === 0);
    namespaceCheck($a->pull('same', 'missing') === 'missing');
    namespaceCheck($b->get('same') === 'other');
    try { $a->flush(); throw new RuntimeException('Unsafe flush allowed'); }
    catch (Kkokk\Poster\Exception\PosterException $e) {}
    namespaceCheck($base->get('host_key') === 'host' && $b->get('same') === 'other');
    $b->forget('same');
    namespaceCheck(!$b->has('same'));
    $a->put('expired', 'value', -2);
    namespaceCheck($a->get('expired', 'missing') === 'missing');
    $repository = new Kkokk\Poster\Cache\CacheRepository($a);
    $repository->put('repo', 'value');
    namespaceCheck($repository->get('repo') === 'value');
} finally { session_abort(); }
echo "PASS: namespaces, consumption, TTL and safe flush refusal\n";