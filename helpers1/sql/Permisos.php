<?php


class Permisos extends Conectar
{
    public static function getRolesGrupoPorRolID($key)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT id, id_modulo, id_roles FROM Roles_Grupo WHERE id_roles=?";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue(1,$key);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPermisosPorUsuarioID($key)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT id, id_usuarios, id_modulo FROM Permisos WHERE id_usuarios=?";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue(1,$key);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function verficarPermisoPorSessionUsuario($ruta)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT p.id, id_usuarios, id_modulo, m.ruta
                FROM Permisos p
                INNER JOIN Modulos m ON m.id = p.ID_Modulo
                WHERE id_usuarios=? AND m.ruta=?";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue($i+=1, $_SESSION['cedula']);
        $result->bindValue($i+=1, $ruta);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function registrar_permiso($data)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "INSERT INTO Permisos (id_usuarios, id_modulo) VALUES (?,?)";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue($i+=1, $data['id']);
        $result->bindValue($i+=1, $data['modulo_id']);

        return $result->execute();
    }

    public static function registrar_rolmod($data)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "INSERT INTO Roles_Grupo(ID_Modulo, ID_Roles) VALUES(?,?)";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue($i+=1, $data['modulo_id']);
        $result->bindValue($i+=1, $data['id']);

        return $result->execute();
    }

    public static function borrar_permiso($data)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "DELETE FROM Permisos WHERE id_usuarios=? AND id_modulo=?";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue($i+=1, $data['id']);
        $result->bindValue($i+=1, $data['modulo_id']);

        return $result->execute();
    }

    public static function borrar_permiso_user($user_id)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "DELETE FROM Permisos WHERE id_usuarios=?";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue(1, $user_id);

        return $result->execute();
    }

    public static function borrar_rolmod($data)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "DELETE FROM Roles_Grupo WHERE ID_Modulo=? AND ID_Roles=?";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue($i+=1, $data['modulo_id']);
        $result->bindValue($i+=1, $data['id']);

        return $result->execute();
    }
}