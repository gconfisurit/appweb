<?php
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class VentasKgEdvPorCategoria extends Conectar{

    public function getinstancias($data){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        switch ($data['instancia']) {
            case '-' : $insta = ""; break;
            case '79': $insta = " where inspadre = ? "; break;
            default  : $insta = " where codinst =  ? ";
        }

        //QUERY
        $sql = "select codinst, descrip from sainsta $insta order by descrip";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        if ($data['instancia']!='-')
            $sql->bindValue(1, $data['instancia']);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNotaDebitos($data, $instancia){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        if ($data['vendedor'] != "-") {
            $sql = "SELECT saprod.tara AS peso, saitemfac.esunid AS unidad, saprod.cantempaq AS paquetes, saitemfac.cantidad AS cantidad, saitemfac.totalitem AS monto, saitemfac.tipofac AS tipo 
                    FROM saitemfac 
                        INNER JOIN saprod ON saitemfac.coditem = saprod.codprod 
                        INNER JOIN sainsta ON saprod.codinst = sainsta.codinst 
                        INNER JOIN safact ON saitemfac.numerod = safact.numerod 
                    WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, saitemfac.FechaE)) BETWEEN ? AND ? AND saprod.codinst = ? 
                      AND safact.codvend = ? AND (saitemfac.tipofac = 'A' OR saitemfac.tipofac = 'B') 
                    ORDER BY sainsta.descrip, saitemfac.tipofac";
        } else {
            $sql = "SELECT saprod.tara AS peso, saitemfac.esunid AS unidad, saprod.cantempaq AS paquetes, saitemfac.cantidad AS cantidad, saitemfac.totalitem AS monto, saitemfac.tipofac AS tipo
                    FROM saitemfac
                        INNER JOIN saprod ON saitemfac.coditem = saprod.codprod
                        INNER JOIN sainsta ON saprod.codinst = sainsta.codinst
                    WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, saitemfac.FechaE)) BETWEEN ? AND ? 
                      AND saprod.codinst = ? AND (saitemfac.tipofac = 'A' OR saitemfac.tipofac = 'B')
                    ORDER BY sainsta.descrip, saitemfac.tipofac";
        }

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $data['fechai']);
        $sql->bindValue(2, $data['fechaf']);
        $sql->bindValue(3, $instancia);
        if ($data['vendedor'] != "-")
            $sql->bindValue(4, $data['vendedor']);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
