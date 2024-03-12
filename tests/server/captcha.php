<?php
/**
 * User: lang
 * Date: 2023/8/25
 * Time: 10:15
 */

use Kkokk\Poster\PosterManager;
require '../../vendor/autoload.php';


$res = PosterManager::Captcha()->type('click')->get();

print_r($res);