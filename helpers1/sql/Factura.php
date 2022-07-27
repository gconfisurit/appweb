<?php


class Factura extends Conectar {

    public static function getById($numerod)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT numerod, tipofac, fechae, codusua, codvend, descrip, Tasa AS tasa,
                       monto AS subtotal,  TExento AS excento, MtoTax AS impuesto,
                       TGravable AS base_imponible, Descto1 AS descuento, MtoTotal as total
                FROM safact WHERE numerod = ?";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue(1, $numerod);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function getHeaderById($numerod)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT numerod, fechae, codusua, codvend, descrip, Tasa AS tasa,
                       monto AS subtotal, Descto1 AS descuento,
                       TExento AS excento, MtoTax AS impuesto,
                       COALESCE((MtoTax*100) / NULLIF(TGravable, 0), 0) AS iva,
                       TGravable AS base_imponible, MtoTotal as total
                FROM safact WHERE numerod = ?";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue(1, $numerod);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getDetailById($numerod, $tipo)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT nrolinea, tipofac, coditem, codvend, descrip1 AS descrip, cantidad, esunid,
                    precio, descto AS descuento, totalitem as total, COALESCE(tasai, 0) AS tasa,
                    (COALESCE(CASE WHEN esunid = 0 THEN (saprod.tara) ELSE (saprod.tara / saprod.cantempaq) END, 0) * saitemfac.cantidad) AS peso,
                    (CASE WHEN esunid = 0 THEN 'BUL' ELSE 'PAQ' END) AS tipo
                FROM saitemfac INNER JOIN saprod ON codprod = saitemfac.coditem
                WHERE numerod = ? AND tipofac = ? ORDER BY saitemfac.nrolinea";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue(1, $numerod);
        $result->bindValue(2, $tipo);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getInvoiceReturns($fechai, $fechaf, $alm=array())
    {
        $i=0;
        $cond = $depo = "";
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        if (count($alm) > 0) {
            $aux = "";
            //se contruye un string para listar los depositvos seleccionados
            //en caso que no haya ninguno, sera vacio
            foreach ($alm as $num)
                $aux .= " OR CodUbic = ?";

            //armamos una lista de los depositos, si no existe ninguno seleccionado no se considera para realizar la consulta
            $depo = "(" . substr($aux, 4, strlen($aux)) . ")";

            $cond = ($depo != "()")
                ? ("AND ".$depo)
                : "";
        }

        $sql= "SELECT CodItem AS coditem, Cantidad AS cantidad, esunid AS esunid FROM SAITEMFAC WHERE NumeroD IN (SELECT fa.NumeroR FROM SAFACT AS fa WHERE TipoFac= 'A' AND NumeroR IS NOT NULL " . $cond . " AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ?
               AND (NumeroD IN (SELECT x.NumeroR FROM SAFACT AS x WHERE x.TipoFac = 'B' AND x.NumeroR=fa.NumeroD AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? GROUP BY x.NumeroR HAVING CAST(SUM(x.Monto) AS INT) < CAST(fa.Monto AS INT)))
               AND NumeroD NOT IN (SELECT Despachos_Det.Numerod FROM APPWEBAJ.dbo.Despachos_Det) AND NumeroD NOT IN (SELECT numerof FROM sanota)) AND tipofac = 'B'";

        $result = (new Conectar)->conexion2()->prepare($sql);

        if ($depo != "()")
            foreach ($alm AS $num)
                $result->bindValue($i+=1, $num);
        $result->bindValue($i+=1, $fechai);
        $result->bindValue($i+=1, $fechaf);
        $result->bindValue($i+=1, $fechai);
        $result->bindValue($i+=1, $fechaf);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}