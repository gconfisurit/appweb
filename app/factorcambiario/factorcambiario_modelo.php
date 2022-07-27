<?php
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class FactorCambiario extends Conectar{

    public function get_factor()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();

        //QUERY
        $sql= "SELECT factor FROM SACONF WHERE CodSucu = 00000";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function editar_factor($factor_nuevo)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "UPDATE SACONF SET factor=?, factorm=?, factorp=? WHERE CodSucu = 00000";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $factor_nuevo);
        $sql->bindValue(2, $factor_nuevo);
        $sql->bindValue(3, $factor_nuevo);

        return $sql->execute();
    }

}