<?php


class KpiLogro extends Conectar {

    public static function Kg_fact($ruta, $fechai, $fechaf, $tipoFac)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT SUM(CASE saitemfac.Esunid WHEN 1 THEN (cantidad/cantempaq)*saprod.tara ELSE cantidad*saprod.tara END) AS kg
			 FROM saitemfac INNER JOIN saprod ON saitemfac.coditem = saprod.codprod WHERE 
			 DATEADD(dd, 0, DATEDIFF(dd, 0, saitemfac.FechaE)) BETWEEN ? AND ? AND codvend = ? AND (tipofac = ?)";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue($i+=1, $fechai);
        $result->bindValue($i+=1, $fechaf);
        $result->bindValue($i+=1, $ruta);
        $result->bindValue($i+=1, $tipoFac);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function Kg_nota($ruta, $fechai, $fechaf, $tipoFac)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT SUM(CASE saitemnota.esunidad WHEN 1 THEN (cantidad/cantempaq)*saprod.tara ELSE cantidad*saprod.tara END) AS kg
			 FROM saitemnota INNER JOIN saprod ON saitemnota.coditem = saprod.codprod WHERE 
			 DATEADD(dd, 0, DATEDIFF(dd, 0, saitemnota.FechaE)) BETWEEN ? AND ? AND codvend = ? AND (tipofac = ?)";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue($i+=1, $fechai);
        $result->bindValue($i+=1, $fechaf);
        $result->bindValue($i+=1, $ruta);
        $result->bindValue($i+=1, $tipoFac);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function Unid_fact($ruta, $fechai, $fechaf, $tipoFac)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT SUM((CASE saitemfac.Esunid WHEN 1 THEN cantidad ELSE cantidad*cantempaq END)) AS paq
			 FROM saitemfac INNER JOIN saprod ON saitemfac.coditem = saprod.codprod WHERE 
			 DATEADD(dd, 0, DATEDIFF(dd, 0, saitemfac.FechaE)) BETWEEN ? AND ? AND codvend = ? AND (tipofac = ?)";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue($i+=1, $fechai);
        $result->bindValue($i+=1, $fechaf);
        $result->bindValue($i+=1, $ruta);
        $result->bindValue($i+=1, $tipoFac);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function Unid_nota($ruta, $fechai, $fechaf, $tipoFac)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT SUM((CASE saitemnota.esunidad WHEN 1 THEN cantidad ELSE cantidad*cantempaq END)) AS paq
                FROM saitemnota INNER JOIN saprod ON saitemnota.coditem = saprod.codprod WHERE 
                DATEADD(dd, 0, DATEDIFF(dd, 0, saitemnota.FechaE)) BETWEEN ? AND ? AND codvend = ? AND (tipofac = ?)";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue($i+=1, $fechai);
        $result->bindValue($i+=1, $fechaf);
        $result->bindValue($i+=1, $ruta);
        $result->bindValue($i+=1, $tipoFac);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function Bul_fact($ruta, $fechai, $fechaf, $tipoFac)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT sum((CASE saitemfac.Esunid WHEN 1 THEN cantidad/cantempaq ELSE cantidad END)) AS bul
			 FROM saitemfac INNER JOIN saprod ON saitemfac.coditem = saprod.codprod WHERE 
			 DATEADD(dd, 0, DATEDIFF(dd, 0, saitemfac.FechaE)) BETWEEN ? AND ? AND codvend = ? AND (tipofac = ?)";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue($i+=1, $fechai);
        $result->bindValue($i+=1, $fechaf);
        $result->bindValue($i+=1, $ruta);
        $result->bindValue($i+=1, $tipoFac);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function Bul_nota($ruta, $fechai, $fechaf, $tipoFac)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT sum((CASE saitemnota.esunidad WHEN 1 THEN cantidad/cantempaq ELSE cantidad END)) AS bul
			 FROM saitemnota INNER JOIN saprod ON saitemnota.coditem = saprod.codprod WHERE 
			 DATEADD(dd, 0, DATEDIFF(dd, 0, saitemnota.FechaE)) BETWEEN ? AND ? AND codvend = ? AND (tipofac = ?)";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue($i+=1, $fechai);
        $result->bindValue($i+=1, $fechaf);
        $result->bindValue($i+=1, $ruta);
        $result->bindValue($i+=1, $tipoFac);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}