<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2023/3/10
 * Time: 11:08
 */

namespace Kkokk\Poster\Facades;

/**
 * @method static \Kkokk\Poster\Interfaces\MyCaptcha type(string $channel = null)
 * @method static object config()
 * @method static boolean check()
 * @method static array get()
 *
 * @see \Kkokk\Poster\Lang\Captcha
 */
class Captcha extends Facade
{
    protected static function getFacadeModel(){
        return 'captcha';
    }
}