<?php
namespace Kkokk\Tests;
use Kkokk\LangPoster\Exception\Exception;
use Kkokk\LangPoster\PosterManager;

require '../vendor/autoload.php';
/**
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 10:07:58
 * @Last Modified by:   lang
 * @Last Modified time: 2020-08-14 18:02:55
 */

try {
		
	// print_r(PosterManager::Poster()->start('http://baidu.com'));
		
	// $PosterManager = new PosterManager();
	PosterManager::poster('poster_user.png')
	->buildIm(600,500)
	->buildImage('https://test.acyapi.51acy.com/photo/logo.png')
	->getPoster();

} catch (Exception $e) {
	echo $e->getMessage();
}