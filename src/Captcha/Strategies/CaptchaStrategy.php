<?php
/**
 * User: lang
 * Date: 2025/4/1
 * Time: 14:35
 */

namespace Kkokk\Poster\Captcha\Strategies;

use Kkokk\Poster\Cache\CacheRepository;
use Kkokk\Poster\Exception\PosterException;
use Kkokk\Poster\Image\Drivers\GdDriver;
use Kkokk\Poster\Image\Drivers\ImagickDriver;

class CaptchaStrategy
{
    protected $configs = [];

    /**
     * @var GdDriver|ImagickDriver
     */
    protected $driver;

    /**
     * @var CacheRepository
     */
    protected $cache;

    protected $expire = 180; // 过期时间

    protected $leeway = 5;   // 误差值

    public function __construct($driver, CacheRepository $cacheRepository)
    {
        if (!$driver instanceof GdDriver && !$driver instanceof ImagickDriver) {
            throw new PosterException('driver must be GdDriver or ImagickDriver');
        }
        $this->driver = $driver;
        $this->cache = $cacheRepository;
        $this->configs = array_merge(['debug' => false], $this->configs);
    }

    public function config($configs = [])
    {
        $this->configs = array_merge($this->configs, $configs);
        return $this;
    }

    public function create($filename = 'captcha')
    {
        if ($this->configs['debug']) {
            $outputPath = POSTER_BASE_PATH . '/../tests/poster/' . $filename . '.' . $this->configs['im_type'];
            gd_image_save(
                $this->driver->getImage(),
                $this->configs['im_type'],
                $outputPath,
                $this->configs['quality']
            );
        }
        $baseData = $this->driver->getBaseData();
        $key = uniqid($filename . '-' . $this->configs['type']) . mt_rand(0, 9999);
        return [$key, $baseData];
    }

    public function put($key, $value, $expire = 0)
    {
        try {
            $this->cache->put($key, $value, $expire ?: $this->expire);
        } catch (\Exception $e) {
            // 报错，则返回密码，自行保存
            return false;
        }
        return true;
    }
}