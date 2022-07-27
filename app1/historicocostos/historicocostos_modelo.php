
<?php
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Historicocostos extends Conectar{

    public function get_historicocostos_por_rango($fechai, $fechaf){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();

        //QUERY
        $sql= "SELECT hist_sku.codprod, hist_sku.fechae, hist_sku.costo, hist_sku.cantidad, hist_sku.marca, descrip FROM hist_sku INNER JOIN saprod ON hist_sku.codprod = saprod.codprod
                        WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, hist_sku.FechaE)) BETWEEN ? AND ?
                        ORDER BY hist_sku.marca ASC, hist_sku.codprod ASC, hist_sku.fechae DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$fechai);
        $sql->bindValue(2,$fechaf);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
