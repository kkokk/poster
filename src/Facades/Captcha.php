<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/10
 * Time: 11:08
 */

namespace Kkokk\Poster\Facades;

use Kkokk\Poster\Cache\AbstractAdapter;
use Kkokk\Poster\Captcha\Generators\CaptchaGenerator;
use Kkokk\Poster\Captcha\CaptchaGeneratorInterface;
use Kkokk\Poster\Captcha\CaptchaManager;

/**
 * @method static CaptchaManager driver(string $driver = null)
 * @method static CaptchaManager setCache(AbstractAdapter $cacheAdapter)
 * @method static CaptchaGenerator type(string $channel = null)
 * @method static CaptchaGeneratorInterface config($params = [])
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