
<?php
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class TasaDolar extends Conectar{

    public function get_tasadolar(){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();

        //QUERY
        $sql= "SELECT FechaE AS fechae, Tasa AS tasa FROM SACOMP WHERE Tasa IS NOT NULL ORDER BY FechaE DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    }
}
