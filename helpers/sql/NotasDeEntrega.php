<?php


class NotasDeEntrega extends Conectar {

    public static function getHeaderById($numerod)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT a.numerod, a.tipofac, a.codclie, a.rif, a.rsocial, a.direccion, b.direc2 AS direccion2, a.telefono, b.Represent,
                       CONCAT(a.codvend,' ', c.Descrip) AS codvend, a.total, a.fechae, a.notas1, a.descuento, a.subtotal 
                FROM sanota AS a 
                    INNER JOIN saclie AS b ON a.codclie = b.codclie 
                    INNER JOIN SAVEND AS c ON a.codvend = c.CodVend  
                WHERE a.TipoFac = 'C' AND a.numerod = ?";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue(1, $numerod);
        $result->execute();
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public static function getDetailById($numerod, $tipo = 'C')
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT numerod, tipofac, coditem,  descripcion, cantidad, precio, totalitem, esunidad, esexento, codvend, 
                        fechae, tipopvp, descuento, total
                FROM saitemnota
                WHERE numerod = ? AND TipoFac = ?";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue(1, $numerod);
        $result->bindValue(2, $tipo);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    //////////////////// DEVOLUCION N/E ////////////////////////////////////////

    public static function getHeaderById2($numerod)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT a.numerod, a.tipofac, a.codclie, a.rif, a.rsocial, a.direccion, b.direc2 AS direccion2, a.telefono, 
                       CONCAT(a.codvend,' ', c.Descrip) AS codvend, a.total, a.fechae, a.notas1, a.descuento, a.subtotal 
                FROM sanota AS a 
                    INNER JOIN saclie AS b ON a.codclie = b.codclie 
                    INNER JOIN SAVEND AS c ON a.codvend = c.CodVend  
                WHERE a.TipoFac = 'D' AND a.numerod = ?";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue(1, $numerod);
        $result->execute();
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public static function getDetailById2($numerod, $tipo = 'D')
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT numerod, tipofac, coditem,  descripcion, cantidad, precio, totalitem, esunidad, esexento, codvend, 
                        fechae, tipopvp, descuento, total
                FROM saitemnota
                WHERE numerod = ? AND TipoFac = ?";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue(1, $numerod);
        $result->bindValue(2, $tipo);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}