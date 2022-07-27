<?php


class Almacen extends Conectar {

    public static function todos()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT CodUbic AS codubi, Descrip AS descrip FROM sadepo ORDER BY codubic";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($key)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT CodUbic AS codubi, Descrip AS descrip FROM sadepo WHERE CodUbic=? ORDER BY codubic";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue(1,$key);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}