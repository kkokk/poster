<?php
declare (strict_types=1);//严格模式

namespace Kkokk\Poster\Exception;
use Throwable;

if (interface_exists(Throwable::class)) {

	class Exception extends \Exception
	{
	    const SYSTEM_CODE = 1;
	    const ERROR_POSTER_CODE    = 2;

	    public function __construct($message = "", $code = 0, Throwable $previous = null)
	    {
	        parent::__construct($message, $code, $previous);
	    }

	}

}else{
	
	class Exception extends \Exception
	{
		const SYSTEM_CODE = 1;
		const ERROR_POSTER_CODE    = 2;

		public function __construct($message = "", $code = 0, $previous = null)
		    {
		        parent::__construct($message, $code, $previous);
		    }
	}

}

