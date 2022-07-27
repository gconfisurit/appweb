<?php


class Proveedores extends Conectar {

    public static function todos()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT [SAPROV].[CodProv] , [SAPROV].[Descrip] FROM [AJ].[dbo].[SAPROV]  ORDER BY [SAPROV].[Descrip] ASC";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

}