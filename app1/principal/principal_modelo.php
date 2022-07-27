<?php
ini_set('memory_limit', '-1');
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Principal extends Conectar{

    public function getDocumentosSinDespachar(){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

$ffin = date('Y-m-d');
$dato = explode("-", $ffin); //Hasta
$aniod=$dato[0]; //año
$mesd=$dato[1]; //mes
$diad="01"; //dia
$fini=$aniod."-01-01";
$t=0;

$sql="SELECT NumeroD, Descrip, TipoFac from safact AS SA where DATEADD(dd, 0, DATEDIFF(dd, 0, SA.FechaE))
                        between '$fini' and '$ffin' and SA.TipoFac in ('A','C') and
                        (SA.NumeroR is null or SA.NumeroR in (select x.NumeroD from SAFACT as x where cast(x.Monto as int)<cast(SA.Monto as int) and X.TipoFac  in ('d','b')
                        and x.NumeroD=SA.NumeroR)) and SA.NumeroD not in (SELECT numeros FROM appfacturas_det) and SA.NumeroD not in (SELECT numerof FROM sanota) order by SA.NumeroD";

/*
$sql=("SELECT fa.descrip, fa.numerod, fa.NumeroR from SAFACT as fa where TipoFac='A' and CodUbic='01' and DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) between '$fini' and '$ffin'
and (NumeroR is null or NumeroD in (select x.NumeroR from SAFACT as x where x.TipoFac = 'B' and x.NumeroR=fa.NumeroD and DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) between '$fini' and '$ffin' group by x.NumeroR having cast(sum(x.Monto) as int)<cast(fa.Monto as int)))
 and NumeroD not in (select numeros from appfacturas_det) order by FechaE asc");*/

        //QUERY
       /* $sql = "SELECT *
                FROM safact AS SA INNER JOIN SAVEND AS VEND ON VEND.CodVend = SA.CodVend
                WHERE SA.NumeroD NOT IN (SELECT Despachos_Det.Numerod FROM APPWEBAJ.dbo.Despachos_Det)
                  AND SA.TipoFac = 'A'
                  AND (SA.NumeroR IS NULL OR SA.NumeroR IN (SELECT x.NumeroD FROM SAFACT AS x WHERE cast(x.Monto AS INT)<cast(SA.Monto AS INT) AND X.TipoFac = 'B'
                  AND x.NumeroD=SA.NumeroR))  AND SA.NumeroD NOT IN (SELECT numerof FROM sanota) ORDER BY SA.NumeroD";*/

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPedidosSinFacturar(){

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
                WHERE  (SF.NumeroD = SI.NumeroD AND SF.TipoFac = SI.TipoFac)
                  AND SC.CodClie = SF.CodClie AND SI.NroLineaC = 0
                  AND SF.TipoFac = 'F' AND sf.Monto <> 0 AND se.CodUbic = 01
                ORDER BY SF.FechaE DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_cxc_bs(){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT SUM(SAACXC.Saldo) as saldo_bs
                FROM saacxc INNER JOIN saclie ON saacxc.codclie = saclie.codclie
                WHERE saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20')";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function get_cxc_bs_dolar(){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT (saacxc.saldo/SAFACT.Tasa) as SaldoPendolar,
                (select Coordinador from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
                from saacxc inner join saclie on saacxc.codclie = saclie.codclie inner join SAFACT on SAFACT.NumeroD= SAACXC.NumeroD 
                where saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') 
                order by saacxc.FechaE asc";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_cxc_dolares(){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
       /* $sql = "SELECT SUM(total-abono) as saldo_dolares
                FROM SANOTA
                WHERE tipofac ='C' AND estatus in (0, 1)";*/
        
        $sql = "SELECT 
          SUM(SAACXC.Saldo) as saldo_dolares
           from aj_d.dbo.saacxc inner join aj_d.dbo.saclie on saacxc.codclie = saclie.codclie 
           where saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20')" ;

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

/////////////////////////  VENTAS ///////////////////////////////////////////////

    public function get_ventas_por_mes_fact($fechai, $fechaf) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

   //SUM(COALESCE((TotalItem/NULLIF(Tasai,0)) * (CASE WHEN itemfact.TipoFac = 'A' THEN 1 ELSE -1 END), 0)) as total

        //QUERY
        /*$sql = "SELECT YEAR(CAST(itemfact.FechaE AS DATETIME)) anio, MONTH(CAST(itemfact.FechaE AS DATETIME)) mes,
        CONVERT(int, CAST(sum(case when fact.TipoFac = 'A' then monto/tasa end) AS money), 1) as total 
                FROM SAFACT fact
                         INNER JOIN SAITEMFAC itemfact ON itemfact.NumeroD = fact.NumeroD
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, fact.FechaE)) BETWEEN ? AND ? AND itemfact.tipofac IN ('A','B')
                  AND fact.NumeroD NOT IN (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac = 'A' AND x.NumeroR IS NOT NULL AND CAST(X.Monto AS BIGINT) = CAST((SELECT Z.Monto FROM SAFACT AS Z WHERE Z.NumeroD = x.NumeroR AND Z.TipoFac = 'B') AS BIGINT))
                GROUP BY YEAR(CAST(itemfact.FechaE AS DATETIME)), MONTH(CAST(itemfact.FechaE AS DATETIME))
                ORDER BY mes ASC";*/

        $sql ="SELECT YEAR(CAST(itemfact.FechaE AS DATETIME)) anio, MONTH(CAST(itemfact.FechaE AS DATETIME)) mes,
       CONVERT(int, CAST(SUM(COALESCE((TotalItem/NULLIF(Tasai,0)) * (CASE WHEN itemfact.TipoFac = 'A' THEN 1 ELSE -1 END), 0))AS money), 1) as total
                FROM SAFACT fact
                         INNER JOIN SAITEMFAC itemfact ON itemfact.NumeroD = fact.NumeroD
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, fact.FechaE)) BETWEEN ? AND ? AND fact.tipofac IN ('A','B')
                  AND fact.NumeroD NOT IN (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac = 'A' AND x.NumeroR IS NOT NULL AND CAST(X.Monto AS BIGINT) = CAST((SELECT Z.Monto FROM SAFACT AS Z WHERE Z.NumeroD = x.NumeroR AND Z.TipoFac = 'B') AS BIGINT))
                GROUP BY YEAR(CAST(itemfact.FechaE AS DATETIME)), MONTH(CAST(itemfact.FechaE AS DATETIME))
                ORDER BY mes ASC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_ventas_por_mes_nota($fechai, $fechaf) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        // $sql = "SUM(itemnota.total * (CASE WHEN itemnota.TipoFac = 'C' THEN 1 ELSE -1 END)) as total";

        //QUERY
       /* $sql = "SELECT YEAR(CAST(itemnota.FechaE AS DATETIME)) anio, MONTH(CAST(itemnota.FechaE AS DATETIME)) mes,
        CONVERT(int, CAST(sum(case when nota.TipoFac = 'C' then nota.total end) AS money), 1) as total
                FROM SANOTA nota
                    INNER JOIN SAITEMNOTA itemnota ON itemnota.numerod = nota.numerod
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, itemnota.FechaE)) BETWEEN ? AND ? AND nota.tipofac in ('C','D') AND numerof = '0'
                GROUP BY YEAR(CAST(itemnota.FechaE AS DATETIME)), MONTH(CAST(itemnota.FechaE AS DATETIME))
                ORDER BY mes ASC";*/

        $sql= "SELECT YEAR(CAST(nota.fechae AS DATETIME)) anio, MONTH(CAST(nota.fechae AS DATETIME)) mes,
				 CONVERT(int, CAST(sum(case when nota.TipoFac = 'C' then nota.total end) AS money), 1) as total
                FROM SANOTA nota
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, nota.fechae)) BETWEEN ? AND ? AND nota.tipofac in ('C','D')
                GROUP BY YEAR(CAST(nota.fechae AS DATETIME)), MONTH(CAST(nota.fechae AS DATETIME))
                ORDER BY mes ASC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

/////////////////////////  BULTOS ///////////////////////////////////////////////

     public function get_bultos_por_mes_fact($fechai, $fechaf) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT YEAR(CAST(itemfact.FechaE AS DATETIME)) anio, MONTH(CAST(itemfact.FechaE AS DATETIME)) mes,
                       SUM(cantidad) as total
                FROM SAFACT fact
                         INNER JOIN SAITEMFAC itemfact ON itemfact.NumeroD = fact.NumeroD
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, fact.FechaE)) BETWEEN ? AND ? AND itemfact.tipofac IN ('A','B')
                  AND fact.NumeroD NOT IN (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac = 'A' AND x.NumeroR IS NOT NULL AND CAST(X.Monto AS BIGINT) = CAST((SELECT Z.Monto FROM SAFACT AS Z WHERE Z.NumeroD = x.NumeroR AND Z.TipoFac = 'B') AS BIGINT))
                GROUP BY YEAR(CAST(itemfact.FechaE AS DATETIME)), MONTH(CAST(itemfact.FechaE AS DATETIME))
                ORDER BY mes ASC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_bultos_por_mes_nota($fechai, $fechaf) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT YEAR(CAST(itemnota.FechaE AS DATETIME)) anio, MONTH(CAST(itemnota.FechaE AS DATETIME)) mes,
                       SUM(cantidad) as total
                FROM SANOTA nota
                    INNER JOIN SAITEMNOTA itemnota ON itemnota.numerod = nota.numerod
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, itemnota.FechaE)) BETWEEN ? AND ? AND nota.tipofac in ('C','D') AND numerof = '0'
                GROUP BY YEAR(CAST(itemnota.FechaE AS DATETIME)), MONTH(CAST(itemnota.FechaE AS DATETIME))
                ORDER BY mes ASC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_inventario_valorizado($alm) {
        $i = 0;
        $cond = $depo = "";
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        if (count($alm) > 0) {
            $aux = "";
            //se contruye un string para listar los depositvos seleccionados
            //en caso que no haya ninguno, sera vacio
            foreach ($alm as $num)
                $aux .= " OR exis.codubic = ?";

            //armamos una lista de los depositos, si no existe ninguno seleccionado no se considera para realizar la consulta
            $depo = "(" . substr($aux, 4, strlen($aux)) . ")";

            $cond = ($depo != "()")
                ? ("AND ".$depo)
                : "";
        }

        //QUERY
        $sql = "SELECT depo.CodUbic AS almacen,  SUM(exis.Existen * prod02.Precio1_B) AS total_b, SUM(exis.exunidad * prod02.Precio1_P) AS total_p
                FROM SADEPO depo
                    INNER JOIN SAEXIS exis ON depo.CodUbic = exis.CodUbic
                    INNER JOIN SAPROD prod ON exis.CodProd = prod.CodProd
                    INNER JOIN SAPROD_02 prod02 ON exis.CodProd = prod02.CodProd
                WHERE (exis.existen > 0 OR exis.exunidad > 0) AND len(prod.marca) > 0 $cond
                GROUP BY depo.CodUbic ORDER BY depo.CodUbic ASC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        if ($depo != "()") {
            foreach ($alm AS $num)
                $sql->bindValue($i+=1, $num);
        }
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


     public function get_detalle_almacen($alm) {
        $i = 0;
        $cond = $depo = "";
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

       /* if (count($alm) > 0) {
            $aux = "";
            //se contruye un string para listar los depositvos seleccionados
            //en caso que no haya ninguno, sera vacio
            foreach ($alm as $num)
                $aux .= " OR exis.codubic = ?";

            //armamos una lista de los depositos, si no existe ninguno seleccionado no se considera para realizar la consulta
            $depo = "(" . substr($aux, 4, strlen($aux)) . ")";

            $cond = ($depo != "()")
                ? ("AND ".$depo)
                : "";
        }**/

        //QUERY
        $sql = "SELECT depo.CodUbic AS almacen, SAINSTA.Descrip AS instancia, SUM(exis.Existen) AS cantidad_b, SUM(exis.exunidad) AS cantidad_p, SUM(exis.Existen * prod02.Precio1_B) AS total_b, SUM(exis.exunidad * prod02.Precio1_P) AS total_p
                FROM SADEPO depo
                    INNER JOIN SAEXIS exis ON depo.CodUbic = exis.CodUbic
                    INNER JOIN SAPROD prod ON exis.CodProd = prod.CodProd
                    INNER JOIN SAPROD_02 prod02 ON exis.CodProd = prod02.CodProd
					inner join SAINSTA on SAINSTA.CodInst = prod.codinst
                WHERE (exis.existen > 0 OR exis.exunidad > 0) AND len(prod.marca) > 0 and depo.codubic  = '$alm'
                GROUP BY depo.CodUbic, SAINSTA.Descrip ORDER BY depo.CodUbic ASC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }






    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_clientes_por_tipo($tipo = 0){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT codclie, descrip, id3, codvend, fechae, tipoid3, activo FROM SACLIE WHERE activo ='1' AND TipoID3 = ? AND codvend NOT IN ('99')";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tipo);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_tasa_dolar(){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT TOP(1) FechaE AS fechae, Tasa AS tasa FROM SACOMP WHERE Tasa IS NOT NULL ORDER BY FechaE DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function get_devoluciones_sin_motivo_Factura($tipodespacho) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $parameter = hash_equals('1', $tipodespacho)
            ? ', saclie.fechae as fecha_ini_clie, desp.numerod, ID_Correlativo as correl,notas1, notas2, observacion as motivo'
            : '';

        $relation = hash_equals('1', $tipodespacho)
            ? ' inner join APPWEBAJ.dbo.Despachos_Det desp on desp.numerod = NumeroR '
            : '';

        $condition = hash_equals('0', $tipodespacho)
            ? " AND (numerod NOT IN (SELECT numerod FROM APPWEBAJ.dbo.Despachos_Det) AND NumeroR NOT IN (SELECT numerod FROM APPWEBAJ.dbo.Despachos_Det)) "
            : " AND (observacion IS NULL OR observacion = '') ";

        //QUERY
        $sql = "SELECT safact.codvend AS code_vendedor, safact.tipofac, safact.numerod, numeror, safact.fechae AS fecha_fact,
                       safact.codclie AS cod_clie, safact.descrip AS cliente, monto $parameter
                FROM SAFACT
                        INNER JOIN saclie ON safact.codclie = saclie.codclie
                        $relation
                WHERE safact.TipoFac = 'B' $condition
                ORDER BY fecha_fact DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);

    }

    public function get_devoluciones_sin_motivo_NotadeEntrega($tipodespacho) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $parameter = hash_equals('1', $tipodespacho)
            ? ', saclie.fechae AS fecha_ini_clie, nt.observacion AS motivo, ID_Correlativo, desp.numerod, notas1'
            : '';

        $relation = hash_equals('1', $tipodespacho)
            ? ' INNER JOIN APPWEBAJ.dbo.Despachos_Det AS desp ON desp.numerod = numerof'
            : '';

        $condition = hash_equals('0', $tipodespacho)
            ? " AND (numerod NOT IN (SELECT numerod FROM APPWEBAJ.dbo.Despachos_Det) AND numerof NOT IN (select numerod FROM APPWEBAJ.dbo.Despachos_Det))"
            : " AND (nt.observacion IS NULL or nt.observacion = '')";

        //QUERY
        $sql = "SELECT nt.codvend AS code_vendedor, nt.tipofac, numerof AS numeror, nt.numerod, nt.fechae AS fecha_fact, nt.codclie AS cod_clie,
                       nt.rsocial AS cliente, total AS monto $parameter
                FROM SANOTA nt
                         INNER JOIN saclie ON nt.codclie = saclie.codclie
                         $relation
                WHERE nt.TipoFac = 'D' $condition
                ORDER BY fecha_fact DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }




  public function get_ventas_por_productos_fact($fechai, $fechaf){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT prod.Descrip as marca,
                       SUM(COALESCE((TotalItem/NULLIF(Tasai,0)) * (CASE WHEN itemfact.TipoFac = 'A' THEN 1 ELSE -1 END), 0)) as montod
                FROM SAFACT fact
                    INNER JOIN SAITEMFAC itemfact ON itemfact.NumeroD = fact.NumeroD
                    INNER JOIN SAPROD prod ON prod.CodProd = itemfact.CodItem
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, fact.FechaE)) BETWEEN ? AND ? AND itemfact.tipofac IN ('A','B')
                  AND fact.NumeroD NOT IN (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac = 'A' AND x.NumeroR IS NOT NULL AND CAST(X.Monto AS BIGINT) = CAST((SELECT Z.Monto FROM SAFACT AS Z WHERE Z.NumeroD = x.NumeroR AND Z.TipoFac = 'B') AS BIGINT))
                GROUP BY prod.Descrip
                ORDER BY montod DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $fechai);
        $sql->bindValue(2, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }



      public function get_ventas_por_productos_nota($fechai, $fechaf){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        
                $sql = "SELECT prod.Descrip as marca,
                       SUM(COALESCE(itemnota.total * (CASE WHEN itemnota.TipoFac = 'C' THEN 1 ELSE -1 END), 0)) AS montod
                FROM SANOTA nota
                    INNER JOIN SAITEMNOTA itemnota ON itemnota.numerod = nota.numerod
                    INNER JOIN SAPROD prod ON prod.CodProd = itemnota.coditem
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, itemnota.FechaE)) BETWEEN ? AND ? AND nota.tipofac IN ('C','D') AND numerof = '0'
                GROUP BY prod.Descrip
                ORDER BY montod DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $fechai);
        $sql->bindValue(2, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }







    public function get_ventas_por_marca_fact($fechai, $fechaf){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT marca,
                       SUM(COALESCE((TotalItem/NULLIF(Tasai,0)) * (CASE WHEN itemfact.TipoFac = 'A' THEN 1 ELSE -1 END), 0)) as montod
                FROM SAFACT fact
                    INNER JOIN SAITEMFAC itemfact ON itemfact.NumeroD = fact.NumeroD
                    INNER JOIN SAPROD prod ON prod.CodProd = itemfact.CodItem
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, fact.FechaE)) BETWEEN ? AND ? AND itemfact.tipofac IN ('A','B')
                  AND fact.NumeroD NOT IN (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac = 'A' AND x.NumeroR IS NOT NULL AND CAST(X.Monto AS BIGINT) = CAST((SELECT Z.Monto FROM SAFACT AS Z WHERE Z.NumeroD = x.NumeroR AND Z.TipoFac = 'B') AS BIGINT))
                GROUP BY marca
                ORDER BY montod DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $fechai);
        $sql->bindValue(2, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_ventas_por_marca_nota($fechai, $fechaf){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        
$sql = "SELECT marca,
                       SUM(COALESCE(itemnota.total * (CASE WHEN itemnota.TipoFac = 'C' THEN 1 ELSE -1 END), 0)) AS montod
                FROM SANOTA nota
                    INNER JOIN SAITEMNOTA itemnota ON itemnota.numerod = nota.numerod
                    INNER JOIN SAPROD prod ON prod.CodProd = itemnota.coditem
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, itemnota.FechaE)) BETWEEN ? AND ? AND nota.tipofac IN ('C','D') AND numerof = '0'
                GROUP BY marca
                ORDER BY montod DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $fechai);
        $sql->bindValue(2, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_ventas_clientes_fact($fechai, $fechaf){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT clie.codclie, clie.Descrip,
                       SUM(COALESCE((TotalItem/NULLIF(Tasai,0)) * (CASE WHEN itemfact.TipoFac = 'A' THEN 1 ELSE -1 END), 0)) as montod
                FROM SAFACT fact
                         INNER JOIN SAITEMFAC itemfact ON itemfact.NumeroD = fact.NumeroD
                         INNER JOIN SAPROD prod ON prod.CodProd = itemfact.CodItem
                         INNER JOIN SACLIE clie ON clie.CodClie = fact.codclie
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, fact.FechaE)) BETWEEN ? AND ? AND itemfact.tipofac IN ('A','B')
                  AND fact.NumeroD NOT IN (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac = 'A' AND x.NumeroR IS NOT NULL AND CAST(X.Monto AS BIGINT) = CAST((SELECT Z.Monto FROM SAFACT AS Z WHERE Z.NumeroD = x.NumeroR AND Z.TipoFac = 'B') AS BIGINT))
                GROUP BY clie.codclie, clie.Descrip
                ORDER BY montod DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $fechai);
        $sql->bindValue(2, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_ventas_cliente_nota($fechai, $fechaf){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT clie.codclie, clie.Descrip,
                       SUM(COALESCE(itemnota.total * (CASE WHEN itemnota.TipoFac = 'C' THEN 1 ELSE -1 END), 0)) AS montod
                FROM SANOTA nota
                         INNER JOIN SAITEMNOTA itemnota ON itemnota.numerod = nota.numerod
                         INNER JOIN SAPROD prod ON prod.CodProd = itemnota.coditem
                         INNER JOIN SACLIE clie ON clie.CodClie = nota.codclie
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, itemnota.FechaE)) BETWEEN ? AND ? AND nota.tipofac IN ('C','D') AND numerof = '0'
                GROUP BY clie.codclie, clie.Descrip
                ORDER BY montod DESC";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $fechai);
        $sql->bindValue(2, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_total_ventas($fechai, $fechaf){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        /*$sql = "SELECT SUM(montod) as montod FROM
                (
                    SELECT SUM(COALESCE(itemnota.total * (CASE WHEN itemnota.TipoFac = 'C' THEN 1 ELSE -1 END), 0)) AS montod
                    FROM SANOTA nota
                             INNER JOIN SAITEMNOTA itemnota ON itemnota.numerod = nota.numerod
                             INNER JOIN SAPROD prod ON prod.CodProd = itemnota.coditem
                    WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, itemnota.FechaE)) BETWEEN ? AND ? AND nota.tipofac IN ('C','D') AND numerof = '0'
                    UNION
                    SELECT SUM(COALESCE((TotalItem/NULLIF(Tasai,0)) * (CASE WHEN itemfact.TipoFac = 'A' THEN 1 ELSE -1 END), 0)) as montod
                    FROM SAFACT fact
                             INNER JOIN SAITEMFAC itemfact ON itemfact.NumeroD = fact.NumeroD
                             INNER JOIN SAPROD prod ON prod.CodProd = itemfact.CodItem
                    WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, fact.FechaE)) BETWEEN ? AND ? AND itemfact.tipofac IN ('A','B')
                      AND fact.NumeroD NOT IN (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac = 'A' AND x.NumeroR IS NOT NULL AND CAST(X.Monto AS BIGINT) = CAST((SELECT Z.Monto FROM SAFACT AS Z WHERE Z.NumeroD = x.NumeroR AND Z.TipoFac = 'B') AS BIGINT))
                ) AS ventas";*/

              $sql = "SELECT
CONVERT(int, CAST(sum(case when TipoFac = 'C' then total end) AS money), 1) Vendido
from SANOTA where TipoFac in ('C','D') and DATEADD(dd, 0, DATEDIFF(dd, 0, SANOTA.fechae)) BETWEEN  ? and ? ";



        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $fechai);
        $sql->bindValue(2, $fechaf);
       // $sql->bindValue(3, $fechai);
       // $sql->bindValue(4, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

     public function get_total_ventasbolivares($fechai, $fechaf){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        /*$sql = "SELECT SUM(montod) as montod FROM
                (
                    SELECT SUM(COALESCE(itemnota.total * (CASE WHEN itemnota.TipoFac = 'C' THEN 1 ELSE -1 END), 0)) AS montod
                    FROM SANOTA nota
                             INNER JOIN SAITEMNOTA itemnota ON itemnota.numerod = nota.numerod
                             INNER JOIN SAPROD prod ON prod.CodProd = itemnota.coditem
                    WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, itemnota.FechaE)) BETWEEN ? AND ? AND nota.tipofac IN ('C','D') AND numerof = '0'
                    UNION
                    SELECT SUM(COALESCE((TotalItem/NULLIF(Tasai,0)) * (CASE WHEN itemfact.TipoFac = 'A' THEN 1 ELSE -1 END), 0)) as montod
                    FROM SAFACT fact
                             INNER JOIN SAITEMFAC itemfact ON itemfact.NumeroD = fact.NumeroD
                             INNER JOIN SAPROD prod ON prod.CodProd = itemfact.CodItem
                    WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, fact.FechaE)) BETWEEN ? AND ? AND itemfact.tipofac IN ('A','B')
                      AND fact.NumeroD NOT IN (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac = 'A' AND x.NumeroR IS NOT NULL AND CAST(X.Monto AS BIGINT) = CAST((SELECT Z.Monto FROM SAFACT AS Z WHERE Z.NumeroD = x.NumeroR AND Z.TipoFac = 'B') AS BIGINT))
                ) AS ventas";*/

              $sql = "SELECT
CONVERT(int, CAST(sum(case when TipoFac = 'A' then monto/tasa end) AS money), 1) Vendidobs
from SAFACT where TipoFac in ('A','B') and DATEADD(dd, 0, DATEDIFF(dd, 0, safact.fechae)) BETWEEN  ? and ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $fechai);
        $sql->bindValue(2, $fechaf);
       // $sql->bindValue(3, $fechai);
       // $sql->bindValue(4, $fechaf);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}

