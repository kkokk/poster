<?php

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

    public function __construct($message = "", $code = null, $previous = null)
    {
        parent::__construct("SystemException : " . $message, $code ?: self::ERROR_POSTER_CODE, $previous);
    }

}