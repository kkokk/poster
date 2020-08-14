<?php
namespace Kkokk\LangPoster\Lang;
/**
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 11:18:03
 * @Last Modified by:   lang
 * @Last Modified time: 2020-08-14 14:02:35
 */

/**
 * 执行
 */
abstract class PosterAbstract
{
	
	protected $options = [];
	abstract public function poster(string $url):array;
}