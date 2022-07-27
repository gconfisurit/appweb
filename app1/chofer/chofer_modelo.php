<?php

//conexion a la base de datos
require_once("../../config/conexion.php");


class Chofer extends Conectar
{
    public function registrar_chofer($data)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();


        $sql = "INSERT INTO appchofer (cedula, descripcion , estatus) VALUES(?,?,?);";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $data["cedula"]);
        $sql->bindValue(2, $data["descripcion"]);
        //$sql->bindValue(3, $data["fecha_ingreso"]);
        $sql->bindValue(3, $data["estatus"]);

        return $sql->execute();
    }

    public function editar_chofer($data)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "UPDATE [AJ].[dbo].appchofer SET descripcion=?, estatus=?  WHERE cedula=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $data["descripcion"]);
        $sql->bindValue(2, $data["estatus"]);
        $sql->bindValue(3, $data["cedula"]);

        return $sql->execute();
    }

    public function editar_estado($id, $estado)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "UPDATE [AJ].[dbo].appchofer SET estado=? WHERE cedula=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $estado);
        $sql->bindValue(2, $id);
        return $sql->execute();
    }

    public function eliminar_chofer($id)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
//    $sql = "DELETE FROM choferes WHERE id = ?";
        $sql = "UPDATE [AJ].[dbo].appchofer SET deleted_at=? WHERE Cedula=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, date("Y/m/d h:i:s"));
        $sql->bindValue(2, $id);

        return $sql->execute();
    }
}
