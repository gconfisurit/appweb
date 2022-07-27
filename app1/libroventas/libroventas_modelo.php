
<?php
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class LibroVenta extends Conectar{

    public function getLibroPorFecha($fechai, $fechaf)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql= "SELECT totalventas, montoiva_contribuyeiva, mtoexento, fechaemision, rifcliente, nombre, tipodoc, numerodoc, nroctrol, tiporeg, factafectada, alicuota_contribuyeiva
                FROM DBO.VW_ADM_LIBROIVAVENTAS
                WHERE ( ? <=FECHAEMISION) AND (FECHAEMISION<= ? ) AND ((FECHARETENCION IS NULL)
                    OR (( ? <=FECHARETENCION) AND (FECHARETENCION<= ? ))) AND TIPODOC != 'RET'
                ORDER BY (YEAR(FECHAFACTURA)*10000)+(MONTH(FECHAFACTURA)*100)+DAY(FECHAFACTURA),FECHAT";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$fechai);
        $sql->bindValue($i+=1,$fechaf);
        $sql->bindValue($i+=1,$fechai);
        $sql->bindValue($i+=1,$fechaf);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getRetencionesOtrosPeriodos($fechai, $fechaf)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql= "SELECT retencioniva, fechaemision, rifcliente, nombre, tipodoc, numerodoc, tiporeg, factafectada, fecharetencion, totalgravable_contribuye, totalivacontribuye
                FROM DBO.VW_ADM_LIBROIVAVENTAS
                WHERE ( ? <=FECHAEMISION) AND (FECHAEMISION<= ? ) AND (NOT(FECHARETENCION IS NULL)
                    AND NOT(( ? <=FECHARETENCION) AND (FECHARETENCION<= ? ))) AND TIPO='81'
                ORDER BY FECHAT";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$fechai);
        $sql->bindValue($i+=1,$fechaf);
        $sql->bindValue($i+=1,$fechai);
        $sql->bindValue($i+=1,$fechaf);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getRetencionItem($fechai, $fechaf, $numerodoc)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql= "SELECT retencioniva, nroretencion, retencioniva
                FROM DBO.VW_ADM_LIBROIVAVENTAS
                WHERE ( ? <=FECHAEMISION) AND (FECHAEMISION<= ? ) AND ((FECHARETENCION IS NULL)
                    OR (( ? <=FECHARETENCION) AND (FECHARETENCION<= ? ))) AND factafectada = ? AND tipodoc = 'RET'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$fechai);
        $sql->bindValue($i+=1,$fechaf);
        $sql->bindValue($i+=1,$fechai);
        $sql->bindValue($i+=1,$fechaf);
        $sql->bindValue($i+=1,$numerodoc);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    }
}

