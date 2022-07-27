<?php
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class KpiMarcaModel extends Conectar{

    public function registrar_kpiMarcas($marca)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "INSERT INTO Kpi_marcas (descripcion, fechae) VALUES (?,?)";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $marca);
        $sql->bindValue(2, date("Y/m/d h:i:s"));

        return $sql->execute();
    }

    public function eliminar_kpiMarcas()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "TRUNCATE TABLE Kpi_marcas";
        $sql = $conectar->prepare($sql);
        return $sql->execute();
    }

}