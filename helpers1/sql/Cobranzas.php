<?php


class Cobranzas extends Conectar {

    public static function getCobranzasRebajadas($ruta, $fechai, $fechaf)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT V.CODVEND, V.fechae, V.numerod, V.NumeroFac, V.MONTO, V.Descrip
                FROM (
                    SELECT FA.codvend, FA.fechae, FA.NumeroD, FA.NumeroD AS NumeroFac, FA.MONTO AS MONTO, FA.Descrip
                    FROM VW_ADM_FACTURAS FA
                    WHERE (FA.Tipofac In ('A','B')) AND (FA.Contado<>0)
                    AND (SUBSTRING(FA.CODSUCU,1,LEN('00000'+''))='00000'+'')
                    UNION ALL
                    SELECT CC.codvend, CC.FechaE, CC.NUMEROD, ISNULL(PG.NUMEROD,CC.NumeroD) AS NUMEROFAC,
                        (CASE WHEN CC.TIPOCXC LIKE '3%' THEN -1
                        WHEN CC.TIPOCXC LIKE '2%' THEN -1 ELSE 1 END)*ISNULL(PG.MONTO-PG.MTOTAX+PG.RetenIVA+CancelI,CC.MONTO-CC.MTOTAX) AS MONTO, CL.Descrip
                    FROM SAACXC CC
                        INNER JOIN SACLIE CL ON (CL.CodClie=CC.CodClie)
                        LEFT JOIN SAPAGCXC PG ON (PG.NROPPAL=CC.NROUNICO)
                    WHERE ((CC.TipoCxc LIKE '4%') OR (CC.Tipocxc LIKE '3%') OR (CC.Tipocxc LIKE '2%'))
                    AND (CC.EsReten=0) AND (FromTran=1) AND (SUBSTRING(CC.CODSUCU,1,LEN('00000'+''))='00000'+'')
                ) V
                WHERE
                (CONVERT(DATETIME,CONVERT(DATETIME, ?,120)+' 00:00:00',120)<=V.FECHAE) AND
                (V.FECHAE<=CONVERT(DATETIME,CONVERT(DATETIME, ?,120)+' 23:59:59',120))
                AND (codvend = ?)";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue($i+=1, $fechai);
        $result->bindValue($i+=1, $fechaf);
        $result->bindValue($i+=1, $ruta);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}