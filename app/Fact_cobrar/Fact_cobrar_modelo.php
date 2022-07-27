<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class Fact_cobrar extends Conectar{


	public function getfacturasporcobrar($vendedor){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
       $conectar= parent::conexion2();
       parent::set_names();

        //QUERY

        if ($vendedor != 'Todos'){

          $sql = "SELECT 
                (select Descrip from SACONF) as Empresa,
                SUM(case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=0 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=7) then SAACXC.Saldo else 0 end) as Total_0_a_7_Dias,
                SUM(case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=8 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=15) then SAACXC.Saldo else 0 end) as Total_8_a_15_Dias,
                SUM(case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=16 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=40) then SAACXC.Saldo else 0 end) as Total_16_a_40_Dias,
                SUM(case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>40) then SAACXC.Saldo else 0 end) as Total_Mayor_a_40_Dias,
                SUM(SAACXC.Saldo) as SubTotal
                from saacxc inner join saclie on saacxc.codclie = saclie.codclie 
                where saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') and SAACXC.CodVend='$vendedor' " ;

          }else{

            $sql = "SELECT 
            (select Descrip from SACONF) as Empresa,
            SUM(case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=0 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=7) then SAACXC.Saldo else 0 end) as Total_0_a_7_Dias,
            SUM(case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=8 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=15) then SAACXC.Saldo else 0 end) as Total_8_a_15_Dias,
            SUM(case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=16 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=40) then SAACXC.Saldo else 0 end) as Total_16_a_40_Dias,
            SUM(case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>40) then SAACXC.Saldo else 0 end) as Total_Mayor_a_40_Dias,
            SUM(SAACXC.Saldo) as SubTotal
             from saacxc inner join saclie on saacxc.codclie = saclie.codclie 
             where saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20')  "; /*and saacxc.FechaE between '$fechai' and '$fechaf'*/
          
          } 


        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
       $sql = $conectar->prepare($sql);
       $sql->execute();
       $result = $sql->fetchAll(PDO::FETCH_ASSOC);
      
       return $result ;
   }


   public function getdetallesfacturasporcobrar($vendedor, $data){

    //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
    //CUANDO ES APPWEB ES CONEXION.
   $conectar= parent::conexion2();
   parent::set_names();

    //QUERY

    switch ($data) {

      case 1:

        $sql = "SELECT (case when saacxc.tipocxc = 10 then 'FACT' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
                CONVERT( date , saacxc.fechae ) as FechaEmi, 
                (case when saacxc.tipocxc = 10 then (select CONVERT( VARCHAR ,fechad,103) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
                  appfacturas_det.numeros = saacxc.numerod) else 'N/A' end) as FechaDesp,
                  DATEDIFF(DD, saacxc.fechae, (case when saacxc.tipocxc = 10 then (select CONVERT( date ,GETDATE()) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
                  appfacturas_det.numeros = saacxc.numerod) else saacxc.fechae end))as DiasTrans,
                DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
                UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, (saacxc.saldo/SAFACT.Tasa) as SaldoPendolar,
                (select Coordinador from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
                from saacxc inner join saclie on saacxc.codclie = saclie.codclie inner join SAFACT on SAFACT.NumeroD= SAACXC.NumeroD 
                where (DATEADD(dd, 0, DATEDIFF(dd, 0, SAACXC.FechaE)) between DATEADD(day, -7, CONVERT( date ,GETDATE())) and DATEADD(day, 0, CONVERT( date ,GETDATE()))) 
                and saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') and SAACXC.CodVend='$vendedor'
                order by saacxc.FechaE asc";
  
      
      break;
  
  
      case 2:

        $sql = "SELECT (case when saacxc.tipocxc = 10 then 'FACT' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
                CONVERT( date , saacxc.fechae ) as FechaEmi, 
                (case when saacxc.tipocxc = 10 then (select CONVERT( VARCHAR ,fechad,103) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
                  appfacturas_det.numeros = saacxc.numerod) else 'N/A' end) as FechaDesp,
                  DATEDIFF(DD, saacxc.fechae, (case when saacxc.tipocxc = 10 then (select CONVERT( date ,GETDATE()) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
                  appfacturas_det.numeros = saacxc.numerod) else saacxc.fechae end))as DiasTrans,
                DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
                UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, (saacxc.saldo/SAFACT.Tasa) as SaldoPendolar,
                (select Coordinador from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
                from saacxc inner join saclie on saacxc.codclie = saclie.codclie inner join SAFACT on SAFACT.NumeroD= SAACXC.NumeroD 
                where (DATEADD(dd, 0, DATEDIFF(dd, 0, SAACXC.FechaE)) between DATEADD(day, -15, CONVERT( date ,GETDATE())) and DATEADD(day, -8, CONVERT( date ,GETDATE()))) 
                and saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') and SAACXC.CodVend='$vendedor'
                order by saacxc.FechaE asc";
          
      break;
      
      
      case 3:

        $sql = "SELECT (case when saacxc.tipocxc = 10 then 'FACT' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
                CONVERT( date , saacxc.fechae ) as FechaEmi, 
                (case when saacxc.tipocxc = 10 then (select CONVERT( VARCHAR ,fechad,103) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
                  appfacturas_det.numeros = saacxc.numerod) else 'N/A' end) as FechaDesp,
                  DATEDIFF(DD, saacxc.fechae, (case when saacxc.tipocxc = 10 then (select CONVERT( date ,GETDATE()) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
                  appfacturas_det.numeros = saacxc.numerod) else saacxc.fechae end))as DiasTrans,
                DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
                UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, (saacxc.saldo/SAFACT.Tasa) as SaldoPendolar,
                (select Coordinador from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
                from saacxc inner join saclie on saacxc.codclie = saclie.codclie inner join SAFACT on SAFACT.NumeroD= SAACXC.NumeroD 
                where (DATEADD(dd, 0, DATEDIFF(dd, 0, SAACXC.FechaE)) between DATEADD(day, -40, CONVERT( date ,GETDATE())) and DATEADD(day, -16, CONVERT( date ,GETDATE()))) and saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') 
                and SAACXC.CodVend='$vendedor'
                order by saacxc.FechaE asc";
          
      break;
        
        
      case 4:

        $sql = "SELECT (case when saacxc.tipocxc = 10 then 'FACT' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
        CONVERT( date , saacxc.fechae ) as FechaEmi, 
        (case when saacxc.tipocxc = 10 then (select CONVERT( VARCHAR ,fechad,103) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
          appfacturas_det.numeros = saacxc.numerod) else 'N/A' end) as FechaDesp,
          DATEDIFF(DD, saacxc.fechae, (case when saacxc.tipocxc = 10 then (select CONVERT( date ,GETDATE()) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
          appfacturas_det.numeros = saacxc.numerod) else saacxc.fechae end))as DiasTrans,
        DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
         UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, (saacxc.saldo/SAFACT.Tasa) as SaldoPendolar,
         (select Coordinador from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
         from saacxc inner join saclie on saacxc.codclie = saclie.codclie inner join SAFACT on SAFACT.NumeroD= SAACXC.NumeroD 
         where (SAACXC.FechaE < DATEADD(day, -40, CONVERT( date ,GETDATE()))) and saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') 
         and SAACXC.CodVend='$vendedor'
         order by saacxc.FechaE asc";
  
          
      break;
         
         
      case 5:

        $sql = "SELECT (case when saacxc.tipocxc = 10 then 'FACT' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
                CONVERT( date , saacxc.fechae ) as FechaEmi, 
                (case when saacxc.tipocxc = 10 then (select CONVERT( VARCHAR ,fechad,103) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
                  appfacturas_det.numeros = saacxc.numerod) else 'N/A' end) as FechaDesp,
                  DATEDIFF(DD, saacxc.fechae, (case when saacxc.tipocxc = 10 then (select CONVERT( date ,GETDATE()) from appfacturas inner join appfacturas_det on appfacturas.correl = appfacturas_det.correl where 
                  appfacturas_det.numeros = saacxc.numerod) else saacxc.fechae end))as DiasTrans,
                DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
                UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend,  (saacxc.saldo/SAFACT.Tasa) as SaldoPendolar,
                (select Coordinador from SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
                from saacxc inner join saclie on saacxc.codclie = saclie.codclie inner join SAFACT on SAFACT.NumeroD= SAACXC.NumeroD 
                where saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') and SAACXC.CodVend='$vendedor'
                order by saacxc.FechaE asc";
  
          
      break;
  }

    //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
   $sql = $conectar->prepare($sql);
   $sql->execute();
   $result = $sql->fetchAll(PDO::FETCH_ASSOC);
  
   return $result ;
}


}