<?php
declare (strict_types=1);//严格模式

namespace Kkokk\LangPoster\Exception;

/**
 * 
 */
class PosterException extends Exception
{
	
	public function __construct($message = "")
	{
		parent::__construct("ErrorVideo : " . $message, self::ERROR_POSTER_CODE, null);
	}
	/**
	 * @Author   lang
	 * @DateTime 2020-08-13T23:17:00+0800
	 * @param    string
	 * @param    array
	 * @return   [type]
	 */
	public function fun($str='abc',$arr=[]){

	}
}