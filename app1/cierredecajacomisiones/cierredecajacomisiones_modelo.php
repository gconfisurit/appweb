<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class cierredecajacomisiones extends Conectar{


	public function getcomision($fechai, $fechaf, $ruta, $tipo){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
       $conectar= parent::conexion2();
       parent::set_names();

        //QUERY
            if($tipo === 'B' and $ruta === 'Todos'){
              $sql = "SELECT 
              c.CodVend EDV, 
              p.NroUnico,
              case 
              when p.TipoCxc = '10' then 'PAG'
              when p.TipoCxc = '31' then 'NDC'
              when p.TipoCxc = '41' then 'RET'
              when p.TipoCxc = '20' then 'PAG'
              end Ope, 
              p.NumeroD NumeroFac, 
              FORMAT(cxc.FechaE, 'dd/MM/yyyy') Emision,
              FORMAT(p.FechaE, 'dd/MM/yyyy') Pagado, 
              DATEDIFF(day, cxc.FechaE, p.FechaE) DiasTrans,
              cl.CodClie Codclie,
              cl.Descrip,
              p.Monto - isnull((select dev.Monto from SAACXC as dev where dev.NroUnico = p.NroPpal and dev.TipoCxc in ('10','20')),0) Monto
              from SAPAGCXC as p 
              inner join SAACXC as c on p.NroPpal = c.NroUnico 
              inner join SACLIE as cl on c.CodClie = cl.CodClie
              left join SAACXC as cxc on cxc.NumeroD = p.NumeroD and cxc.TipoCxc in ('10','20')
              where DATEADD(dd, 0, DATEDIFF(dd, 0, p.FechaE)) between '$fechai' and '$fechaf'and p.TipoCxc in ('10','20') and c.TipoCxc in ('41')
              order by p.FechaE DESC";
            }else{

                if($tipo === 'D' and $ruta === 'Todos'){
                    $sql = "SELECT 
                    c.CodVend EDV, 
                    p.NroUnico,
                    case 
                    when p.TipoCxc = '10' then 'PAG'
                    when p.TipoCxc = '31' then 'NDC'
                    when p.TipoCxc = '41' then 'RET'
                    when p.TipoCxc = '20' then 'PAG'
                    end Ope, 
                    p.NumeroD NumeroFac, 
                    FORMAT(cxc.FechaE, 'dd/MM/yyyy') Emision,
                    FORMAT(p.FechaE, 'dd/MM/yyyy') Pagado, 
                    DATEDIFF(day, cxc.FechaE, p.FechaE) DiasTrans,
                    cl.CodClie Codclie,
                    cl.Descrip,
                    p.Monto - isnull((select dev.Monto from [AJ_D].[dbo].SAACXC as dev where dev.NroUnico = p.NroPpal and dev.TipoCxc in ('10','20')),0) Monto
                    from [AJ_D].[dbo].SAPAGCXC as p 
                    inner join [AJ_D].[dbo].SAACXC as c on p.NroPpal = c.NroUnico 
                    inner join [AJ_D].[dbo].SACLIE as cl on c.CodClie = cl.CodClie
                    left join [AJ_D].[dbo].SAACXC as cxc on cxc.NumeroD = p.NumeroD and cxc.TipoCxc in ('10','20')
                    where DATEADD(dd, 0, DATEDIFF(dd, 0, p.FechaE)) between '$fechai' and '$fechaf' and p.TipoCxc in ('10','20') and c.TipoCxc in ('41')
                    order by p.FechaE DESC";
                }else{

                    if($tipo === 'B' and $ruta != 'Todos'){
                        $sql = "SELECT 
                        c.CodVend EDV, 
                        p.NroUnico,
                        case 
                        when p.TipoCxc = '10' then 'PAG'
                        when p.TipoCxc = '31' then 'NDC'
                        when p.TipoCxc = '41' then 'RET'
                        when p.TipoCxc = '20' then 'PAG'
                        end Ope, 
                        p.NumeroD NumeroFac, 
                        FORMAT(cxc.FechaE, 'dd/MM/yyyy') Emision,
                        FORMAT(p.FechaE, 'dd/MM/yyyy') Pagado, 
                        DATEDIFF(day, cxc.FechaE, p.FechaE) DiasTrans,
                        cl.CodClie Codclie,
                        cl.Descrip,
                        p.Monto - isnull((select dev.Monto from SAACXC as dev where dev.NroUnico = p.NroPpal and dev.TipoCxc in ('10','20')),0) Monto
                        from SAPAGCXC as p 
                        inner join SAACXC as c on p.NroPpal = c.NroUnico 
                        inner join SACLIE as cl on c.CodClie = cl.CodClie
                        left join SAACXC as cxc on cxc.NumeroD = p.NumeroD and cxc.TipoCxc in ('10','20')
                        where DATEADD(dd, 0, DATEDIFF(dd, 0, p.FechaE)) between '$fechai' and '$fechaf' and  c.CodVend = '$ruta' and p.TipoCxc in ('10','20') and c.TipoCxc in ('41')
                        order by p.FechaE DESC";
                    }else{

                        if($tipo === 'D' and $ruta != 'Todos'){
                            $sql = "SELECT 
                            c.CodVend EDV, 
                            p.NroUnico,
                            case 
                            when p.TipoCxc = '10' then 'PAG'
                            when p.TipoCxc = '31' then 'NDC'
                            when p.TipoCxc = '41' then 'RET'
                            when p.TipoCxc = '20' then 'PAG'
                            end Ope, 
                            p.NumeroD NumeroFac, 
                            FORMAT(cxc.FechaE, 'dd/MM/yyyy') Emision,
                            FORMAT(p.FechaE, 'dd/MM/yyyy') Pagado, 
                            DATEDIFF(day, cxc.FechaE, p.FechaE) DiasTrans,
                            cl.CodClie Codclie,
                            cl.Descrip,
                            p.Monto - isnull((select dev.Monto from [AJ_D].[dbo].SAACXC as dev where dev.NroUnico = p.NroPpal and dev.TipoCxc in ('10','20')),0) Monto
                            from [AJ_D].[dbo].SAPAGCXC as p 
                            inner join [AJ_D].[dbo].SAACXC as c on p.NroPpal = c.NroUnico 
                            inner join [AJ_D].[dbo].SACLIE as cl on c.CodClie = cl.CodClie
                            left join [AJ_D].[dbo].SAACXC as cxc on cxc.NumeroD = p.NumeroD and cxc.TipoCxc in ('10','20')
                            where DATEADD(dd, 0, DATEDIFF(dd, 0, p.FechaE)) between '$fechai' and '$fechaf' and  c.CodVend = '$ruta' and p.TipoCxc in ('10','20') and c.TipoCxc in ('41')
                            order by p.FechaE DESC";
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