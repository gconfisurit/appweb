<?php
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Skunovendidos extends Conectar{

    public function getnovendidos($data){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT sf.numerod, sf.codvend, sv.descrip AS vendedor, sc.codclie as codclie,sc.descrip AS cliente, si.coditem,
                       si.descrip1, sp.marca, si.esunid, sf.signo*si.cantidad AS cantidad, sf.signo*si.totalitem / si.tasai AS totalitem,
                       se.Existen AS bultos, se.ExUnidad AS paquetes, sf.fechae
                FROM SACLIE AS SC
                    INNER JOIN SAFACT AS SF ON SC.CodClie = SF.CodClie
                    INNER JOIN SAITEMFAC AS SI ON SF.NumeroD = SI.NumeroD
                    INNER JOIN SAPROD AS SP ON SI.CodItem = SP.CodProd
                    INNER JOIN SAVEND AS SV ON sf.CodVend = sv.CodVend
                    INNER JOIN SAEXIS AS SE ON si.CodItem = SE.CodProd
                WHERE (SUBSTRING(CONVERT(VARCHAR,SF.FechaE,120),1,10) >= ? AND SUBSTRING(CONVERT(VARCHAR,SF.FechaE,120),1,10) <= ? )
                  AND (SF.NumeroD = SI.NumeroD AND SF.TipoFac = SI.TipoFac)
                  AND SC.CodClie = SF.CodClie AND SI.NroLineaC = 0
                  AND SF.TipoFac = 'F' AND sf.Monto <> 0 AND se.CodUbic = 01
                ORDER BY SF.FechaE DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$data['fechai']);
        $sql->bindValue(2,$data['fechaf']);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}

