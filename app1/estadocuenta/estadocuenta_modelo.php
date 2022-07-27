<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class estadocuenta extends Conectar{


	public function getestadocuenta($fechai, $fechaf , $cliente, $tipo){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION. tipocxc, fechae, tipop, fechap,tipofac,estado,mtotalbs,tasa,mtotald,mabonado,moneda,nc,saldoact,numerod,referencia,detalle
       $conectar= parent::conexion2();
       parent::set_names();

        //QUERY

        if($tipo == 'B'){

           $sql = "SELECT  [TipoCxc]
	                ,SAACXC.[CodClie]
				          ,Descrip
                  ,SAACXC.[FechaE]
                  ,[FechaV]
                  ,[NumeroD]
                  ,[NumeroN]
                  ,[Document]
                  ,[Monto]
                  ,[SaldoAct]
                  FROM [SAACXC] inner join SACLIE on SACLIE.CodClie=SAACXC.CodClie where [SAACXC].codclie = '$cliente' AND DATEADD(dd, 0, DATEDIFF(dd, 0, [SAACXC].fechae)) between '$fechai' and '$fechaf'";


        }else{


           $sql = "SELECT  [TipoCxc]
                  ,SAACXC.[CodClie]
				          ,Descrip
                  ,SAACXC.[FechaE]
                  ,[FechaV]
                  ,[NumeroD]
                  ,[NumeroN]
                  ,[Document]
                  ,[Monto]
                  ,[SaldoAct]
              FROM [AJ_D].[dbo].[SAACXC] inner join SACLIE on SACLIE.CodClie=SAACXC.CodClie where [SAACXC].codclie = '$cliente' AND DATEADD(dd, 0, DATEDIFF(dd, 0, [SAACXC].fechae)) between '$fechai' and '$fechaf'";




        }

           
        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
       $sql = $conectar->prepare($sql);
       $sql->execute();
       $result = $sql->fetchAll(PDO::FETCH_ASSOC);
      
       return $result ;
   }

   public function saldoactual( $cliente){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
       $conectar= parent::conexion2();
       parent::set_names();

        //QUERY
  
          $sql = "SELECT sum(saldo) as ultimo FROM SAACXCAUX1 WHERE (codclie = '$cliente') and tipocxc = '10'" ;



        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
       $sql = $conectar->prepare($sql);
       $sql->execute();
       $result = $sql->fetchAll(PDO::FETCH_ASSOC);
      
       return $result ;
   }

    public function montosabonados( $cliente){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
       $conectar= parent::conexion2();
       parent::set_names();

        //QUERY
  
          $sql = "SELECT sum(mabonado) as ultimo FROM SAACXCAUX1 WHERE (codclie = '$cliente') and tipocxc = '41'" ;



        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
       $sql = $conectar->prepare($sql);
       $sql->execute();
       $result = $sql->fetchAll(PDO::FETCH_ASSOC);
      
       return $result ;
   }


   public function devoluciones( $cliente){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
       $conectar= parent::conexion2();
       parent::set_names();

        //QUERY
  
          $sql = "SELECT * FROM SAACXCAUX1 WHERE (codclie = '$cliente') and tipocxc = '31'" ;



        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
       $sql = $conectar->prepare($sql);
       $sql->execute();
       $result = $sql->fetchAll(PDO::FETCH_ASSOC);
      
       return $result ;
   }


}