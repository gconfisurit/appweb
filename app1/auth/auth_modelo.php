<?php
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Auth extends Conectar
{

    public function login($user, $pass)
    {
        $conectar = parent::conexion();
        parent::set_names();


        $sql = "SELECT * FROM Usuarios WHERE Login=? AND Clave =?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $user);
        $sql->bindValue(2, $pass);
        $sql->execute();

        return $sql->fetch();
    }

}