<?php

class RecoverUser extends Conectar {

    public static function all()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT usuario, fecha, codigo_recuperacion FROM RecuperarUsuario";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByUser($key)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT usuario, fecha, codigo_recuperacion FROM RecuperarUsuario WHERE usuario = ?";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue(1,$key);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function insert($usuario, $codigo)
    {
        $i = 0;
        date_default_timezone_set('America/Caracas');
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "INSERT INTO RecuperarUsuario(usuario, fecha, codigo_recuperacion) VALUES(?,?,?)";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue($i+=1, $usuario);
        $result->bindValue($i+=1, date(FORMAT_DATETIME_FOR_INSERT));
        $result->bindValue($i+=1, $codigo);
        return $result->execute();
    }

    public static function update($usuario, $codigo)
    {
        $i = 0;
        date_default_timezone_set('America/Caracas');
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql = "UPDATE RecuperarUsuario SET fecha=?, codigo_recuperacion=? WHERE usuario=?";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue($i+=1, date(FORMAT_DATETIME_FOR_INSERT));
        $result->bindValue($i+=1, $codigo);
        $result->bindValue($i+=1, $usuario);
        return $result->execute();
    }

    public static function delete($key)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql = "DELETE FROM RecuperarUsuario WHERE usuario = ?";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue(1, $key);
        return $result->execute();
    }
}