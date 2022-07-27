<?php

//llamar a la conexion de la base de datos
require_once("../../config/conexion.php");


class Roles extends Conectar
{

    //listar los usuarios
    public function get_roles()
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT id, descripcion FROM roles";

        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }


    public function registrar_rol($data)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "INSERT INTO roles VALUES(?);";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $data['descripcion']);

        return $sql->execute();
    }

    public function editar_rol($data)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE roles SET Descripcion=? WHERE  ID=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $data["descripcion"]);
        $sql->bindValue(2, $data["id_rol"]);

        return $sql->execute();
    }

//fin editar usuario

//mostrar los datos del usuario por el id
    public function get_rol_por_id($id)
    {

        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT id, descripcion FROM roles WHERE ID=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $id);
        $sql->execute();

        return $resultado = $sql->fetchAll();

    }

    public function get_nombre_rol($rol)
    {

        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT id, descripcion FROM Roles WHERE Descripcion = ?";

        $sql = $conectar->prepare($sql);

        $sql->bindValue(1, $rol);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_relacion_rol_usuario($id_rol)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT u.* FROM Roles r
                    INNER JOIN Usuarios u ON u.ID_Rol = r.id
                WHERE r.ID = ?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $id_rol);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function eliminar_chofer($id)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "DELETE FROM Roles WHERE id = ?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $id);

        return $sql->execute();
    }
}

?>
