<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class facturasinliquidar extends Conectar{


	public function getfacturasinliquidar($fechai, $fechaf, $chofer, $tipo){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
       $conectar= parent::conexion2();
       parent::set_names();

                //QUERY

                if($tipo === '0' and $chofer === 'Todos'){
                    $sql = "SELECT codvend as Ruta, appChofer.descripcion as Chofer, appfacturas.correl as NumDespacho, appfacturas_det.numeros as Factura, appfacturas.fechad as FechaDespacho, CodClie, Descrip as Cliente, MtoTotal, appChofer.descripcion, safact.fechae as FechaEmi
                    from appfacturas_det inner join appfacturas on appfacturas.correl = appfacturas_det.correl inner join SAFACT
                    on appfacturas_det.numeros = SAFACT.NumeroD inner join appChofer on appfacturas.cedula_chofer = appChofer.cedula  
                    where DATEADD(dd, 0, DATEDIFF(dd, 0, appfacturas.fechad)) between '$fechai' and '$fechaf'
                    and appfacturas_det.numeros in (select numerod from saacxc where tipocxc='10' and saldo>0) 
                    order by fechad, appfacturas_det.numeros";
                }else{

                    if($tipo === '1' and $chofer === 'Todos'){
                        $sql = "SELECT codvend as Ruta, appChofer.descripcion as Chofer, appfacturas.correl as NumDespacho, appfacturas_det.numeros as Factura, appfacturas.fechad as FechaDespacho, CodClie, Descrip as Cliente, MtoTotal, appChofer.descripcion, safact.fechae as FechaEmi
                        from appfacturas_det inner join appfacturas on appfacturas.correl = appfacturas_det.correl inner join SAFACT
                         on appfacturas_det.numeros = SAFACT.NumeroD inner join appChofer on appfacturas.cedula_chofer = appChofer.cedula  
                        where  DATEADD(dd, 0, DATEDIFF(dd, 0, appfacturas.fechad)) between '$fechai' and '$fechaf'
                        and appfacturas_det.numeros not in (select numerod from saacxc where tipocxc='10' and saldo>0) 
                        order by fechad, appfacturas_det.numeros";
                    }else{

                        if($tipo === '0' and $chofer != 'Todos'){
                            $sql = "SELECT codvend as Ruta, appChofer.descripcion as Chofer, appfacturas.correl as NumDespacho, appfacturas_det.numeros as Factura, appfacturas.fechad as FechaDespacho, CodClie, Descrip as Cliente, MtoTotal, appChofer.descripcion, safact.fechae as FechaEmi
                                    from appfacturas_det inner join appfacturas on appfacturas.correl = appfacturas_det.correl inner join SAFACT
                                    on appfacturas_det.numeros = SAFACT.NumeroD inner join appChofer on appfacturas.cedula_chofer = appChofer.cedula  
                                    where cedula_chofer = '$chofer' AND DATEADD(dd, 0, DATEDIFF(dd, 0, appfacturas.fechad)) between '$fechai' and '$fechaf'
                                    and appfacturas_det.numeros in (select numerod from saacxc where tipocxc='10' and saldo>0) 
                                    order by fechad, appfacturas_det.numeros";
                        }else{
                            if($tipo === '1' and $chofer != 'Todos'){
                                $sql = "SELECT codvend as Ruta, appChofer.descripcion as Chofer, appfacturas.correl as NumDespacho, appfacturas_det.numeros as Factura, appfacturas.fechad as FechaDespacho, CodClie, Descrip as Cliente, MtoTotal, appChofer.descripcion, safact.fechae as FechaEmi
                                from appfacturas_det inner join appfacturas on appfacturas.correl = appfacturas_det.correl inner join SAFACT
                                 on appfacturas_det.numeros = SAFACT.NumeroD inner join appChofer on appfacturas.cedula_chofer = appChofer.cedula  
                                where cedula_chofer = '$chofer' and DATEADD(dd, 0, DATEDIFF(dd, 0, appfacturas.fechad)) between '$fechai' and '$fechaf'
                                and appfacturas_det.numeros not in (select numerod from saacxc where tipocxc='10' and saldo>0) 
                                order by fechad, appfacturas_det.numeros";

                            }
                        }
                    }

                }
              
        

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
       $sql = $conectar->prepare($sql);
       $sql->execute();
       $result = $sql->fetchAll(PDO::FETCH_ASSOC);
      
       return $result ;
   }


}