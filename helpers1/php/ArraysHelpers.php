<?php


class ArraysHelpers
{
    public static function validate($arr) {
        return (is_array($arr) == true and count($arr) > 0);
    }

    public static function validateWithPos(array $arr, int $position) {
        if (is_array($arr) == true and count($arr) > 0 and array_key_exists($position, $arr))
            return $arr[$position];
        return array();
    }

    public static function validateWithParameter(array $arr, string $parameter) {
        if (is_array($arr) == true and count($arr) > 0 and array_key_exists($parameter, $arr))
            return $arr[$parameter];
        return array();
    }

    public static function validateWithPosAndParameter(array $arr, int $position, string $parameter) {
        if (is_array($arr) == true and count($arr) > 0)
            return $arr[$position][$parameter];
        return 0;
    }
}