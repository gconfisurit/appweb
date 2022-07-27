
<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class NExcobrar extends Conectar{


		public function getNEporcobrar($vendedor){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
       $conectar= parent::conexion2();
       parent::set_names();

        //QUERY
		 if($vendedor != 'Todos'){
          $sql = "SELECT 
          (select Descrip from aj_d.dbo.SACONF) as Empresa,
          SUM(case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=0 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=7) then SAACXC.Saldo else 0 end) as Total_0_a_7_Dias,
          SUM(case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=8 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=15) then SAACXC.Saldo else 0 end) as Total_8_a_15_Dias,
          SUM(case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=16 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=40) then SAACXC.Saldo else 0 end) as Total_16_a_40_Dias,
          SUM(case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>40) then SAACXC.Saldo else 0 end) as Total_Mayor_a_40_Dias,
          SUM(SAACXC.Saldo) as SubTotal
           from aj_d.dbo.saacxc inner join aj_d.dbo.saclie on saacxc.codclie = saclie.codclie 
           where saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') AND saacxc.CodVend='$vendedor'" ;
		 }else {
			$sql = "SELECT 
			(select Descrip from aj_d.dbo.SACONF) as Empresa,
			SUM(case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=0 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=7) then SAACXC.Saldo else 0 end) as Total_0_a_7_Dias,
			SUM(case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=8 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=15) then SAACXC.Saldo else 0 end) as Total_8_a_15_Dias,
			SUM(case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=16 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=40) then SAACXC.Saldo else 0 end) as Total_16_a_40_Dias,
			SUM(case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>40) then SAACXC.Saldo else 0 end) as Total_Mayor_a_40_Dias,
			SUM(SAACXC.Saldo) as SubTotal
			 from aj_d.dbo.saacxc inner join aj_d.dbo.saclie on saacxc.codclie = saclie.codclie 
			 where saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20')" ;
		 }

        


        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
       $sql = $conectar->prepare($sql);
       $sql->execute();
       $result = $sql->fetchAll(PDO::FETCH_ASSOC);
      
       return $result ;
   }

   public function getdetallesNEporcobrar($vendedor, $data){
      //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
      //CUANDO ES APPWEB ES CONEXION.
     $conectar= parent::conexion2();
     parent::set_names();
  
      //QUERY
  
      switch ($data) {
  
        case 1:
          if ($vendedor != 'Todos') {
            $sql = "SELECT (case when saacxc.tipocxc = 10 then 'NE' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
            CONVERT( date , saacxc.fechae ) as FechaEmi, 
            DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
             UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, 
             (select Coordinador from aj_d.dbo.SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
             from aj_d.dbo.saacxc inner join saclie on saacxc.codclie = saclie.codclie 
             where (DATEADD(dd, 0, DATEDIFF(dd, 0, SAACXC.FechaE)) between DATEADD(day, -7, CONVERT( date ,GETDATE())) and GETDATE()) and saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') AND saacxc.CodVend='$vendedor' 
             order by saacxc.FechaE asc";

             } else {
            $sql = "SELECT (case when saacxc.tipocxc = 10 then 'NE' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
            CONVERT( date , saacxc.fechae ) as FechaEmi, 
            DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
             UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, 
             (select Coordinador from aj_d.dbo.SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
             from aj_d.dbo.saacxc inner join saclie on saacxc.codclie = saclie.codclie 
             where (DATEADD(dd, 0, DATEDIFF(dd, 0, SAACXC.FechaE)) between DATEADD(day, -7, CONVERT( date ,GETDATE())) and GETDATE()) and saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') 
             order by saacxc.FechaE asc";

             }
            
        break;
    
    
        case 2:
          if ($vendedor != 'Todos') {
            $sql = "SELECT (case when saacxc.tipocxc = 10 then 'NE' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
          CONVERT( date , saacxc.fechae ) as FechaEmi, 
          DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
           UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, 
           (select Coordinador from aj_d.dbo.SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
           from aj_d.dbo.saacxc inner join saclie on saacxc.codclie = saclie.codclie 
           where (DATEADD(dd, 0, DATEDIFF(dd, 0, SAACXC.FechaE)) between DATEADD(day, -15, CONVERT( date ,GETDATE())) and DATEADD(day, -8, CONVERT( date ,GETDATE()))) 
           and saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') AND saacxc.CodVend='$vendedor' 
           order by saacxc.FechaE asc";
           
          } else {
            $sql = "SELECT (case when saacxc.tipocxc = 10 then 'NE' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
            CONVERT( date , saacxc.fechae ) as FechaEmi, 
            DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
            UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, 
            (select Coordinador from aj_d.dbo.SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
            from aj_d.dbo.saacxc inner join saclie on saacxc.codclie = saclie.codclie 
            where (DATEADD(dd, 0, DATEDIFF(dd, 0, SAACXC.FechaE)) between DATEADD(day, -15, CONVERT( date ,GETDATE())) and DATEADD(day, -8, CONVERT( date ,GETDATE()))) 
            and saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') 
            order by saacxc.FechaE asc";
          }

        break;
        
        
        case 3:
          if ($vendedor != 'Todos') {
            $sql = "SELECT (case when saacxc.tipocxc = 10 then 'NE' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
          CONVERT( date , saacxc.fechae ) as FechaEmi, 
          DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
           UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, 
           (select Coordinador from aj_d.dbo.SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
           from aj_d.dbo.saacxc inner join saclie on saacxc.codclie = saclie.codclie 
           where (DATEADD(dd, 0, DATEDIFF(dd, 0, SAACXC.FechaE)) between DATEADD(day, -40, CONVERT( date ,GETDATE())) and DATEADD(day, -16, CONVERT( date ,GETDATE()))) and saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') AND saacxc.CodVend='$vendedor' 
           order by saacxc.FechaE asc";
          } else {
            $sql = "SELECT (case when saacxc.tipocxc = 10 then 'NE' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
            CONVERT( date , saacxc.fechae ) as FechaEmi, 
            DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
            UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, 
            (select Coordinador from aj_d.dbo.SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
            from aj_d.dbo.saacxc inner join saclie on saacxc.codclie = saclie.codclie 
            where (DATEADD(dd, 0, DATEDIFF(dd, 0, SAACXC.FechaE)) between DATEADD(day, -40, CONVERT( date ,GETDATE())) and DATEADD(day, -16, CONVERT( date ,GETDATE()))) and saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') 
            order by saacxc.FechaE asc";
          }
            
        break;
          
          
        case 4:
          if ($vendedor != 'Todos') {
            $sql = "SELECT (case when saacxc.tipocxc = 10 then 'NE' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
            CONVERT( date , saacxc.fechae ) as FechaEmi, 
            DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
            UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, 
            (select Coordinador from aj_d.dbo.SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
            from aj_d.dbo.saacxc inner join saclie on saacxc.codclie = saclie.codclie 
            where (SAACXC.FechaE < DATEADD(day, -40, CONVERT( date ,GETDATE()))) and saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') AND saacxc.CodVend='$vendedor' 
            order by saacxc.FechaE asc";
          } else {
            $sql = "SELECT (case when saacxc.tipocxc = 10 then 'NE' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
            CONVERT( date , saacxc.fechae ) as FechaEmi, 
            DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy,
            UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, 
            (select Coordinador from aj_d.dbo.SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
            from aj_d.dbo.saacxc inner join saclie on saacxc.codclie = saclie.codclie 
            where (SAACXC.FechaE < DATEADD(day, -40, CONVERT( date ,GETDATE()))) and saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') 
            order by saacxc.FechaE asc";
          }   
            
        break;
           
           
        case 5:
          if($vendedor != 'Todos'){
            $sql = "SELECT (case when saacxc.tipocxc = 10 then 'NE' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
          CONVERT( date , saacxc.fechae ) as FechaEmi, DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy, UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, 
          (case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=0 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=7) then SAACXC.Saldo else 0 end) as Total_0_a_7_Dias,
          (case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=8 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=15) then SAACXC.Saldo else 0 end) as Total_8_a_15_Dias,
          (case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=16 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=40) then SAACXC.Saldo else 0 end) as Total_16_a_40_Dias,
          (case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>40) then SAACXC.Saldo else 0 end) as Total_Mayor_a_40_Dias,
          (select Coordinador from aj_d.dbo.SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
          from aj_d.dbo.saacxc inner join saclie on saacxc.codclie = saclie.codclie 
          where saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') AND saacxc.CodVend='$vendedor'
          order by saacxc.FechaE asc";
          }else {
            $sql = "SELECT (case when saacxc.tipocxc = 10 then 'NE' else 'N/D' end) as TipoOpe, saacxc.numerod as NroDoc, saclie.CodClie as CodClie, saclie.Descrip as Cliente, 
          CONVERT( date , saacxc.fechae ) as FechaEmi, DATEDIFF(DD, saacxc.fechae, CONVERT( date ,GETDATE()))as DiasTransHoy, UPPER(saacxc.codvend) as Ruta, saacxc.saldo as SaldoPend, 
          (case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=0 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=7) then SAACXC.Saldo else 0 end) as Total_0_a_7_Dias,
          (case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=8 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=15) then SAACXC.Saldo else 0 end) as Total_8_a_15_Dias,
          (case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>=16 and DATEDIFF(DD, SAACXC.FechaE, GETDATE())<=40) then SAACXC.Saldo else 0 end) as Total_16_a_40_Dias,
          (case when (DATEDIFF(DD, SAACXC.FechaE, GETDATE())>40) then SAACXC.Saldo else 0 end) as Total_Mayor_a_40_Dias,
          (select Coordinador from aj_d.dbo.SAVEND_02 where SAVEND_02.CodVend = saacxc.CodVend) as Supervisor
          from aj_d.dbo.saacxc inner join saclie on saacxc.codclie = saclie.codclie 
          where saacxc.saldo>0 AND (saacxc.tipocxc='10' OR saacxc.tipocxc='20') 
          order by saacxc.FechaE asc";
          }
          
        break;
    }
  
      //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
     $sql = $conectar->prepare($sql);
     $sql->execute();
     $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    
     return $result ;
  }


}
