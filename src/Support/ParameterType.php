<?php

namespace Kkokk\Poster\Support;

/** @internal */
final class ParameterType
{
    /**
     * PHP 5.6 无法解析 ?Type；运行时校验避免 PHP 8.4 隐式可空声明弃用，
     * 同时保持参数只能是指定类型或 null（不能放宽为任意 callable）。
     *
     * @param mixed $value
     * @param string $type
     * @param string $parameter
     * @throws \TypeError PHP 7+ 参数类型错误
     * @throws \InvalidArgumentException PHP 5.6 参数类型错误
     */
    public static function nullable($value, $type, $parameter)
    {
        if ($value === null || $value instanceof $type) {
            return;
        }
        $message = $parameter . ' must be an instance of ' . $type . ' or null';
        if (class_exists('TypeError', false)) {
            throw new \TypeError($message);
        }
        throw new \InvalidArgumentException($message);
    }
}
