<?php


class Maestroclientes extends Conectar {

    public function getMaestro($edv)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        if($edv == 'Todos'){

            $sql= "SELECT a.codclie, a.descrip, a.activo, a.codvend, b.Ruta_Alternativa, b.Ruta_Alternativa_2, b.DiasVisita, a.Direc1, a.Direc2, b.CodNestle
               FROM saclie AS a 
                   INNER JOIN SACLIE_01 AS b ON a.CodClie=b.CodClie  
               ORDER BY a.CodClie DESC";

        }else{
            $sql= "SELECT a.codclie, a.descrip, a.activo, a.codvend, b.Ruta_Alternativa, b.Ruta_Alternativa_2, b.DiasVisita, a.Direc1, a.Direc2, b.CodNestle
               FROM saclie AS a 
                   INNER JOIN SACLIE_01 AS b ON a.CodClie=b.CodClie  
               WHERE a.CodVend = ? OR b.Ruta_Alternativa = ? OR b.Ruta_Alternativa_2 =  ?  
               ORDER BY a.CodClie DESC";

        }
        

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$edv);
        $sql->bindValue(2,$edv);
        $sql->bindValue(3,$edv);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);

    }
}