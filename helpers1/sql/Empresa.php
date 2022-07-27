<?php


class Empresa extends Conectar {

    public static function getInfo()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "select descrip, direc1, telef, rif from SACONF";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->execute();
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public static function getName()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "select descrip from SACONF";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->execute();
        return $result->fetch(PDO::FETCH_ASSOC)['descrip'];
    }

}