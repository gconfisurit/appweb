<?php

//conexion a la base de datos
require_once("../../config/conexion.php");

class Vehiculos extends Conectar
{
    public function registrar_vehiculo($data)
    {
        $i = 0;
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "INSERT INTO [AJ].[dbo].[appVehiculo] (placa, modelo, capacidad, volumen) VALUES(?,?,?,?);";

        $sql = $conectar->prepare($sql);
        $sql->bindValue($i += 1, $data["placa"]);
        $sql->bindValue($i += 1, $data["modelo"]);
        $sql->bindValue($i += 1, $data["capacidad"]);
        $sql->bindValue($i += 1, $data["volumen"]);

        return $sql->execute();
    }

    public function editar_vehiculo($data)
    {
        $i = 0;
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "UPDATE vehiculos SET  Modelo=?,  Capacidad=?,  Volumen=?,  Estado=?  WHERE   ID=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue($i += 1, $data["modelo"]);
        $sql->bindValue($i += 1, $data["capacidad"]);
        $sql->bindValue($i += 1, $data["volumen"]);
        $sql->bindValue($i += 1, $data["estado"]);
        $sql->bindValue($i += 1, $data["id_vehiculo"]);

        return $sql->execute();
    }

//fin editar usuario

    public function editar_estado($id, $estado)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE vehiculos SET estado=? WHERE id=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $estado);
        $sql->bindValue(2, $id);

        return $sql->execute();
    }

    public function eliminar_vehiculo($id)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        //QUERY
//    $sql = "DELETE FROM vehiculos WHERE id = ?";
        $sql = "UPDATE vehiculos SET deleted_at=? WHERE id=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, date("Y/m/d h:i:s"));
        $sql->bindValue(2, $id);

        return $sql->execute();
    }
}

?>
