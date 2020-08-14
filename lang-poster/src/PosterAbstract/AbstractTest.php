<?php
namespace Kkokk\LangPoster\PosterAbstract;
use Kkokk\LangPoster\Lang\PosterAbstract;
/**
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 11:49:51
 * @Last Modified by:   lang
 * @Last Modified time: 2020-08-14 14:02:46
 */
/**
 * 
 */
class AbstractTest extends PosterAbstract
{
	
	// public function __construct($options)
	// {
	// 	// print_r($options);
	// }

	public function poster(string $url):array
	{
		return ['url'=>$url];
	}
}