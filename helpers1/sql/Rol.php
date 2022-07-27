<?php

class Rol extends Conectar
{
    public static function todos()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql = "SELECT id, descripcion FROM Roles";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($key)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql="SELECT id, descripcion FROM Roles WHERE id = ?";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue(1,$key);
        $result->execute();
        return $result->fetch(PDO::FETCH_ASSOC);
    }
}