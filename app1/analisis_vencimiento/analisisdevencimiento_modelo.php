<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class analisisdevencimiento extends Conectar{


	public function getanalisisdevencimiento($fechai, $fechaf, $codprov){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
       $conectar= parent::conexion2();
       parent::set_names();

        //QUERY

        if ($codprov == "Todos"){

          $sql = "SELECT * from SAACXP inner join SAPROV on saacxp.codprov
           = [SAPROV].codprov where [SAACXP].fechae between '$fechai' and '$fechaf' and SAACXP.tipocxp='10' and SAACXP.saldo>0 order by SAACXP.fechae desc" ;

          }else{

            $sql = "SELECT * from SAACXP inner join SAPROV on SAACXP.codprov
           = SAPROV.codprov where  SAACXP.fechae between '$fechai' and '$fechaf' and SAACXP.tipocxp='10' and SAACXP.saldo>0 and SAACXP.codprov = '$codprov' order by SAACXP.fechae desc";
          
          } 


        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
       $sql = $conectar->prepare($sql);
       $sql->execute();
       $result = $sql->fetchAll(PDO::FETCH_ASSOC);
      
       return $result ;
   }

   function dias_transcurridos($fecha_i,$fecha_f)
{
	$dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
	$dias 	= abs($dias); $dias = floor($dias);		
	return $dias;
}


}