<?php

//conexion a la base de datos
require_once("../../config/conexion.php");

class Usuario extends Conectar
{
    public function registrar_usuario($data)
    {
        $i=0;
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "INSERT INTO usuarios(Cedula, Login, Nomper, Email, Clave, ID_Rol, Fecha_Registro, Fecha_Ult_Ingreso, Estado) 
                VALUES(?,?,?,?,?,?,?,?,?)";

        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $data["cedula"]);
        $sql->bindValue($i+=1, $data["login"]);
        $sql->bindValue($i+=1, ucwords($data["nomper"]));
        $sql->bindValue($i+=1, strtolower($data["email"]));
        $sql->bindValue($i+=1, md5($data["clave"]));
        $sql->bindValue($i+=1, $data["rol"]);
        $sql->bindValue($i+=1, date(FORMAT_DATETIME_FOR_INSERT));
        $sql->bindValue($i+=1, date(FORMAT_DATETIME_FOR_INSERT));
        $sql->bindValue($i+=1, $data["estado"]);

        return $sql->execute();
    }

    public function editar_usuario($data)
    {
        $i=0;
        $conectar = parent::conexion();
        parent::set_names();

        $sql = (hash_equals('', $data["clave"]))
            ? "UPDATE usuarios SET  Login=?,  Nomper=?,  Email=?,  ID_Rol=?,  Estado=?  WHERE   Cedula=?"
            : "UPDATE usuarios SET  Login=?,  Nomper=?,  Email=?,  Clave=?,  ID_Rol=?,  Estado=?  WHERE   Cedula=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $data["login"]);
        $sql->bindValue($i+=1, ucwords($data["nomper"]));
        $sql->bindValue($i+=1, strtolower($data["email"]));
        if (!hash_equals('', $data["clave"]))
            $sql->bindValue($i+=1, md5($data["clave"]));
        $sql->bindValue($i+=1, $data["rol"]);
        $sql->bindValue($i+=1, $data["estado"]);
        $sql->bindValue($i+=1, $data["id_usuario"]);

        return $sql->execute();
    }

    //fin editar usuario


    public function editar_estado($id, $estado)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "update usuarios set estado=? where cedula=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $estado);
        $sql->bindValue(2, $id);

        return $sql->execute();
    }

    public function get_cedula_correo_del_usuario($cedula, $email)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT cedula, login, nomper, email, clave, id_rol, fecha_registro, fecha_ult_ingreso, estado 
                FROM usuarios WHERE cedula=? OR email=? AND deleted_at IS NULL";

        $sql = $conectar->prepare($sql);

        $sql->bindValue(1, $cedula);
        $sql->bindValue(2, $email);
        $sql->execute();

        return $resultado = $sql->fetchAll();

    }

    public function eliminar_usuario($dni) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
//        $sql = "DELETE FROM usuarios WHERE cedula = ?";
        $sql = "update usuarios set deleted_at=? where cedula=?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, date("Y/m/d h:i:s"));
        $sql->bindValue(2, $dni);

        return $sql->execute();
    }

}
?>
