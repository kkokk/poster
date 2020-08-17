<?php
declare (strict_types=1);//严格模式

namespace Kkokk\Poster\Exception;
/**
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 09:56:51
 * @Last Modified by:   lang
 * @Last Modified time: 2020-08-17 14:05:52
 */

class SystemException extends Exception
{
	
	public function __construct($message = "")
	{
		parent::__construct("ErrorSystem : " . $message, self::SYSTEM_CODE, null);
	}

}