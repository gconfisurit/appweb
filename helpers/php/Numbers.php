<?php


class Numbers
{
    public static function avoidNull($number) : float {
        return ( ! is_null($number) ) ? $number : 0;
    }

    public static function generateRandomNumber($cant_digits = 1, $except = array()) : int {
        do {
            $number = Strings::randomString($cant_digits);
        } while (in_array($number, $except));
        return intval($number);
    }
}
