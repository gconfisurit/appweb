<?php


class Dates {

    public static function month_name($month_number, $abbreviated = false)
    {
        if (!$abbreviated) {
            $string = str_replace(
                array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'),
                array('ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', ' DICIEMBRE'),
                $month_number
            );
        } else {
            $string = str_replace(
                array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'),
                array('ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', ' DIC'),
                $month_number
            );
        }
        return $string;
    }

    public static function check_in_range($date_start, $date_end, $date_toevaluate)
    {
        $date_start = strtotime($date_start);
        $date_end = strtotime($date_end);
        $date_toevaluate = strtotime($date_toevaluate);
        if (($date_toevaluate >= $date_start) && ($date_toevaluate <= $date_end))
            return true;
        return false;
    }

    public static function daysEnterDates($date_start, $date_end)
    {
        // Da igual el formato de las fechas (dd-mm-aaaa o aaaa-mm-dd)
        return ((strtotime($date_end)-strtotime($date_start))/86400);
    }

    public static function normalize_date($date)
    {
        $string = '';

        if (!empty($date)) {
            $var = explode('/',str_replace('-','/',$date));
            $string = "$var[2]-$var[1]-$var[0]";
        }

        return $string;
    }

    public static function validateDate($date, $format = FORMAT_DATETIME_FOR_INSERT)
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}