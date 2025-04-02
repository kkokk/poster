<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/10
 * Time: 11:08
 */

namespace Kkokk\Poster\Facades;

use Kkokk\Poster\Cache\AbstractCacheAdapter;
use Kkokk\Poster\Captcha\CaptchaGeneratorInterface;
use Kkokk\Poster\Captcha\CaptchaManager;
use Kkokk\Poster\Captcha\Strategies\CaptchaStrategy;
use Kkokk\Poster\Captcha\Strategies\CaptchaStrategyInterface;

/**
 * @method static CaptchaManager extension(string $driver = null)
 * @method static CaptchaManager setCache(AbstractCacheAdapter $cacheAdapter)
 * @method static CaptchaStrategy|CaptchaStrategyInterface type(string $channel = null)
 * @method static CaptchaStrategyInterface config($params = [])
 * @method static CaptchaGeneratorInterface check($key, $value, $leeway = 0, $secret = null)
 * @method static CaptchaGeneratorInterface get($expire = 0)
 * @extends Facade<\Kkokk\Poster\Lang\Captcha>
 * @see CaptchaManager
 */
class Captcha extends Facade
{
    protected static function getFacadeModel()
    {
        return 'captcha';
    }
}