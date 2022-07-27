<?php


class Marcas extends Conectar {

    public static function todos()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT DISTINCT(marca) FROM saprod WHERE activo = '1' AND marca IS NOT NULL ORDER BY marca ASC";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByName($key)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT DISTINCT(marca) FROM saprod WHERE activo = '1' AND marca=?";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue(1,$key);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}