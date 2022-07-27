<?php

class Auditoria extends Conectar {

    public static function todos()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT id, fecha, usuario, accion FROM Auditoria";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByUser($key)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT id, fecha, usuario, accion FROM Auditoria WHERE usuario = ?";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue(1,$key);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function insert($usuario, $accion)
    {
        $i = 0;
        date_default_timezone_set('America/Caracas');
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "INSERT INTO Auditoria(fecha, usuario, accion) VALUES(?,?,?)";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue($i+=1, date(FORMAT_DATETIME2));
        $result->bindValue($i+=1, $usuario);
        $result->bindValue($i+=1, $accion);
        return $result->execute();
    }
}