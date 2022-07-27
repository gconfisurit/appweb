<?php


class Usuarios extends Conectar
{
    public static function todos()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT cedula, login, nomper, email, clave, id_rol, fecha_registro, fecha_ult_ingreso, estado 
               FROM Usuarios WHERE deleted_at IS NULL";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function byUserName($key)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT cedula, login, nomper, email, clave, id_rol, fecha_registro, fecha_ult_ingreso, estado
                FROM Usuarios WHERE deleted_at IS NULL AND login = ?";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue(1,$key);
        $result->execute();
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public static function byDni($key)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT cedula, login, nomper, email, clave, id_rol, fecha_registro, fecha_ult_ingreso, estado 
                FROM Usuarios WHERE deleted_at IS NULL AND cedula = ?";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue(1,$key);
        $result->execute();
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public static function byRol($key)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT cedula, login, nomper, email, clave, id_rol, fecha_registro, fecha_ult_ingreso, estado 
               FROM Usuarios WHERE deleted_at IS NULL AND ID_Rol = ?";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue(1,$key);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function updatePassword($user, $password)
    {
        $i = 0;
        date_default_timezone_set('America/Caracas');
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql = "UPDATE Usuarios SET Clave=? WHERE Login=?";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue($i+=1, $password);
        $result->bindValue($i+=1, $user);
        return $result->execute();
    }
}