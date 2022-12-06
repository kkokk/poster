<?php
/**
 * Author: lang
 * Email: 732853989@qq.com
 * Date: 2022/12/6
 * Time: 18:10
 */

namespace Kkokk\Poster\Interfaces;


abstract class MyCaptcha
{
    abstract public function config($param = []);
    abstract public function get();
}