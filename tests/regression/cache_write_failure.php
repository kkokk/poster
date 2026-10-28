<?php
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
class FailedWriteAdapter extends Kkokk\Poster\Cache\SessionCacheAdapter {
    public function put($key, $value, $ttl = null) { return false; }
}
class FailedWriteProbe extends Kkokk\Poster\Captcha\Strategies\Input\InputCaptcha {
    public function store() { return $this->put('probe', 'answer', 60); }
}
$p = new FailedWriteProbe(new Kkokk\Poster\Image\Drivers\GdDriver(), new Kkokk\Poster\Cache\CacheRepository(new FailedWriteAdapter()));
if ($p->store() !== false) throw new RuntimeException('Failed cache write reported success');
echo "PASS: captcha propagates explicit cache write failure\n";