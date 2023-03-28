<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/10
 * Time: 11:08
 */

namespace Kkokk\Poster\Facades;
use Kkokk\Poster\Interfaces\CaptchaInterface;
use Kkokk\Poster\Lang\CaptchaInterface as CaptchaManager;
/**
 * @method static CaptchaManager type(string $channel = null)
 * @method static CaptchaInterface config($params = [])
 * @method static CaptchaInterface check($key , $value , $leeway = 0, $secret = null)
 * @method static CaptchaInterface get($expire = 0)
 *
 * @see CaptchaManager
 */
class Captcha extends Facade
{
    protected static function getFacadeModel(){
        return 'captcha';
    }
}