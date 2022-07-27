
<?php
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class FacturaSinDes extends Conectar{
    public function getFacturas($tipo, $fechai, $fechaf, $convend, $verDespachadas)
    {
        $i = 0;
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        if ($verDespachadas == 0){ /*sin despachar*/
            if ($convend != "-"){
                if ($tipo == "-"){ /*busqueda con rango de fecha y numero de vendedor*/
                   
                   $sql = "SELECT *,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '0') as Bult,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '1') as Paq
                        from safact AS SA where DATEADD(dd, 0, DATEDIFF(dd, 0, SA.FechaE))
                        between '$fechai' and '$fechaf' and SA.TipoFac in ('A','C') and SA.codvend = '$convend' and
                        SA.NumeroD not in (SELECT numeros FROM appfacturas_det) and SA.NumeroD not in (SELECT numerof FROM sanota) and
                        (SA.NumeroR is null or SA.NumeroR in (select x.NumeroD from SAFACT as x where cast(x.Monto as int)<cast(SA.Monto as int) and X.TipoFac  in ('d','b')
                        and x.NumeroD=SA.NumeroR)) order by SA.NumeroD";
                
            }if ($tipo == "0"){ /*busqueda con rango de fecha con EDV y detal por despachar*/
                    
                    $sql = "SELECT *,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '0') as Bult,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '1') as Paq
                        from safact AS SA where DATEADD(dd, 0, DATEDIFF(dd, 0, SA.FechaE))
                        between '$fechai' and '$fechaf' and SA.TipoFac in ('A','C') and SA.codvend like '%n%' and
                        SA.NumeroD not in (SELECT numeros FROM appfacturas_det) and
                        (SA.NumeroR is null or SA.NumeroR in (select x.NumeroD from SAFACT as x where cast(x.Monto as int)<cast(SA.Monto as int) and X.TipoFac  in ('d','b')
                        and x.NumeroD=SA.NumeroR))  and SA.NumeroD not in (SELECT numerof FROM sanota) order by SA.NumeroD";
                }if ($tipo == "1"){/*busqueda con rango de fecha con EDV y mayor por despachar*/
                   
                   $sql = "SELECT *,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '0') as Bult,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '1') as Paq
                        from safact AS SA where DATEADD(dd, 0, DATEDIFF(dd, 0, SA.FechaE))
                        between '$fechai' and '$fechaf' and SA.TipoFac in ('A','C') and SA.codvend not like '%n%' and
                        SA.NumeroD not in (SELECT numeros FROM appfacturas_det) and
                        (SA.NumeroR is null or SA.NumeroR in (select x.NumeroD from SAFACT as x where cast(x.Monto as int)<cast(SA.Monto as int) and X.TipoFac  in ('d','b')
                        and x.NumeroD=SA.NumeroR)) and SA.NumeroD not in (SELECT numerof FROM sanota) order by SA.NumeroD";
                }
            }else{
                if ($tipo == "-"){/*buscar todos por despachar*/
                   
                   $sql = "SELECT *,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '0') as Bult,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '1') as Paq from safact AS SA where DATEADD(dd, 0, DATEDIFF(dd, 0, SA.FechaE))
                        between '$fechai' and '$fechaf' and SA.TipoFac in ('A','C') and
                        (SA.NumeroR is null or SA.NumeroR in (select x.NumeroD from SAFACT as x where cast(x.Monto as int)<cast(SA.Monto as int) and X.TipoFac  in ('d','b')
                        and x.NumeroD=SA.NumeroR)) and SA.NumeroD not in (SELECT numeros FROM appfacturas_det) and SA.NumeroD not in (SELECT numerof FROM sanota) order by SA.NumeroD";
                
            }if ($tipo == "0"){ /*busqueda rango de fecha todos los EDV tipo detal*/
                    
                    $sql = "SELECT *,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '0') as Bult,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '1') as Paq from safact AS SA where DATEADD(dd, 0, DATEDIFF(dd, 0, SA.FechaE))
                        between '$fechai' and '$fechaf' and codvend like '%n%' and SA.TipoFac in ('A','C') and
                        SA.NumeroD not in (SELECT numeros FROM appfacturas_det) and
                        (SA.NumeroR is null or SA.NumeroR in (select x.NumeroD from SAFACT as x where cast(x.Monto as int)<cast(SA.Monto as int) and X.TipoFac  in ('d','b')
                        and x.NumeroD=SA.NumeroR)) and SA.NumeroD not in (SELECT numerof FROM sanota) order by SA.NumeroD";
                }if ($tipo == "1"){ /*busqueda rango de fecha todos los EDV tipo mayor*/
                   
                   $sql = "SELECT *,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac  in ('A','C') and EsUnid = '0') as Bult,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac  in ('A','C') and EsUnid = '1') as Paq from safact AS SA where DATEADD(dd, 0, DATEDIFF(dd, 0, SA.FechaE))
                        between '$fechai' and '$fechaf' and codvend not like '%n%' and SA.TipoFac  in ('A','C') and
                        SA.NumeroD not in (SELECT numeros FROM appfacturas_det) and
                        (SA.NumeroR is null or SA.NumeroR in (select x.NumeroD from SAFACT as x where cast(x.Monto as int)<cast(SA.Monto as int) and X.TipoFac  in ('d','b')
                        and x.NumeroD=SA.NumeroR))  and SA.NumeroD not in (SELECT numerof FROM sanota) order by SA.NumeroD";
                }
            }
        }else{
            if ($convend != "-"){
                if ($tipo == "-"){
                    $sql = "SELECT *,
                        (select fechad from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where
                        appfacturas_det.numeros = sa.numerod) as fechad,
        
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '0') as Bult,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '1') as Paq,
        
                        (select Tiempo_Estimado_Despacho from savend_02 where savend_02.codvend = sa.codvend) as testimado
                        from safact AS SA where DATEADD(dd, 0, DATEDIFF(dd, 0, SA.FechaE))
                        between '$fechai' and '$fechaf' and SA.TipoFac in ('A','C') and SA.codvend = '$convend' and
                        SA.NumeroD in (SELECT numeros FROM appfacturas_det) and
                        (SA.NumeroR is null or SA.NumeroR in (select x.NumeroD from SAFACT as x where cast(x.Monto as int)<cast(SA.Monto as int) and X.TipoFac  in ('d','b')
                        and x.NumeroD=SA.NumeroR)) order by SA.NumeroD";

                }if ($tipo == "0"){

                    $sql = "SELECT *,
                        (select fechad from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where
                        appfacturas_det.numeros = sa.numerod) as fechad,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '0') as Bult,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '1') as Paq,
                        (select Tiempo_Estimado_Despacho from savend_02 where savend_02.codvend = sa.codvend) as testimado
                        from safact AS SA where DATEADD(dd, 0, DATEDIFF(dd, 0, SA.FechaE))
                        between '$fechai' and '$fechaf' and SA.TipoFac in ('A','C') and SA.codvend like '%n%' and
                        SA.NumeroD in (SELECT numeros FROM appfacturas_det) and
                        (SA.NumeroR is null or SA.NumeroR in (select x.NumeroD from SAFACT as x where cast(x.Monto as int)<cast(SA.Monto as int) and X.TipoFac  in ('d','b')
                        and x.NumeroD=SA.NumeroR)) order by SA.NumeroD";
                }if ($tipo == "1"){

                    $sql = "SELECT *,
                        (select fechad from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where
                        appfacturas_det.numeros = sa.numerod) as fechad,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '0') as Bult,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '1') as Paq,
                        (select Tiempo_Estimado_Despacho from savend_02 where savend_02.codvend = sa.codvend) as testimado
                        from safact AS SA where DATEADD(dd, 0, DATEDIFF(dd, 0, SA.FechaE))
                        between '$fechai' and '$fechaf' and SA.TipoFac in ('A','C') and SA.codvend not like '%n%' and
                        SA.NumeroD in (SELECT numeros FROM appfacturas_det) and
                        (SA.NumeroR is null or SA.NumeroR in (select x.NumeroD from SAFACT as x where cast(x.Monto as int)<cast(SA.Monto as int) and X.TipoFac  in ('d','b')
                        and x.NumeroD=SA.NumeroR)) order by SA.NumeroD";
                }
            }else{
                if ($tipo == "-"){
                   
                    $sql = "SELECT *,
                        (select fechad from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where
                        appfacturas_det.numeros = sa.numerod) as fechad,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '0') as Bult,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '1') as Paq,
                        (select Tiempo_Estimado_Despacho from savend_02 where savend_02.codvend = sa.codvend) as testimado
                        from safact AS SA where SA.TipoFac in ('A','C') and
                        (SA.NumeroR is null or SA.NumeroR in (select x.NumeroD from SAFACT as x where cast(x.Monto as int)<cast(SA.Monto as int) and X.TipoFac  in ('d','b')
                        and x.NumeroD=SA.NumeroR)) and SA.NumeroD in
                        (SELECT appfacturas_det.numeros FROM appfacturas_det inner join appfacturas on appfacturas_det.correl = appfacturas.correl where DATEADD(dd, 0, DATEDIFF(dd, 0, fechad))
                        between '$fechai' and '$fechaf') order by SA.NumeroD";

                }if ($tipo == "0"){
                   
                    $sql = "SELECT *,
                        (select fechad from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where
                        appfacturas_det.numeros = sa.numerod) as fechad,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '0') as Bult,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '1') as Paq,
                        (select Tiempo_Estimado_Despacho from savend_02 where savend_02.codvend = sa.codvend) as testimado
                        from safact AS SA where SA.TipoFac in ('A','C') and codvend like '%n%' and
                        (SA.NumeroR is null or SA.NumeroR in (select x.NumeroD from SAFACT as x where cast(x.Monto as int)<cast(SA.Monto as int) and X.TipoFac  in ('d','b')
                        and x.NumeroD=SA.NumeroR)) and SA.NumeroD in
                        (SELECT appfacturas_det.numeros FROM appfacturas_det inner join appfacturas on appfacturas_det.correl = appfacturas.correl where DATEADD(dd, 0, DATEDIFF(dd, 0, fechad))
                        between '$fechai' and '$fechaf') order by SA.NumeroD";

                }if ($tipo == "1"){
                    
                    $sql = "SELECT *,
                        (select fechad from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where
                        appfacturas_det.numeros = sa.numerod) as fechad,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '0') as Bult,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '1') as Paq,
                        (select Tiempo_Estimado_Despacho from savend_02 where savend_02.codvend = sa.codvend) as testimado
                        from safact AS SA where DATEADD(dd, 0, DATEDIFF(dd, 0, SA.FechaE))
                        between '$fechai' and '$fechaf' and codvend not like '%n%' and SA.TipoFac in ('A','C') and
                        SA.NumeroD in (SELECT numeros FROM appfacturas_det) and
                        (SA.NumeroR is null or SA.NumeroR in (select x.NumeroD from SAFACT as x where cast(x.Monto as int)<cast(SA.Monto as int) and X.TipoFac  in ('d','b')
                        and x.NumeroD=SA.NumeroR)) order by SA.NumeroD";
                    
                      
                       /* $sql = mssql_query("select *,
                        (select fechad from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where
                        appfacturas_det.numeros = sa.numerod) as fechad,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '0') as Bult,
                        (select sum(cantidad) from saitemfac where saitemfac.numerod = SA.numerod and saitemfac.tipofac in ('A','C') and EsUnid = '1') as Paq,
                        (select Tiempo_Estimado_Despacho from savend_02 where savend_02.codvend = sa.codvend) as testimado
                        from safact AS SA where SA.TipoFac in ('A','C') and codvend not like '%n%' and
                        (SA.NumeroR is null or SA.NumeroR in (select x.NumeroD from SAFACT as x where cast(x.Monto as int)<cast(SA.Monto as int) and X.TipoFac  in ('d','b')
                        and x.NumeroD=SA.NumeroR)) and SA.NumeroD in
                        (SELECT appfacturas_det.numeros FROM appfacturas_det inner join appfacturas on appfacturas_det.correl = appfacturas.correl where DATEADD(dd, 0, DATEDIFF(dd, 0, fechad))
                        between '$fechai' and '$fechaf') order by SA.NumeroD");*/
                }
            }
        } 
       
       $sql = $conectar->prepare($sql); 
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCanales(){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $sql= "SELECT DISTINCT Clase FROM SAVEND WHERE Clase IS NOT NULL AND LEN(Clase) > 1";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_cabecera_factura_por_id($numerod, $tipofac){
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion2();
        parent::set_names();

        $sql= "SELECT sa.numerod, sa.codvend AS vendedor, sa.codclie AS codcliente, sa.descrip AS cliente, sa.fechae AS fechaemi, sa.mtototal, sa.monto, sa.descto1, sa.mtotax, codusua, sataxvta.CodTaxs, sataxvta.MtoTax AS tax
                FROM safact AS sa
                    LEFT JOIN saclie ON sa.codclie = saclie.codclie
                    LEFT JOIN sataxvta ON sa.numerod = sataxvta.numerod
                WHERE sa.numerod = ? AND sa.tipofac = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $numerod);
        $sql->bindValue(2, $tipofac);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    function dias_transcurridos($fecha_i,$fecha_f)
    {
        $dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
        $dias 	= abs($dias); $dias = floor($dias);		
        return $dias;
    }
}