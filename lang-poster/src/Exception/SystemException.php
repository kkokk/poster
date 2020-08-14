<?php
declare (strict_types=1);//严格模式

namespace Kkokk\LangPoster\Exception;
/**
 * @Author: lang
 * @Email:  732853989@qq.com
 * @Date:   2020-08-14 09:56:51
 * @Last Modified by:   lang
 * @Last Modified time: 2020-08-14 17:16:43
 */

class SystemException extends Exception
{
	
	public function __construct($message = "")
	{
		parent::__construct("ErrorSystem : " . $message, self::SYSTEM_CODE, null);
	}

}