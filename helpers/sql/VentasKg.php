<?php

class VentasKg extends Conectar {

    public static function getNumerodOfDiscounts($datei, $datef, $codinst)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT DISTINCT numerod
                FROM saitemfac
                    INNER JOIN saprod  ON saitemfac.coditem = saprod.codprod
                    INNER JOIN sainsta ON saprod.codinst = sainsta.codinst
                WHERE DATEADD(DD, 0, DATEDIFF(DD, 0, saitemfac.FechaE)) BETWEEN ? AND ?
                  AND saprod.codinst = ? AND (saitemfac.tipofac = 'A' OR saitemfac.tipofac = 'B')";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue(1,$datei);
        $result->bindValue(2,$datef);
        $result->bindValue(3,$codinst);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}