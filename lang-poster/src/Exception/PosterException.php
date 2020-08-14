<?php
declare (strict_types=1);//严格模式

namespace Kkokk\LangPoster\Exception;
/**
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 09:56:51
 * @Last Modified by:   lang
 * @Last Modified time: 2020-08-14 17:27:01
 */

class PosterException extends Exception
{
	
	public function __construct($message = "")
	{
		parent::__construct("PosterException : " . $message, self::ERROR_POSTER_CODE, null);
	}

}