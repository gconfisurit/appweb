
<?php
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Tabladinamica extends Conectar{

    public function getTabladinamicaFactura($data)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

          $marca = (!hash_equals('-', $data['marca'])) ? " AND saprod.marca LIKE ?" : "";
        $edv = (!hash_equals('-', $data['edv'])) ? " AND  sanota.codvend LIKE ?" : "";

         $condicion1="";
         $condicion2="";

        if($data['edv']=='-'){
            $condicion1=" AND saprod.marca LIKE ?";
         }

         if($data['marca']=='-'){
            $condicion2=" AND  SAITEMFAC.codvend LIKE ?";
         }

       /* echo "<script>console.log('fechai: " . $data['fechai'] . "' );</script>";
        echo "<script>console.log('fechaf: " . $data['fechaf'] . "' );</script>";
        echo "<script>console.log('marca: " . $data['marca'] . "' );</script>";
        echo "<script>console.log('edv: " . $data['edv'] . "' );</script>";*/

        //QUERY
        $sql= "SELECT
                (SELECT codvend FROM savend WHERE savend.codvend = SAITEMFAC.codvend) AS codvend,
                (SELECT descrip FROM savend WHERE savend.codvend = SAITEMFAC.codvend) AS vendedor,
                (SELECT clase FROM savend WHERE savend.codvend = SAITEMFAC.codvend) AS clasevend,
                SAITEMFAC.TipoFac AS tipo,
                SAITEMFAC.numerod AS numerod,
                (SELECT codclie FROM SAFACT WHERE SAFACT.numerod = SAITEMFAC.numerod AND SAFACT.tipofac = SAITEMFAC.tipofac) AS codclie,
                (SELECT Descrip as ID3 FROM SAFACT WHERE SAFACT.numerod = SAITEMFAC.numerod AND SAFACT.tipofac = SAITEMFAC.tipofac) AS cliente,
                (SELECT saclie_01.codnestle FROM SAFACT INNER JOIN saclie_01 ON SAFACT.codclie = saclie_01.codclie WHERE SAFACT.numerod = SAITEMFAC.numerod AND SAFACT.tipofac = SAITEMFAC.tipofac) AS codnestle,
                (SELECT saclie_01.clasificacion FROM SAFACT INNER JOIN saclie_01 ON SAFACT.codclie = saclie_01.codclie WHERE SAFACT.numerod = SAITEMFAC.numerod AND SAFACT.tipofac = SAITEMFAC.tipofac) AS clasificacion,
                SAITEMFAC.coditem,
                SAITEMFAC.Descrip1 as descripcion,
                (SELECT marca FROM SAPROD WHERE SAITEMFAC.coditem = SAPROD.CodProd) AS marca,
                SAITEMFAC.cantidad,
                (CASE SAITEMFAC.EsUnid WHEN 1 then 'PAQ' ELSE 'BULT' END) AS unid,
                (CASE SAITEMFAC.EsUnid WHEN 1 then cantidad ELSE cantidad*cantempaq END) AS paq,
                (CASE SAITEMFAC.EsUnid WHEN 1 then cantidad/cantempaq ELSE cantidad END) AS bul,
                (SELECT descrip FROM sainsta WHERE sainsta.codinst = saprod.codinst) AS instancia,
                --(CASE SAITEMFAC.esexento WHEN 1  then SAITEMFAC.TotalItem ELSE SAITEMFAC.TotalItem / 1.16 END) AS montod,
                --SAITEMNOTA.total AS montod,
                --(CASE SAITEMFAC.esexento WHEN 1  then SAITEMFAC.Descto ELSE SAITEMFAC.Descto / 1.16 END) AS descuento,
                --SAITEMNOTA.descuento AS descuento,
                SAITEMFAC.TotalItem as montod,
                SAITEMFAC.Descto as descuento,
                (SELECT tasa FROM SAFACT WHERE SAFACT.numerod = SAITEMFAC.numerod AND SAFACT.tipofac = SAITEMFAC.tipofac) AS factor,
                SAITEMFAC.fechae,
                SAITEMFAC.esexento,
                (CASE SAITEMFAC.EsUnid WHEN 1  then (cantidad/cantempaq)*saprod.tara ELSE cantidad*saprod.tara END) AS kg,
                CONCAT('(',MONTH(SAITEMFAC.fechae),')','-',UPPER(DATENAME(MONTH,SAITEMFAC.fechae)),'-(',YEAR(SAITEMFAC.fechae),')') MES
                 FROM SAITEMFAC INNER JOIN saprod ON SAITEMFAC.coditem = saprod.codprod
                 INNER JOIN SAFACT ON SAITEMFAC.numerod = SAFACT.numerod AND SAITEMFAC.tipofac = SAFACT.tipofac WHERE
                 DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMFAC.FechaE)) between ? AND ?  $marca $edv AND (SAITEMFAC.tipofac = 'A' OR SAITEMFAC.Tipofac = 'B') ORDER BY SAITEMFAC.fechae asc";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$data['fechai']);
        $sql->bindValue($i+=1,$data['fechaf']);
        if (!hash_equals('-', $data['marca']))
            $sql->bindValue($i+=1,$data['marca']);
        if (!hash_equals('-', $data['edv']))
            $sql->bindValue($i+=1,$data['edv']);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTabladinamicaNotaDeEntrega($data)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $marca = (!hash_equals('-', $data['marca'])) ? " AND saprod.marca LIKE ?" : "";
        $edv = (!hash_equals('-', $data['edv'])) ? " AND  sanota.codvend LIKE ?" : "";

        //QUERY
        $sql= "SELECT
                (SELECT codvend FROM savend WHERE savend.codvend = SAITEMNOTA.codvend) AS codvend,
                (SELECT descrip FROM savend WHERE savend.codvend = SAITEMNOTA.codvend) AS vendedor,
                (SELECT clase FROM savend WHERE savend.codvend = SAITEMNOTA.codvend) AS clasevend,
                saitemnota.tipofac AS tipo,
                saitemnota.numerod AS numerod,
                (SELECT codclie FROM SANOTA WHERE SANOTA.numerod = SAITEMNOTA.numerod AND SANOTA.tipofac = SAITEMNOTA.tipofac) AS codclie,
                (SELECT rsocial FROM SANOTA WHERE SANOTA.numerod = SAITEMNOTA.numerod AND SANOTA.tipofac = SAITEMNOTA.tipofac) AS cliente,
                (SELECT saclie_01.codnestle FROM SANOTA INNER JOIN saclie_01 ON SANOTA.codclie = saclie_01.codclie WHERE SANOTA.numerod = SAITEMNOTA.numerod AND SANOTA.tipofac = SAITEMNOTA.tipofac) AS codnestle,
                (SELECT saclie_01.clasificacion FROM SANOTA INNER JOIN saclie_01 ON SANOTA.codclie = saclie_01.codclie WHERE SANOTA.numerod = SAITEMNOTA.numerod AND SANOTA.tipofac = SAITEMNOTA.tipofac) AS clasificacion,
                SAITEMNOTA.coditem,
                SAITEMNOTA.descripcion,
                (SELECT marca FROM SAPROD WHERE SAITEMNOTA.coditem = SAPROD.CodProd) AS marca,
                SAITEMNOTA.cantidad,
                (CASE SAITEMNOTA.esunidad WHEN 1 then 'PAQ' ELSE 'BULT' END) AS unid,
                (CASE SAITEMNOTA.esunidad WHEN 1 then cantidad ELSE cantidad*cantempaq END) AS paq,
                (CASE SAITEMNOTA.esunidad WHEN 1 then cantidad/cantempaq ELSE cantidad END) AS bul,
                (SELECT descrip FROM sainsta WHERE sainsta.codinst = saprod.codinst) AS instancia,
                (CASE SAITEMNOTA.esexento WHEN 1  then SAITEMNOTA.total ELSE SAITEMNOTA.total / 1.16 END) AS montod,
                --SAITEMNOTA.total AS montod,
                (CASE SAITEMNOTA.esexento WHEN 1  then SAITEMNOTA.descuento ELSE SAITEMNOTA.descuento / 1.16 END) AS descuento,
                --SAITEMNOTA.descuento AS descuento,
                (SELECT tasa FROM SAFACT WHERE SAFACT.numerod = SAITEMNOTA.numerod AND SAFACT.tipofac = SAITEMNOTA.tipofac) AS factor,
                SAITEMNOTA.fechae,
                SAITEMNOTA.esexento,
                (CASE SAITEMNOTA.esunidad WHEN 1  then (cantidad/cantempaq)*saprod.tara ELSE cantidad*saprod.tara END) AS kg,
                CONCAT('(',MONTH(saitemnota.fechae),')','-',UPPER(DATENAME(MONTH,saitemnota.fechae)),'-(',YEAR(saitemnota.fechae),')') MES
                 FROM SAITEMNOTA INNER JOIN saprod ON SAITEMNOTA.coditem = saprod.codprod
                 INNER JOIN sanota ON saitemnota.numerod = sanota.numerod AND saitemnota.tipofac = sanota.tipofac WHERE
                 DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMNOTA.FechaE)) between ? AND ? $marca $edv  AND (SAITEMNOTA.tipofac = 'C' OR SAITEMNOTA.Tipofac = 'D') AND  
                SANOTA.numerof =(SELECT numerof FROM sanota WHERE sanota.numerod = SAITEMNOTA.numerod AND sanota.tipofac = SAITEMNOTA.tipofac AND sanota.numerof = 0) ORDER BY SAITEMNOTA.fechae";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$data['fechai']);
        $sql->bindValue($i+=1,$data['fechaf']);
        if (!hash_equals('-', $data['marca']))
            $sql->bindValue($i+=1,$data['marca']);
        if (!hash_equals('-', $data['edv']))
            $sql->bindValue($i+=1,$data['edv']);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalNotaDeEntrega($data, $tipo)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $edv = (!hash_equals('-', $data['edv'])) ? " AND saitemnota.codvend = ?" : "";

        //QUERY
        $sql= "SELECT sum((CASE SAITEMNOTA.esexento WHEN 1  THEN SAITEMNOTA.total ELSE SAITEMNOTA.total / 1.16 END)) montod
                FROM SAITEMNOTA INNER JOIN saprod ON SAITEMNOTA.coditem = saprod.codprod
                INNER JOIN sanota ON saitemnota.numerod = sanota.numerod AND saitemnota.tipofac = sanota.tipofac
                WHERE
                DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMNOTA.FechaE)) BETWEEN ? AND ? $edv AND (SAITEMNOTA.tipofac = ?) AND  
                SANOTA.numerof = (SELECT numerof FROM sanota WHERE sanota.numerod = SAITEMNOTA.numerod AND sanota.tipofac = SAITEMNOTA.tipofac AND sanota.numerof = 0)";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$data['fechai']);
        $sql->bindValue($i+=1,$data['fechaf']);
        if (!hash_equals('-', $data['edv']))
            $sql->bindValue($i+=1,$data['edv']);
        $sql->bindValue($i+=1,$tipo);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getResumenFactura($data)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $edv = (!hash_equals('-', $data['edv'])) ? " AND codvend LIKE ?" : "";

        //QUERY
        $sql= "SELECT codvend, descto1, descto2, tasa, numerod, tipofac, fechae, codclie, descrip
                FROM SAFACT WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ?
                AND (tipofac = 'A' OR Tipofac = 'B')
                AND (Descto1 > 0 OR Descto2 > 0) $edv";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$data['fechai']);
        $sql->bindValue($i+=1,$data['fechaf']);
        if (!hash_equals('-', $data['edv']))
            $sql->bindValue($i+=1,$data['edv']);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getResumenNotaDeEntrega($data)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $edv = (!hash_equals('-', $data['edv'])) ? " AND sanota.codvend LIKE ?" : "";

        //QUERY
        $sql= "SELECT 
                sanota.codvend, 
                sanota.descuento,
                (SELECT tasa FROM SAFACT WHERE SAFACT.numerod = sanota.numerod AND SAFACT.tipofac = sanota.tipofac AND (sanota.tipofac = 'C' OR sanota.Tipofac = 'D')) 
                    AS tasa,
                sanota.numerod, 
                sanota.tipofac,
                sanota.fechae,
                sanota.codclie,
                sanota.rsocial AS descrip
                FROM sanota INNER JOIN SAFACT ON safact.numerod = sanota.numerod WHERE
                DATEADD(dd, 0, DATEDIFF(dd, 0, sanota.FechaE)) BETWEEN ? AND ? $edv
                AND (sanota.tipofac = 'C' OR sanota.Tipofac = 'D')
                AND sanota.descuento > 0";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1,$data['fechai']);
        $sql->bindValue($i+=1,$data['fechaf']);
        if (!hash_equals('-', $data['edv']))
            $sql->bindValue($i+=1,$data['edv']);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}

