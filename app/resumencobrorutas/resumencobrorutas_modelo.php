<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class resumencobrorutas extends Conectar{


	public function getcobros($fechai, $fechaf, $ruta, $tipo){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
       $conectar= parent::conexion2();
       parent::set_names();

        //QUERY
            if($tipo == 'B' and $ruta == 'Todos'){
              $sql = "SELECT EDV,
SUM(De_0_a_7_Dias) De_0_a_7_Dias, 
SUM(De_8_a_14_Dias) De_8_a_14_Dias, 
SUM(De_15_a_21_Dias) De_15_a_21_Dias,
SUM(De_22_a_31_Dias) De_22_a_31_Dias,
SUM(Mas_31_Dias) Mas_31_Dias,
SUM(Total) Total
FROM (
select 
c.CodVend EDV,
case when DATEDIFF(day, cxc.FechaE, p.FechaE) >= 0 and DATEDIFF(day, cxc.FechaE, p.FechaE) <= 7 then
p.Monto 
else 0 end De_0_a_7_Dias,

case when DATEDIFF(day, cxc.FechaE, p.FechaE) >= 8 and DATEDIFF(day, cxc.FechaE, p.FechaE) <= 14 then
p.Monto 
else 0 end De_8_a_14_Dias,

case when DATEDIFF(day, cxc.FechaE, p.FechaE) >= 15 and DATEDIFF(day, cxc.FechaE, p.FechaE) <= 21 then
p.Monto 
else 0 end De_15_a_21_Dias,

case when DATEDIFF(day, cxc.FechaE, p.FechaE) >= 22 and DATEDIFF(day, cxc.FechaE, p.FechaE) <= 31 then
p.Monto
else 0 end De_22_a_31_Dias,

case when DATEDIFF(day, cxc.FechaE, p.FechaE) >= 32 then
p.Monto  
else 0 end Mas_31_Dias,

p.Monto Total

from [AJ].[dbo].SAPAGCXC as p 
inner join  [AJ].[dbo].SAACXC as c on p.NroPpal = c.NroUnico 
inner join  [AJ].[dbo].SACLIE as cl on c.CodClie = cl.CodClie
left join  [AJ].[dbo].SAACXC as cxc on cxc.NumeroD = p.NumeroD and cxc.TipoCxc in ('10','20')
where DATEADD(dd, 0, DATEDIFF(dd, 0, p.FechaE))  between '$fechai' and '$fechaf' and p.TipoCxc in ('10','20') and c.TipoCxc in ('41')
) AS TOTAL 
GROUP BY EDV";
            }else{

                if($tipo == 'D' and $ruta == 'Todos'){
                   
$sql = "SELECT EDV,
SUM(De_0_a_7_Dias) De_0_a_7_Dias, 
SUM(De_8_a_14_Dias) De_8_a_14_Dias, 
SUM(De_15_a_21_Dias) De_15_a_21_Dias,
SUM(De_22_a_31_Dias) De_22_a_31_Dias,
SUM(Mas_31_Dias) Mas_31_Dias,
SUM(Total) Total
FROM (
select 
c.CodVend EDV,
case when DATEDIFF(day, cxc.FechaE, p.FechaE) >= 0 and DATEDIFF(day, cxc.FechaE, p.FechaE) <= 7 then
p.Monto 
else 0 end De_0_a_7_Dias,

case when DATEDIFF(day, cxc.FechaE, p.FechaE) >= 8 and DATEDIFF(day, cxc.FechaE, p.FechaE) <= 14 then
p.Monto 
else 0 end De_8_a_14_Dias,

case when DATEDIFF(day, cxc.FechaE, p.FechaE) >= 15 and DATEDIFF(day, cxc.FechaE, p.FechaE) <= 21 then
p.Monto 
else 0 end De_15_a_21_Dias,

case when DATEDIFF(day, cxc.FechaE, p.FechaE) >= 22 and DATEDIFF(day, cxc.FechaE, p.FechaE) <= 31 then
p.Monto
else 0 end De_22_a_31_Dias,

case when DATEDIFF(day, cxc.FechaE, p.FechaE) >= 32 then
p.Monto  
else 0 end Mas_31_Dias,

p.Monto Total

from [AJ_D].[dbo].SAPAGCXC as p 
inner join  [AJ_D].[dbo].SAACXC as c on p.NroPpal = c.NroUnico 
inner join  [AJ_D].[dbo].SACLIE as cl on c.CodClie = cl.CodClie
left join  [AJ_D].[dbo].SAACXC as cxc on cxc.NumeroD = p.NumeroD and cxc.TipoCxc in ('10','20')
where DATEADD(dd, 0, DATEDIFF(dd, 0, p.FechaE))  between '$fechai' and '$fechaf' and p.TipoCxc in ('10','20') and c.TipoCxc in ('41')
) AS TOTAL 
GROUP BY EDV";

                }else{

                    if($tipo == 'B' and $ruta != 'Todos'){
                        $sql = "SELECT EDV,
SUM(De_0_a_7_Dias) De_0_a_7_Dias, 
SUM(De_8_a_14_Dias) De_8_a_14_Dias, 
SUM(De_15_a_21_Dias) De_15_a_21_Dias,
SUM(De_22_a_31_Dias) De_22_a_31_Dias,
SUM(Mas_31_Dias) Mas_31_Dias,
SUM(Total) Total
FROM (
select 
c.CodVend EDV,
case when DATEDIFF(day, cxc.FechaE, p.FechaE) >= 0 and DATEDIFF(day, cxc.FechaE, p.FechaE) <= 7 then
p.Monto 
else 0 end De_0_a_7_Dias,

case when DATEDIFF(day, cxc.FechaE, p.FechaE) >= 8 and DATEDIFF(day, cxc.FechaE, p.FechaE) <= 14 then
p.Monto 
else 0 end De_8_a_14_Dias,

case when DATEDIFF(day, cxc.FechaE, p.FechaE) >= 15 and DATEDIFF(day, cxc.FechaE, p.FechaE) <= 21 then
p.Monto 
else 0 end De_15_a_21_Dias,

case when DATEDIFF(day, cxc.FechaE, p.FechaE) >= 22 and DATEDIFF(day, cxc.FechaE, p.FechaE) <= 31 then
p.Monto
else 0 end De_22_a_31_Dias,

case when DATEDIFF(day, cxc.FechaE, p.FechaE) >= 32 then
p.Monto  
else 0 end Mas_31_Dias,

p.Monto Total

from [AJ].[dbo].SAPAGCXC as p 
inner join  [AJ].[dbo].SAACXC as c on p.NroPpal = c.NroUnico 
inner join  [AJ].[dbo].SACLIE as cl on c.CodClie = cl.CodClie
left join  [AJ].[dbo].SAACXC as cxc on cxc.NumeroD = p.NumeroD and cxc.TipoCxc in ('10','20')
where DATEADD(dd, 0, DATEDIFF(dd, 0, p.FechaE))  between '$fechai' and '$fechaf' and p.TipoCxc in ('10','20') and c.TipoCxc in ('41') and c.codvend = '$ruta'
) AS TOTAL 
GROUP BY EDV";
                    }else{

                        if($tipo == 'D' and $ruta != 'Todos'){
                           
$sql = "SELECT EDV,
SUM(De_0_a_7_Dias) De_0_a_7_Dias, 
SUM(De_8_a_14_Dias) De_8_a_14_Dias, 
SUM(De_15_a_21_Dias) De_15_a_21_Dias,
SUM(De_22_a_31_Dias) De_22_a_31_Dias,
SUM(Mas_31_Dias) Mas_31_Dias,
SUM(Total) Total
FROM (
select 
c.CodVend EDV,
case when DATEDIFF(day, cxc.FechaE, p.FechaE) >= 0 and DATEDIFF(day, cxc.FechaE, p.FechaE) <= 7 then
p.Monto 
else 0 end De_0_a_7_Dias,

case when DATEDIFF(day, cxc.FechaE, p.FechaE) >= 8 and DATEDIFF(day, cxc.FechaE, p.FechaE) <= 14 then
p.Monto 
else 0 end De_8_a_14_Dias,

case when DATEDIFF(day, cxc.FechaE, p.FechaE) >= 15 and DATEDIFF(day, cxc.FechaE, p.FechaE) <= 21 then
p.Monto 
else 0 end De_15_a_21_Dias,

case when DATEDIFF(day, cxc.FechaE, p.FechaE) >= 22 and DATEDIFF(day, cxc.FechaE, p.FechaE) <= 31 then
p.Monto
else 0 end De_22_a_31_Dias,

case when DATEDIFF(day, cxc.FechaE, p.FechaE) >= 32 then
p.Monto  
else 0 end Mas_31_Dias,

p.Monto Total

from [AJ_D].[dbo].SAPAGCXC as p 
inner join  [AJ_D].[dbo].SAACXC as c on p.NroPpal = c.NroUnico 
inner join  [AJ_D].[dbo].SACLIE as cl on c.CodClie = cl.CodClie
left join  [AJ_D].[dbo].SAACXC as cxc on cxc.NumeroD = p.NumeroD and cxc.TipoCxc in ('10','20')
where DATEADD(dd, 0, DATEDIFF(dd, 0, p.FechaE))  between '$fechai' and '$fechaf' and p.TipoCxc in ('10','20') and c.TipoCxc in ('41') and c.codvend = '$ruta'
) AS TOTAL 
GROUP BY EDV";

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