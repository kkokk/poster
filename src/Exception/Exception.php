<?php
declare (strict_types=1);//严格模式

namespace Kkokk\LangPoster\Exception;

use Throwable;

/**
 * Created By 1
 * Author：smalls
 * Email：smalls0098@gmail.com
 * Date：2020/4/26 - 22:26
 **/
class Exception extends \Exception
{


    const INVALID_MANAGER_CODE = 1;
    const ERROR_POSTER_CODE    = 2;

    if (interface_exists(Throwable::class)) {

	    public function __construct($message = "", $code = 0, Throwable $previous = null)
	    {
	        parent::__construct($message, $code, $previous);
	    }

	}else{

		public function __construct($message = "", $code = 0, $previous = null)
	    {
	        parent::__construct($message, $code, $previous);
	    }

	}
    
}