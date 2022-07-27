<?php


class Session {

    public static function create($data) {
        $_SESSION = array(
            "cedula"  => $data["Cedula"],
            "login"   => $data["Login"],
            "nomper"  => $data["Nomper"],
            "email"   => $data["Email"],
            "rol"     => $data["ID_Rol"],
            "empresa" => Empresa::getName()
        );
    }

    public static function getValue($val) {
        return $_SESSION[$val];
    }

}