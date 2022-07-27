
<?php
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class LibroCompra extends Conectar{

    public function getLibroPorFecha($fechai, $fechaf)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql= "SELECT fechacompra, id3ex, descripex, tipodoc, nroretencion, numerodoc, nroctrol, tiporeg, docafectado, totalcompraconiva, mtoexento, totalcompra, alicuota_iva, monto_iva, retencioniva, porctreten, fecharetencion 
                FROM DBO.VW_ADM_LIBROIVACOMPRAS WHERE ( '$fechai' <=FECHATRAN) AND (FECHATRAN<= '$fechaf' ) 
                ORDER BY (YEAR(FechaCompra)*10000)+(MONTH(FechaCompra)*100)+DAY(FechaCompra),FECHAT";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$fechai);
        $sql->bindValue($i+=1,$fechaf);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    }
}

