<?php

class Strings {

    public static function avoidNull($string) : string {
        return ( ! is_null($string) ) ? $string : '';
    }

    public static function avoidNullOrEmpty($string) : bool {
        return ( !is_null($string) and !empty($string) );
    }

    public static function addCero($num) : string {
        if(intval($num)<=9)
            return "0".$num;
        return $num;
    }

    public static function rdecimal($number, $precision = 1, $separator = '.', $separatorDecimal = ',') : string {
        $numberParts = explode($separator, $number);
        if ($precision == 0) {
            $response = number_format(floatval($numberParts[0]), 0, $separatorDecimal, $separator);
        } else {
            $response = number_format(floatval($numberParts[0]), 0, ",", ".");
            if (count($numberParts) > 1) {
                $response .= $separatorDecimal;
                $response .= substr(
                    $numberParts[1],
                    0,
                    $precision
                );
            }
        }

        return $response;
    }

    public static function rdecimal2($number, $precision = 2, $separator = '.', $separatorDecimal = ',')
{
	$numberParts = explode($separator, $number);
	$response = number_format($numberParts[0], 0, ",", ".");
	if (count($numberParts) > 1) {
		$response .= $separatorDecimal;
		$response .= substr(
			$numberParts[1],
			0,
			$precision
		);
	}
	return $response;
}

    public static function titleFromJson($name = '') : string {
        $string = file_get_contents(PATH_CONFIG."strings.json");
        $json = json_decode($string, true);
        if ($string != false and $json != null)
            return $json[strtolower($name)]['title'];
        return '';
    }

    public static function DescriptionFromJson($name = '') : string {
        $string = file_get_contents(PATH_CONFIG."strings.json");
        $json = json_decode($string, true);
        if ($string != false and $json != null)
            return $json[strtolower($name)]['description'];
        return '';
    }

    public static function randomString($length = 10)
    {
        $salt = '1234567890';
        $rand = '';
        for ($i = 0; $i < $length; $i++) {
            //Loop hasta que el string aleatorio contenga la longitud ingresada.
            $num = rand() % strlen($salt);
            $tmp = substr($salt, $num, 1);
            $rand = $rand . $tmp;
        }
        //Retorno del string aleatorio.
        return $rand;
    }

    public static function remove_accents($cadena) {

        //Reemplazamos la A y a
        $cadena = str_replace(
            array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
            array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
            $cadena
        );

        //Reemplazamos la E y e
        $cadena = str_replace(
            array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
            array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
            $cadena );

        //Reemplazamos la I y i
        $cadena = str_replace(
            array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
            array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
            $cadena );

        //Reemplazamos la O y o
        $cadena = str_replace(
            array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
            array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
            $cadena );

        //Reemplazamos la U y u
        $cadena = str_replace(
            array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
            array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
            $cadena );

        //Reemplazamos la N, n, C y c
        $cadena = str_replace(
            array('Ñ', 'ñ', 'Ç', 'ç'),
            array('N', 'n', 'C', 'c'),
            $cadena
        );

        return $cadena;
    }

}