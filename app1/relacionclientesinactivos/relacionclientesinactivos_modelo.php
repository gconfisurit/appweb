
<?php
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class RelacionClientesInactivos extends Conectar
{

    public function get_todos_los_clientes_inactivos()
    {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT saclie.codclie, saclie.descrip, saclie.id3, saclie.direc1 AS direccion, saclie.telef AS telefono, saclie.movil AS movil, saclie.diascred AS diascredito, saclie.limitecred AS limitecredito, saclie.descto AS descuento, saclie.CodVend AS edv, saclie.fechae AS fechae, limitecred, 
                    tipoid3 AS idtid3, saclie.Activo AS idactivo
                    FROM saclie 
                    WHERE saclie.activo <> '1'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

}