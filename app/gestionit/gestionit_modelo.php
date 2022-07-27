<?php
//conexion a la base de datos
require_once("../../config/conexion.php");

class GestionIt extends Conectar
{
    public function registrar_modulo($data)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "INSERT INTO Modulos (nombre, icono, ruta, menu_id, estatus) VALUES(?,?,?,?,?);";

        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $data["nombre"]);
        $sql->bindValue($i+=1, $data["icono"]);
        $sql->bindValue($i+=1, $data["ruta"]);
        $sql->bindValue($i+=1, $data["menu_id"]);
        $sql->bindValue($i+=1, $data["estado"]);

        return $sql->execute();
    }

    public function editar_modulo($data)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE Modulos SET nombre=?, icono=?, ruta=?, menu_id=?, estatus=?  WHERE id=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $data["nombre"]);
        $sql->bindValue($i+=1, $data["icono"]);
        $sql->bindValue($i+=1, $data["ruta"]);
        $sql->bindValue($i+=1, $data["menu_id"]);
        $sql->bindValue($i+=1, $data["estado"]);
        $sql->bindValue($i+=1, $data["id_modulo"]);

        return $sql->execute();
    }

    public function editar_estado_modulo($id, $estado)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "update Modulos set estatus=? where id=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $estado);
        $sql->bindValue(2, $id);

        return $sql->execute();
    }

    public function eliminar_modulo($id) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "DELETE FROM Modulos WHERE id = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $id);

        return $sql->execute();
    }

    public function editar_menuid_en_modulo($id, $menu_id)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "update Modulos set menu_id=? where id=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $menu_id);
        $sql->bindValue(2, $id);

        return $sql->execute();
    }

    public function registrar_menu($data)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "INSERT INTO Menu (nombre, menu_orden, menu_padre, menu_hijo, icono, estatus) VALUES(?,?,?,?,?,?);";

        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $data["nombre"]);
        $sql->bindValue($i+=1, $data["menu_orden"]);
        $sql->bindValue($i+=1, $data["menu_padre"]);
        $sql->bindValue($i+=1, $data["menu_hijo"]);
        $sql->bindValue($i+=1, $data["icono"]);
        $sql->bindValue($i+=1, $data["estado"]);

        return $sql->execute();
    }

    public function editar_menu($data)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE Menu SET nombre=?, menu_orden=?, menu_padre=?, menu_hijo=?, icono=?, estatus=?  WHERE id=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $data["nombre"]);
        $sql->bindValue($i+=1, $data["menu_orden"]);
        $sql->bindValue($i+=1, $data["menu_padre"]);
        $sql->bindValue($i+=1, $data["menu_hijo"]);
        $sql->bindValue($i+=1, $data["icono"]);
        $sql->bindValue($i+=1, $data["estado"]);
        $sql->bindValue($i+=1, $data["id_menu"]);

        return $sql->execute();
    }

    public function editar_estado_menu($id, $estado)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "update Menu set estatus=? where id=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $estado);
        $sql->bindValue(2, $id);

        return $sql->execute();
    }

    public function eliminar_menu($id) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "DELETE FROM Menu WHERE id = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $id);

        return $sql->execute();
    }

    public function editar_menuportipo_en_menu($id, $menu_id, $tipo)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "update Menu set $tipo=? where id=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $menu_id);
        $sql->bindValue(2, $id);

        return $sql->execute();
    }
}