<?php
set_time_limit(0);
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Despachos extends Conectar {



     public function DocumentosporDespachar($numerod)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $sql= "SELECT NumeroD, Descrip, TipoFac from safact AS SA where SA.TipoFac in ('A','C') and NumeroD='$numerod' and
                        (SA.NumeroR is null or SA.NumeroR in (select x.NumeroD from SAFACT as x where cast(x.Monto as int)<cast(SA.Monto as int) and X.TipoFac  in ('d','b')
                        and x.NumeroD=SA.NumeroR)) and SA.NumeroD not in (SELECT numeros FROM appfacturas_det where TipoFac='A' or TipoFac='C' ) and SA.NumeroD not in (SELECT numerof FROM sanota) order by SA.NumeroD";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getFactura($numerod) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT numerod, tipofac, fechae, descrip, codclie, codvend, CONCAT(direc1, ' ', direc2) as direccion,
                     COALESCE(MtoTotal/NULLIF(Tasa,0), 0) as total
                FROM SAFACT WHERE NumeroD = ? AND TipoFac = 'A'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $numerod);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNotaDeEntrega($numerod) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT numerod, tipofac, fechae, rsocial as descrip, direccion, codclie, codvend, total
                FROM SANOTA WHERE numerod = ? AND TipoFac = 'C'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $numerod);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCubicajeYPesoTotalporFactura($numerod) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT saitemfac.coditem, descrip, saprod.tara, saitemfac.esunid AS unidad, saprod.cantempaq AS paquetes, 
                       saitemfac.cantidad, tipofac, COALESCE(cubicaje, 0) as cubicaje
                FROM saitemfac
                         INNER JOIN saprod ON saitemfac.coditem = saprod.codprod
                         INNER JOIN SAPROD_01 ON SAPROD.CodProd = SAPROD_01.CodProd
                WHERE numerod = ? AND tipofac = 'A' ORDER BY saitemfac.coditem";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $numerod);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCubicajeYPesoTotalporNotaDeEntrega($numerod) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT saitemnota.coditem, descrip, saprod.tara, saitemnota.esunidad AS unidad, saprod.cantempaq AS paquetes, 
                       saitemnota.cantidad, tipofac, COALESCE(cubicaje, 0) as cubicaje
                FROM saitemnota
                         INNER JOIN saprod ON saitemnota.coditem = saprod.codprod
                         INNER JOIN SAPROD_01 ON SAPROD.CodProd = SAPROD_01.CodProd
                WHERE numerod = ? AND tipofac = 'C' ORDER BY saitemnota.coditem";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $numerod);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getExisteDocumentoEnDespachos($numerod, $tipodoc) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT numeros FROM appfacturas_det WHERE numeros = ? AND Tipofac = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $numerod);
        $sql->bindValue($i+=1, $tipodoc);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDocumentoEnDespachos($numerod, $tipodoc, $evaluate_active = false) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $activo = ($evaluate_active)
            ? " AND Activo = '1'"
            : "";

        //QUERY
       /* $sql= "SELECT correlativo, numerod, tipofac, fechae, destino, fecha_liqui, 
                    COALESCE(monto_cancelado, 0) as monto_cancelado, Choferes.Nomper as NomperChofer
                FROM Despachos
                    INNER JOIN Despachos_Det ON Despachos.Correlativo = Despachos_Det.ID_Correlativo
                    INNER JOIN Choferes ON Despachos.ID_Chofer = Choferes.Cedula
                WHERE Despachos_Det.Numerod = ? AND Tipofac = ? $activo";*/

                $sql = "SELECT correl, fecha_liqui, monto_cancelado from appfacturas_det where numeros = '$numerod' and Tipofac = '$tipodoc'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $numerod);
        $sql->bindValue(2, $tipodoc);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }



        public function getDetallesnDespacho($correl) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();
        //QUERY
                $sql = "select * from appfacturas where correl = '$correl'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }



        public function getChoferEnDespacho($cedula) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERy

                $sql = "select descripcion from appChofer where cedula = '$cedula'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductosDespachoCreadoEnFacturas($correlativo) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT DISTINCT coditem, descrip, cantempaq, esempaque, saprod.Tara as tara, codinst,
                    SUM(CASE WHEN EsUnid=0 THEN cantidad ELSE 0 END) AS bultos,
                    SUM(CASE WHEN EsUnid=1 THEN cantidad ELSE 0 END) AS paquetes
                FROM SAITEMFAC
                    INNER JOIN SAPROD ON SAITEMFAC.CodItem = SAPROD.CodProd
                WHERE TipoFac = 'A' AND (numerod in (SELECT Numeros FROM appfacturas_det WHERE correl = ? AND TipoFac = 'A'))
                GROUP BY CodItem, Descrip, CantEmpaq, EsEmpaque, saprod.Tara, CodInst ORDER BY SAITEMFAC.CodItem";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $correlativo);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductosDespachoCreadoEnNotaDeEntrega($correlativo) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT coditem, descrip, cantempaq, esempaque, saprod.Tara as tara, codinst,
                    SUM(CASE WHEN esunidad=0 THEN cantidad ELSE 0 END) AS bultos,
                    SUM(CASE WHEN esunidad=1 THEN cantidad ELSE 0 END) AS paquetes
                FROM SAITEMNOTA INNER JOIN SAPROD ON SAITEMNOTA.CodItem = SAPROD.CodProd
                WHERE SAITEMNOTA.TipoFac = 'C'  AND (numerod in (SELECT Numeros FROM appfacturas_det WHERE correl = ? AND TipoFac = 'C'))
                GROUP BY CodItem, Descrip, CantEmpaq, EsEmpaque, saprod.Tara, CodInst ORDER BY SAITEMNOTA.CodItem";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $correlativo);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getCabeceraDespacho($correlativo) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
       // $sql = "SELECT Correlativo, fechae, fechad, ID_Chofer, ID_Vehiculo, Destino, ID_Usuario FROM Despachos where Correlativo = ?";
       $sql = "select * from appfacturas where correl = '$correlativo'";
        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarDespacho($values) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
    
         $sql = "INSERT into appfacturas (fechae, id_usu, correl, fechad, nota, cedula_chofer, placa) values  (?, ?, ?, ?, ?, ?, ?)";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, date('Y-m-d'));
        $sql->bindValue($i+=1,$values['usuario']);
        $sql->bindValue($i+=1,$values['correlativo']);
        $sql->bindValue($i+=1,$values['fechad']);
        $sql->bindValue($i+=1,$values['destino']);
        $sql->bindValue($i+=1,$values['chofer']);
        $sql->bindValue($i+=1,$values['vehiculo']);
        
        //$resultado = $sql->execute();

        return   $resultado = $sql->execute();    //   $resultado ? $conectar->lastInsertId() : -1;
    }

    public function deleteDespacho($correlativo) {

        $delete1 = false;
        $delete2 = false;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql_1 = "DELETE FROM Despachos WHERE Correlativo = ?";
        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql_1 = $conectar->prepare($sql_1);
        $sql_1->bindValue(1,$correlativo);
        $delete1 = $sql_1->execute();

        //QUERY
        $sql_2 = "DELETE FROM Despachos_Det WHERE ID_Correlativo = ?";
        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql_2 = $conectar->prepare($sql_2);
        $sql_2->bindValue(1,$correlativo);
        $delete2 = $sql_2->execute();


        return ($delete1 && $delete2);
    }

    public function insertarDetalleDespacho($correlativo, $numero_documento) {
        $i=0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
            $sql = "INSERT into appfacturas_det (correl, numeros) VALUES (?,?);";
 

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $correlativo);
        $sql->bindValue(2,$numero_documento);;
        return  $sql->execute();
    }

    public function updateDespacho($correlativo, $destino, $chofer, $vehiculo, $fechad) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "UPDATE appfacturas SET nota = ?,  cedula_chofer = ?,  placa = ?,  fechad = ? WHERE correl = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$destino);
        $sql->bindValue(2,$chofer);
        $sql->bindValue(3,$vehiculo);
        $sql->bindValue(4,$fechad);
        $sql->bindValue(5,$correlativo);
        return $sql->execute();
    }

    public function updateDetalleDespacho($correlativo, $numerod_nuevo, $numerod_viejo, $tipofac_nuevo, $tipofac_viejo) {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "UPDATE appfacturas_det SET numeros = ?, TipoFac = ? WHERE correl = ? AND numeros = ? AND TipoFac = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $numerod_nuevo);
        $sql->bindValue($i+=1, $tipofac_nuevo);
        $sql->bindValue($i+=1, $correlativo);
        $sql->bindValue($i+=1, $numerod_viejo);
        $sql->bindValue($i+=1, $tipofac_viejo);
        return $sql->execute();
    }


 public function antesdeeliminar($correlativo) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT * from appfacturas_det where correl = ? and estado = '1'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $correlativo);
        return $sql->execute();
    }

    public function antesdeeliminar_contar($correlativo) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT count(numeros) as contador from appfacturas_det where  correl = ? and estado = '1'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $correlativo);
        return $sql->execute();
    }

     public function eliminar_del_documento($correlativo, $documento) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "DELETE FROM appfacturas_det WHERE correl = ? and numeros = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $correlativo);
        $sql->bindValue(2, $documento);
        return $sql->execute();
    }

      public function eliminar_del($correlativo) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "DELETE FROM appfacturas WHERE correl = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $correlativo);
        return $sql->execute();
    }
      public function eliminar_del_det($correlativo) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "DELETE FROM appfacturas_det WHERE correl = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $correlativo);
        return $sql->execute();
    }

     public function getnroplanilla($numeros) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT * from appdespacho where numerod = '$numeros'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        return $sql->execute();
    }

      public function MaxCorrelativo() {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        /*$conectar= parent::conexion2();
        parent::set_names();*/

        //QUERY
        $sql = "SELECT max(correl) as correlativo from appfacturas";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.

         $result = (new Conectar)->conexion2()->prepare($sql);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
       /* $sql = $conectar->prepare($sql);
        return $sql->execute();*/
    }


     public function eliminar_nroplanilla($documento,$nroplanilla) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "DELETE FROM appdespacho WHERE numerod = '$documento' and nroplanilla = '$nroplanilla''";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        return $sql->execute();
    }

    public function eliminar_nroplanilla_todo($nroplanilla) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "DELETE FROM appdespacho WHERE nroplanilla = '$nroplanilla'";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        return $sql->execute();
    }

    public function deleteDetalleDespacho($correlativo, $documento, $tipofac) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "DELETE FROM Despachos_Det WHERE ID_Correlativo = ? AND Numerod = ? AND Tipofac = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $correlativo);
        $sql->bindValue(2, $documento);
        $sql->bindValue(3, $tipofac);
        return $sql->execute();
    }

    public function get_despacho_por_id($correlativo) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "SELECT * from appfacturas where correl = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$correlativo, PDO::PARAM_STR);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_existe_factura_despachada_por_id($numerod) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "SELECT fechad, correlativo, 
                (SELECT Nomper FROM Usuarios WHERE Cedula = Despachos.ID_Usuario) 
                AS nomper 
                FROM Despachos_Det INNER JOIN Despachos ON Despachos_Det.id_correlativo = Despachos.correlativo WHERE Despachos_Det.numerod = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$numerod, PDO::PARAM_STR);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDocumentosPorCorrelativo($correlativo) {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        //QUERY
        $sql = "SELECT * FROM appfacturas_det where correl = ?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$correlativo);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMercanciaSinDespachar($fechai, $fechaf, $alm=array()){
        $i=0;
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
                $aux .= " OR CodUbic = ?";

            //armamos una lista de los depositos, si no existe ninguno seleccionado no se considera para realizar la consulta
            $depo = "(" . substr($aux, 4, strlen($aux)) . ")";

            $cond = ($depo != "()")
                ? ("AND ".$depo)
                : "";
        }

        //QUERY
        $sql= "SELECT CodProd, Descrip, CantEmpaq,
                    (SELECT SUM(cantidad) FROM SAITEMFAC WHERE esunid='0' AND CodProd=CodItem ".$cond." AND numerod IN (SELECT fa.numerod FROM SAFACT AS fa WHERE TipoFac='A' AND
                    DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? AND (NumeroR IS NULL OR NumeroD IN (SELECT x.NumeroR FROM SAFACT AS x WHERE x.TipoFac = 'B' AND x.NumeroR=fa.NumeroD AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? GROUP BY x.NumeroR HAVING cast(SUM(x.Monto) AS BIGINT)<cast(fa.Monto AS BIGINT))) AND NumeroD NOT IN (SELECT Despachos_Det.Numerod FROM APPWEBAJ.dbo.Despachos_Det)))
                    AS todob,
                    (SELECT SUM(cantidad) FROM SAITEMFAC WHERE esunid='1' AND CodProd=CodItem ".$cond." AND numerod IN (SELECT fa.numerod FROM SAFACT AS fa WHERE TipoFac='A' AND
                    DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? AND (NumeroR IS NULL OR NumeroD IN (SELECT x.NumeroR FROM SAFACT AS x WHERE x.TipoFac = 'B' AND x.NumeroR=fa.NumeroD AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ? GROUP BY x.NumeroR HAVING cast(SUM(x.Monto) AS BIGINT)<cast(fa.Monto AS BIGINT))) AND NumeroD NOT IN (SELECT Despachos_Det.Numerod FROM APPWEBAJ.dbo.Despachos_Det)))
                    AS todop,
                    (SELECT SUM(Existen) FROM SAEXIS WHERE CodProd=SAPROD.CodProd ".$cond.")
                    AS exis,
                    (SELECT SUM(ExUnidad) FROM SAEXIS WHERE CodProd=SAPROD.CodProd  ".$cond.")
                    AS exunid
                FROM SAPROD WHERE CodProd IN (SELECT coditem FROM SAITEMFAC WHERE TipoFac='A' ".$cond."
                AND DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) BETWEEN ? AND ?) ORDER BY CodProd";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        if ($depo != "()") {
            foreach ($alm as $num)
                $sql->bindValue($i += 1, $num);
        }
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        if ($depo != "()") {
            foreach ($alm as $num)
                $sql->bindValue($i += 1, $num);
        }
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        if ($depo != "()") {
            foreach ($alm as $num)
                $sql->bindValue($i += 1, $num);
        }
        if ($depo != "()") {
            foreach ($alm as $num)
                $sql->bindValue($i += 1, $num);
        }
        if ($depo != "()") {
            foreach ($alm as $num)
                $sql->bindValue($i += 1, $num);
        }
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    }
}

