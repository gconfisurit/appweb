<?php
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Kpi extends Conectar
{
    public function get_coordinadores()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT distinct coordinador from savend_02 d inner join savend S on S.codvend= d.CodVend where (d.coordinador = '' or d.coordinador is not null) and d.coordinador != ' ' and S.Activo = 1 and s.codvend != '00' and s.codvend != '16'  order by coordinador Asc";
        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_rutasPorCoordinador($nombre)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT * FROM savend INNER JOIN savend_02 ON savend.codvend = savend_02.codvend
                WHERE activo = '1' AND coordinador != '' AND savend_02.coordinador = ?
                ORDER BY savend.codvend";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $nombre);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_MaestroClientesPorRuta($ruta)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT (saclie.CodClie) AS codclie, Descrip as descrip, Direc2 AS direc, (SELECT DiasVisita FROM SACLIE_01 WHERE SACLIE_01.CodClie=saclie.CodClie) as dia_visita FROM saclie INNER JOIN saclie_01 ON saclie.codclie = saclie_01.codclie
                WHERE activo = '1' AND (saclie.CodVend = '$ruta' or Ruta_Alternativa = '$ruta' OR Ruta_Alternativa_2 = '$ruta')";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $ruta);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_ClientesActivosPorRuta($ruta, $fechai, $fechaf)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT distinct(SAFACT.CodClie) AS codclie, Descrip as descrip, Direc2 AS direc, (SELECT DiasVisita FROM SACLIE_01 WHERE SACLIE_01.CodClie=SAFACT.CodClie) as dia_visita FROM SAFACT WHERE SAFACT.CodVend = ? AND TipoFac in ('A') AND SAFACT.CodClie IN (SELECT SACLIE.CodClie FROM SACLIE INNER JOIN SACLIE_01 ON SACLIE.CodClie = SACLIE_01.CodClie
                WHERE activo = '1' AND (SACLIE.CodVend = ? or Ruta_Alternativa = ? OR Ruta_Alternativa_2 = ?)) AND DATEADD(dd, 0, DATEDIFF(dd, 0, SAFACT.FechaE)) between ? and ? AND NumeroD NOT IN (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac in ('A') AND x.NumeroR is not NULL AND cast(X.Monto as BIGINT) = cast((select Z.Monto from SAFACT AS Z where Z.NumeroD = x.NumeroR and Z.TipoFac in ('B'))as BIGINT))
                
                UNION
                
                SELECT distinct(SANOTA.CodClie) AS codclie, rsocial AS descip, direccion AS direc, (SELECT DiasVisita FROM SACLIE_01 WHERE SACLIE_01.CodClie=SANOTA.CodClie) as dia_visita FROM SANOTA WHERE SANOTA.CodVend = ? AND TipoFac in ('C') AND SANOTA.numerof = '0' AND SANOTA.CodClie IN (SELECT SACLIE.CodClie FROM SACLIE INNER JOIN SACLIE_01 ON SACLIE.CodClie = SACLIE_01.CodClie
                WHERE activo = '1' AND (SACLIE.CodVend = ? or Ruta_Alternativa = ? OR Ruta_Alternativa_2 = ?)) AND DATEADD(dd, 0, DATEDIFF(dd, 0, SANOTA.FechaE)) between ? and ? AND NumeroD NOT IN (SELECT X.NumeroD FROM SANOTA AS X WHERE X.TipoFac in ('C') AND x.numerof is not NULL AND cast(X.subtotal as BIGINT) = cast((select Z.subtotal from SANOTA AS Z where Z.NumeroD = x.numerof and Z.TipoFac in ('D'))as BIGINT))";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);

        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_ClientesNoActivosPorRuta($ruta, $fechai, $fechaf)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT cli.codclie AS codclie, cli.descrip AS descrip, cli.Direc2 AS direc, cli01.DiasVisita AS dia_visita  FROM SACLIE AS CLI INNER JOIN saclie_01 AS CLI01 ON CLI.codclie = CLI01.codclie WHERE CLI.activo = 1 AND (CLI.CodVend = ? OR CLI01.Ruta_Alternativa = ? OR CLI01.Ruta_Alternativa_2 = ?)
                AND CLI.codclie NOT IN
                (SELECT DISTINCT (SAFACT.CodClie) AS CODCLIE FROM SAFACT WHERE SAFACT.CodVend = ? AND TipoFac = 'A' AND SAFACT.CodClie IN (SELECT SACLIE.CodClie FROM SACLIE INNER JOIN SACLIE_01 ON SACLIE.CodClie = SACLIE_01.CodClie
                WHERE ACTIVO = 1 AND (SACLIE.CodVend = ? OR Ruta_Alternativa = ? OR Ruta_Alternativa_2 = ?)) AND DATEADD(dd, 0, DATEDIFF(dd, 0, SAFACT.FechaE)) BETWEEN ? AND ? AND NumeroD NOT IN (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac IN ('A') AND x.NumeroR IS NOT NULL AND cast(X.Monto AS BIGINT) = cast((SELECT Z.Monto FROM SAFACT AS Z WHERE Z.NumeroD = x.NumeroR AND Z.TipoFac IN ('B')) AS BIGINT))
                
                UNION
                
                SELECT DISTINCT (SANOTA.CodClie) AS CODCLIE FROM SANOTA WHERE SANOTA.CodVend = ? AND TipoFac IN ('C') AND SANOTA.numerof = '0' AND SANOTA.CodClie IN (SELECT SACLIE.CodClie FROM SACLIE INNER JOIN SACLIE_01 ON SACLIE.CodClie = SACLIE_01.CodClie
                WHERE ACTIVO = 1 AND (SACLIE.CodVend = ? OR Ruta_Alternativa = ? OR Ruta_Alternativa_2 = ?)) AND DATEADD(dd, 0, DATEDIFF(dd, 0, SANOTA.FechaE)) BETWEEN ? AND ? AND NumeroD NOT IN (SELECT X.NumeroD FROM SANOTA AS X WHERE X.TipoFac IN ('C') AND x.numerof IS NOT NULL AND cast(X.subtotal AS BIGINT) = cast((SELECT Z.subtotal FROM SANOTA AS Z WHERE Z.NumeroD = x.numerof AND Z.TipoFac IN ('D')) AS BIGINT)))";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $ruta);

        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);

        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_frecuenciaVisita($ruta)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT * FROM savend_02 WHERE CodVend = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $ruta);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_ventasFactura($ruta, $fechai, $fechaf)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT numerod, descrip, fechae, COALESCE(TGravable/NULLIF(Tasa,0), 0) as montod FROM safact WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, safact.FechaE)) BETWEEN ? AND ? AND safact.codvend = ? AND tipofac = 'A' AND NumeroD NOT IN 
                (SELECT X.NumeroD FROM SAFACT AS X WHERE X.TipoFac = 'A' AND x.NumeroR IS NOT NULL AND
                CAST(X.Monto AS BIGINT) = CAST((SELECT Z.Monto FROM SAFACT AS Z WHERE Z.NumeroD = x.NumeroR AND Z.TipoFac = 'B') AS BIGINT))";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $ruta);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_ventasNotas($ruta, $fechai, $fechaf)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT numerod, rsocial as descrip, fechae, COALESCE(subtotal, 0) AS montod FROM sanota WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, sanota.FechaE)) BETWEEN ? AND ? AND sanota.codvend = ? AND tipofac = 'C' AND SANOTA.numerof = '0' AND NumeroD NOT IN 
                (SELECT X.NumeroD FROM sanota AS X WHERE X.TipoFac = 'C' AND x.Numerof IS NOT NULL AND
                CAST(X.subtotal AS BIGINT) = CAST((SELECT Z.subtotal FROM SAnota AS Z WHERE Z.NumeroD = x.Numerof AND Z.TipoFac = 'D')AS BIGINT))";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $ruta);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_devolucionesFactura($ruta, $fechai, $fechaf)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT numerod, descrip, fechae, COALESCE(TGravable/NULLIF(Tasa,0), 0) as montod, tipofac FROM safact WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, safact.FechaE)) BETWEEN ? AND ? AND safact.codvend = ? AND tipofac = 'B'";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $ruta);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_devolucionesNotas($ruta, $fechai, $fechaf)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT numerod, rsocial as descrip, fechae, COALESCE(subtotal, 0) AS montod, tipofac FROM SANOTA WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, SANOTA.FechaE)) BETWEEN ? AND ? AND SANOTA.codvend = ? AND tipofac = 'D'";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $ruta);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function obtenerObjetivo($ruta)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT ObjVentasBu FROM SAVEND_02 where CodVend ='$ruta'";
        $sql = $conectar->prepare($sql);

        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_montoDivisasDevolucionesFactura($ruta, $fechai, $fechaf)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT COALESCE(SUM(TGravable/NULLIF(Tasa,0)), 0) AS MontoD FROM safact WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, safact.FechaE)) BETWEEN ? AND ? 
                AND safact.codvend = ? AND tipofac = 'B'";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $ruta);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_montoDivisasDevolucionesNotas($ruta, $fechai, $fechaf)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT COALESCE(SUM(subtotal), 0) AS MontoD FROM sanota WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, sanota.FechaE)) BETWEEN ? AND ? 
                AND sanota.codvend = ? AND tipofac = 'D'";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $ruta);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_ventasDivisasFactura($ruta, $fechai, $fechaf)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT COALESCE(SUM(TGravable/NULLIF(Tasa,0)), 0) AS MontoD FROM safact 
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, safact.FechaE)) BETWEEN ? AND ? AND safact.codvend = ? AND tipofac = 'A'";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $ruta);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_ventasDivisasNotas($ruta, $fechai, $fechaf)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT COALESCE(SUM(CASE WHEN descuento IS NOT NULL THEN subtotal-descuento ELSE subtotal END)/1.16, 0) AS MontoD FROM SANOTA 
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, SANOTA.FechaE)) BETWEEN ? AND ? AND SANOTA.codvend = ? AND tipofac = 'C' AND SANOTA.numerof = '0'";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $ruta);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_ventasDivisasPepsicoFactura($ruta, $fechai, $fechaf)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT COALESCE(SUM(TotalItem/NULLIF(Tasai,0)), 0) AS MontoD
                FROM SAITEMFAC
                         INNER JOIN SAPROD ON SAITEMFAC.coditem = SAPROD.codprod
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMFAC.FechaE)) BETWEEN ? AND ? 
                AND SAITEMFAC.CodVend = ? AND SAITEMFAC.TipoFac = 'A' AND SAPROD.marca like ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, '%PEPSICO%', PDO::PARAM_STR);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_ventasDivisasPepsicoNotas($ruta, $fechai, $fechaf)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT COALESCE(SUM(CASE WHEN descuento IS NOT NULL THEN totalitem-descuento ELSE totalitem END)/1.16, 0) AS MontoD
                FROM SAITEMNOTA
                         INNER JOIN SAPROD ON SAITEMNOTA.coditem = SAPROD.codprod
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMNOTA.FechaE)) BETWEEN ? AND ? 
                AND SAITEMNOTA.CodVend = ? AND SAITEMNOTA.tipofac = 'C' AND SAPROD.marca like ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, '%PEPSICO%', PDO::PARAM_STR);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_ventasDivisasComplementariaFactura($ruta, $fechai, $fechaf)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT COALESCE(SUM(TotalItem/NULLIF(Tasai,0)), 0) AS MontoD
                FROM SAITEMFAC
                         INNER JOIN SAPROD ON SAITEMFAC.coditem = SAPROD.codprod
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMFAC.FechaE)) BETWEEN ? AND ? 
                AND SAITEMFAC.CodVend = ? AND SAITEMFAC.TipoFac = 'A' AND SAPROD.marca NOT LIKE ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, '%PEPSICO%', PDO::PARAM_STR);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_ventasDivisasComplementariaNotas($ruta, $fechai, $fechaf)
    {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        $sql = "SELECT COALESCE(SUM(CASE WHEN descuento IS NOT NULL THEN totalitem-descuento ELSE totalitem END)/1.16, 0) AS MontoD
                FROM SAITEMNOTA
                         INNER JOIN SAPROD ON SAITEMNOTA.coditem = SAPROD.codprod
                WHERE DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMNOTA.FechaE)) BETWEEN ? AND ? 
                AND SAITEMNOTA.CodVend = ? AND SAITEMNOTA.tipofac = 'C' AND SAPROD.marca NOT LIKE ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $ruta);
        $sql->bindValue($i+=1, '%PEPSICO%', PDO::PARAM_STR);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }
}