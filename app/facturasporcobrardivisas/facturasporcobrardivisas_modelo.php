<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class facturasporcobrardivisas extends Conectar{


	public function getfacturasporcobrardivisas($fechai, $fechaf){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
       $conectar= parent::conexion2();
       parent::set_names();

        //QUERY

       

          $sql = "SELECT 
          (select Descrip from SACONF) as Empresa,
          SUM(case when (DATEDIFF(DD, SAACXCAUX1.FechaE, GETDATE())>=0 and DATEDIFF(DD, SAACXCAUX1.FechaE, GETDATE())<=7) then SAACXCAUX1.Saldo else 0 end) as Total_0_a_7_Dias,
          SUM(case when (DATEDIFF(DD, SAACXCAUX1.FechaE, GETDATE())>=8 and DATEDIFF(DD, SAACXCAUX1.FechaE, GETDATE())<=15) then SAACXCAUX1.Saldo else 0 end) as Total_8_a_15_Dias,
          SUM(case when (DATEDIFF(DD, SAACXCAUX1.FechaE, GETDATE())>=16 and DATEDIFF(DD, SAACXCAUX1.FechaE, GETDATE())<=40) then SAACXCAUX1.Saldo else 0 end) as Total_16_a_40_Dias,
          SUM(case when (DATEDIFF(DD, SAACXCAUX1.FechaE, GETDATE())>40) then SAACXCAUX1.Saldo else 0 end) as Total_Mayor_a_40_Dias,
          SUM(SAACXCAUX1.Saldo) as SubTotal
          from SAACXCAUX1 inner join saclie on SAACXCAUX1.codclie = saclie.codclie 
           where SAACXCAUX1.saldo>0 AND SAACXCAUX1.tipocxc='10' and saacxcaux1.estado=0";


        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
       $sql = $conectar->prepare($sql);
       $sql->execute();
       $result = $sql->fetchAll(PDO::FETCH_ASSOC);
      
       return $result ;
   }


   public function getdetallesfacturasporcobrardivisas($fechai, $fechaf, $data){

    //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
    //CUANDO ES APPWEB ES CONEXION.
   $conectar= parent::conexion2();
   parent::set_names();

    //QUERY

    switch ($data) {

      case 1:

        $sql = "SELECT (case when saacxcaux1.tipocxc = 10 then 'FACT' else 'N/D' end) as TipoOpe, saacxcaux1.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
        CONVERT( date , saacxcaux1.fechae ) as FechaEmi, 
        (case when saacxcaux1.tipocxc = 10 then (select CONVERT( VARCHAR ,fechad,103) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
               appfacturas_det.numeros = saacxcaux1.numerod) else 'N/A' end) as FechaDesp,
               DATEDIFF(DD, saacxcaux1.fechae, (case when saacxcaux1.tipocxc = 10 then (select CONVERT( date ,GETDATE()) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
               appfacturas_det.numeros = saacxcaux1.numerod) else saacxcaux1.fechae end))as DiasTrans,
        DATEDIFF(DD, saacxcaux1.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
        saacxcaux1.saldo as SaldoPend, (select codvend from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as ruta,
        (select Coordinador from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor 
         from saacxcaux1 inner join saclie on saacxcaux1.codclie = saclie.codclie 
         inner join saacxc on saacxc.numerod = saacxcaux1.numerod
        where (DATEADD(dd, 0, DATEDIFF(dd, 0, saacxcaux1.FechaE)) between DATEADD(day, -7, CONVERT( date ,GETDATE())) and GETDATE()) and saacxcaux1.saldo>0 AND saacxcaux1.tipocxc='10'  and saacxcaux1.estado =0
        order by saacxcaux1.FechaE asc";
  
      
      break;
  
  
      case 2:

        $sql = "SELECT (case when saacxcaux1.tipocxc = 10 then 'FACT' else 'N/D' end) as TipoOpe, saacxcaux1.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
        CONVERT( date , saacxcaux1.fechae ) as FechaEmi, 
        (case when saacxcaux1.tipocxc = 10 then (select CONVERT( VARCHAR ,fechad,103) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
               appfacturas_det.numeros = saacxcaux1.numerod) else 'N/A' end) as FechaDesp,
               DATEDIFF(DD, saacxcaux1.fechae, (case when saacxcaux1.tipocxc = 10 then (select CONVERT( date ,GETDATE()) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
               appfacturas_det.numeros = saacxcaux1.numerod) else saacxcaux1.fechae end))as DiasTrans,
        DATEDIFF(DD, saacxcaux1.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
        saacxcaux1.saldo as SaldoPend, (select codvend from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as ruta,
        (select Coordinador from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor 
         from saacxcaux1 inner join saclie on saacxcaux1.codclie = saclie.codclie 
         inner join saacxc on saacxc.numerod = saacxcaux1.numerod
        where (DATEADD(dd, 0, DATEDIFF(dd, 0, saacxcaux1.FechaE)) between DATEADD(day, -15, CONVERT( date ,GETDATE())) and DATEADD(day, -8, CONVERT( date ,GETDATE()))) and saacxcaux1.saldo>0 AND saacxcaux1.tipocxc='10'  and saacxcaux1.estado =0
        order by saacxcaux1.FechaE asc";
          
      break;
      
      
      case 3:

        $sql = "SELECT (case when saacxcaux1.tipocxc = 10 then 'FACT' else 'N/D' end) as TipoOpe, saacxcaux1.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
        CONVERT( date , saacxcaux1.fechae ) as FechaEmi, 
        (case when saacxcaux1.tipocxc = 10 then (select CONVERT( VARCHAR ,fechad,103) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
               appfacturas_det.numeros = saacxcaux1.numerod) else 'N/A' end) as FechaDesp,
               DATEDIFF(DD, saacxcaux1.fechae, (case when saacxcaux1.tipocxc = 10 then (select CONVERT( date ,GETDATE()) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
               appfacturas_det.numeros = saacxcaux1.numerod) else saacxcaux1.fechae end))as DiasTrans,
        DATEDIFF(DD, saacxcaux1.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
        saacxcaux1.saldo as SaldoPend, (select codvend from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as ruta,
        (select Coordinador from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor 
         from saacxcaux1 inner join saclie on saacxcaux1.codclie = saclie.codclie 
         inner join saacxc on saacxc.numerod = saacxcaux1.numerod
        where (DATEADD(dd, 0, DATEDIFF(dd, 0, saacxcaux1.FechaE)) between DATEADD(day, -40, CONVERT( date ,GETDATE())) and DATEADD(day, -16, CONVERT( date ,GETDATE()))) and saacxcaux1.saldo>0 AND saacxcaux1.tipocxc='10'  and saacxcaux1.estado =0
        order by saacxcaux1.FechaE asc";
          
      break;
        
        
      case 4:

        $sql = "SELECT (case when saacxcaux1.tipocxc = 10 then 'FACT' else 'N/D' end) as TipoOpe, saacxcaux1.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
        CONVERT( date , saacxcaux1.fechae ) as FechaEmi, 
        (case when saacxcaux1.tipocxc = 10 then (select CONVERT( VARCHAR ,fechad,103) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
               appfacturas_det.numeros = saacxcaux1.numerod) else 'N/A' end) as FechaDesp,
               DATEDIFF(DD, saacxcaux1.fechae, (case when saacxcaux1.tipocxc = 10 then (select CONVERT( date ,GETDATE()) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
               appfacturas_det.numeros = saacxcaux1.numerod) else saacxcaux1.fechae end))as DiasTrans,
        DATEDIFF(DD, saacxcaux1.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
        saacxcaux1.saldo as SaldoPend, (select codvend from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as ruta,
        (select Coordinador from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor 
         from saacxcaux1 inner join saclie on saacxcaux1.codclie = saclie.codclie 
         inner join saacxc on saacxc.numerod = saacxcaux1.numerod
        where (SAACXC.FechaE < DATEADD(day, -40, CONVERT( date ,GETDATE()))) and saacxcaux1.saldo>0 AND saacxcaux1.tipocxc='10'  and saacxcaux1.estado =0
        order by saacxcaux1.FechaE asc";
  
          
      break;
         
         
      case 5:

        $sql = "SELECT (case when saacxc.tipocxc = 10 then 'FACT' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
        CONVERT( date , saacxc.fechae ) as FechaEmi, 
        (case when saacxc.tipocxc = 10 then (select CONVERT( VARCHAR ,fechad,103) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
          appfacturas_det.numeros = saacxc.numerod) else 'N/A' end) as FechaDesp,
          DATEDIFF(DD, saacxc.fechae, (case when saacxc.tipocxc = 10 then (select CONVERT( date ,GETDATE()) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
          appfacturas_det.numeros = saacxc.numerod) else saacxc.fechae end))as DiasTrans,
        DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
         saacxcaux1.saldo as SaldoPend, (select codvend from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as ruta, 
         (select Coordinador from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
        from saacxcaux1 inner join saclie on saacxcaux1.codclie = saclie.codclie 
          inner join saacxc on saacxc.numerod = saacxcaux1.numerod
        where saacxcaux1.saldo>0 AND saacxcaux1.tipocxc='10' and saacxcaux1.estado =0
        order by saacxcaux1.FechaE asc;";
  
          
      break;
  }

    //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
   $sql = $conectar->prepare($sql);
   $sql->execute();
   $result = $sql->fetchAll(PDO::FETCH_ASSOC);
  
   return $result ;
}


}