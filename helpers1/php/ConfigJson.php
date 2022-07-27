<?php

class ConfigJson
{
    public static function get() : array {
        $json_string = file_get_contents(PATH_CONFIG."strings_config.json");
        if (Strings::avoidNullOrEmpty($json_string))
            return json_decode($json_string, true);
        return array();
    }

    public static function set(array $configJson) : bool {
        $json_string = json_encode($configJson);
        return file_put_contents(PATH_CONFIG."strings_config.json", $json_string);
    }

    public static function getParameters($module) : array {
        $json_string = file_get_contents(PATH_CONFIG."strings_config.json");
        if (Strings::avoidNullOrEmpty($json_string)) {
            $json = json_decode($json_string, true);
            return ArraysHelpers::validateWithParameter($json, $module);
        }
        return array();
    }

    public static function getParameterOfModule($directory, $parameter) : string {
        $json_string = file_get_contents(PATH_CONFIG."strings_config.json");
        if (Strings::avoidNullOrEmpty($json_string)) {
            $json = json_decode($json_string, true);
            $module = ArraysHelpers::validateWithPosAndParameter(Modulos::getByRoute($directory), 0, 'nombre');
            $arr_parameters_of_module = ArraysHelpers::validateWithParameter($json, $module);
            return ArraysHelpers::validateWithParameter($arr_parameters_of_module, $parameter);
        }
        return '';
    }
}