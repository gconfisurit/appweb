<?php

class KpiMarcas extends Conectar
{
    public static function todos($orden = 'ASC')
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql = "SELECT id, descripcion, fechae FROM Kpi_marcas ORDER BY id $orden";

        /* $sql = "SELECT distinct  sainsta.CodInst, sainsta.Descrip
			from [AJ].[dbo].sainsta 
			inner join [AJ].[dbo].saprod on saprod.CodInst = sainsta.CodInst
			inner join [AJ].[dbo].saexis on saexis.CodProd = saprod.CodProd 
			where (saexis.codubic = '01') and (saexis.Existen > 0 or saexis.ExUnidad>0) ORDER BY sainsta.CodInst $orden ";*/

        $result = (new Conectar())->conexion()->prepare($sql);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function bultosActivadosPorMarca(
        $ruta,
        $marca,
        $fechai,
        $fechaf,
        $detalle = false
    ) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $cells_fact =
            $detalle == true
                ? ", SAFACT.NumeroD AS numerod, safact.Descrip AS cliente, saitemfac.Descrip1 AS producto, saitemfac.TipoFac AS tipofac, (CASE WHEN EsUnid = '0' THEN Cantidad ELSE 0 END) AS bult, (CASE WHEN EsUnid = '1' THEN Cantidad ELSE 0 END) AS paq"
                : '';
        $cells_nota =
            $detalle == true
                ? ", SANOTA.numerod, sanota.rsocial AS cliente, saitemnota.descripcion AS producto, saitemnota.tipofac AS tipofac, (CASE WHEN esunidad = '0' THEN cantidad ELSE 0 END) AS bult, (CASE WHEN esunidad = '1' THEN cantidad ELSE 0 END) AS paq"
                : '';

        $sql = "SELECT DISTINCT(CodClie) $cells_fact FROM saitemfac INNER JOIN saprod ON saitemfac.coditem = saprod.codprod INNER JOIN
                SAFACT ON SAITEMFAC.NumeroD = SAFACT.NumeroD WHERE
                DATEADD(dd, 0, DATEDIFF(dd, 0, saitemfac.FechaE)) BETWEEN ? AND ? AND saprod.marca LIKE ? AND
                SAITEMFAC.codvend = ? AND saitemfac.tipofac = 'A' AND SAFACT.tipofac = 'A' AND SAFACT.NumeroD NOT IN
                (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac = 'A' AND x.NumeroR IS NOT NULL AND
                CAST(X.Monto AS BIGINT) = CAST((SELECT Z.Monto FROM SAFACT AS Z WHERE Z.NumeroD = x.NumeroR AND Z.TipoFac = 'B') AS BIGINT))
                
                UNION
                
                SELECT DISTINCT(CodClie) $cells_nota FROM saitemnota INNER JOIN saprod ON saitemnota.coditem = saprod.codprod INNER JOIN
                sanota ON saitemnota.NumeroD = sanota.NumeroD WHERE
                DATEADD(dd, 0, DATEDIFF(dd, 0, saitemnota.FechaE)) BETWEEN ? AND ? AND saprod.marca LIKE ? AND
                saitemnota.codvend = ? AND saitemnota.tipofac = 'C' AND sanota.tipofac = 'C' AND numerof = '0' AND sanota.NumeroD NOT IN
                (SELECT X.NumeroD FROM sanota AS X WHERE X.TipoFac = 'C' AND x.Numerof IS NOT NULL AND
                CAST(X.subtotal AS BIGINT) = CAST((SELECT Z.subtotal FROM sanota AS Z WHERE Z.NumeroD = x.Numerof AND Z.TipoFac = 'D') AS BIGINT))";

        $result = (new Conectar())->conexion2()->prepare($sql);
        $result->bindValue($i += 1, $fechai);
        $result->bindValue($i += 1, $fechaf);
        $result->bindValue($i += 1, $marca);
        $result->bindValue($i += 1, $ruta);

        $result->bindValue($i += 1, $fechai);
        $result->bindValue($i += 1, $fechaf);
        $result->bindValue($i += 1, $marca);
        $result->bindValue($i += 1, $ruta);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}
