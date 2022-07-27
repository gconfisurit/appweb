<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class devolucionesdata extends Conectar{


	public function getdevoluciones($fechai, $fechaf, $ruta, $tipo){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
       $conectar= parent::conexion2();
       parent::set_names();

        //QUERY
            if($tipo === 'Todos' and $ruta === 'Todos'){
              $sql = "SELECT tipofac, safact.codvend as code_vendedor, numerod, safact.fechae as fecha_fact, safact.codclie as cod_clie, safact.descrip as cliente, Monto, saclie.fechae as fecha_ini_clie, NumeroR, safact.Notas1 as motivo,
                     (select descripcion as chofer from appChofer where cedula=(select cedula_chofer from appfacturas where correl=(select TOP 1 correl from appfacturas_det where numeros=NumeroR))) as chofer  FROM safact inner join saclie on safact.codclie = saclie.codclie  where DATEADD(dd, 0, DATEDIFF(dd, 0, safact.FechaE)) between '$fechai' and '$fechaf'  and tipofac in ('B','D') order by fecha_fact desc";
            }else{

                if($tipo === 'B' and $ruta === 'Todos'){
                    $sql = "SELECT tipofac, safact.codvend as code_vendedor, numerod, safact.fechae as fecha_fact, safact.codclie as cod_clie, safact.descrip as cliente, Monto, saclie.fechae as fecha_ini_clie, NumeroR,  safact.Notas1 as motivo,
                         (select descripcion as chofer from appChofer where cedula=(select cedula_chofer from appfacturas where correl=(select TOP 1 correl from appfacturas_det where numeros=NumeroR))) as chofer  FROM safact inner join saclie on safact.codclie = saclie.codclie  where DATEADD(dd, 0, DATEDIFF(dd, 0, safact.FechaE)) between '$fechai' and '$fechaf'  and tipofac ='B' order by fecha_fact desc";
                }else{

                    if($tipo === 'D' and $ruta === 'Todos'){
                        $sql = "SELECT tipofac, safact.codvend as code_vendedor, numerod, safact.fechae as fecha_fact, safact.codclie as cod_clie, safact.descrip as cliente, Monto, saclie.fechae as fecha_ini_clie, NumeroR,  safact.Notas1 as motivo,
                             (select descripcion as chofer from appChofer where cedula=(select cedula_chofer from appfacturas where correl=(select TOP 1 correl from appfacturas_det where numeros=NumeroR))) as chofer  FROM safact inner join saclie on safact.codclie = saclie.codclie  where DATEADD(dd, 0, DATEDIFF(dd, 0, safact.FechaE)) between '$fechai' and '$fechaf'  and tipofac ='D' order by fecha_fact desc";
                    }else{

                        if($tipo === 'B' and $ruta != 'Todos'){
                            $sql = "SELECT tipofac, safact.codvend as code_vendedor, numerod,  safact.fechae as fecha_fact, safact.codclie as cod_clie, safact.descrip as cliente, Monto, saclie.fechae as fecha_ini_clie, NumeroR,  safact.Notas1 as motivo,
                                 (select descripcion as chofer from appChofer where cedula=(select cedula_chofer from appfacturas where correl=(select TOP 1 correl from appfacturas_det where numeros=NumeroR))) as chofer  FROM safact inner join saclie on safact.codclie = saclie.codclie  where DATEADD(dd, 0, DATEDIFF(dd, 0, safact.FechaE)) between '$fechai' and '$fechaf' and safact.codvend = '$ruta'  and tipofac ='B' order by fecha_fact desc";
                        }else{
                            if($tipo === 'D' and $ruta != 'Todos'){
                                $sql = "SELECT tipofac, safact.codvend as code_vendedor, numerod,  safact.fechae as fecha_fact, safact.codclie as cod_clie, safact.descrip as cliente, Monto, saclie.fechae as fecha_ini_clie, NumeroR, safact.Notas1 as motivo,
                                 (select descripcion as chofer from appChofer where cedula=(select cedula_chofer from appfacturas where correl=(select TOP 1 correl from appfacturas_det where numeros=NumeroR))) as chofer  FROM safact inner join saclie on safact.codclie = saclie.codclie  where DATEADD(dd, 0, DATEDIFF(dd, 0, safact.FechaE)) between '$fechai' and '$fechaf' and safact.codvend = '$ruta'  and tipofac ='D' order by fecha_fact desc";

                            }
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

   	public function editarMotivo($motivo,$ndevolucion, $tipo ){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
       $conectar= parent::conexion2();
       parent::set_names();

        //QUERY
        echo "<script>console.log('tipo: " . $tipo . "' );</script>";


        if($tipo == 'B'){
             $sql = "UPDATE SAFACT SET Notas1 = '$motivo'  where TipoFac='B' and NumeroD='$ndevolucion'";
            }else{

                if($tipo == 'D'){
                    $sql = "UPDATE SAFACT SET Notas1 = '$motivo'  where TipoFac='D' and NumeroD='$ndevolucion'";
                }
         }
              
           

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
       $sql = $conectar->prepare($sql);
      
       return  $sql->execute(); ;
   }


}