<?php
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class InventarioGlobal extends Conectar
{
    public function getInventarioGlobal($fechai, $fechaf, $alm=array())
    {
        $i = 0;
        $cond = $depo = "";
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

       /* if (count($alm) > 0) {
            $aux = "";
            //se contruye un string para listar los depositvos seleccionados
            //en caso que no haya ninguno, sera vacio
            foreach ($alm as $num)
                $aux .= " OR CodUbic = ?";

            //armamos una lista de los depositos, si no existe ninguno seleccionado no se considera para realizar la consulta
            $depo = "(" . substr($aux, 4, strlen($aux)) . ")";

            $cond = ($depo != "()")
                ? ("AND ".$depo)
                : "";
        }*/

                $calm=count($alm);
        if ($calm=='1'){
            $cond="and CodUbic='$alm[0]'";
            $cond1="CodUbic='$alm[0]'";
        }else if ($calm=='2'){
            $cond="and (CodUbic='$alm[0]' or CodUbic='$alm[1]')";
            $cond1="(CodUbic='$alm[0]' or CodUbic='$alm[1]')";
        }else if ($calm=='3'){
            $cond="and (CodUbic='$alm[0]' or CodUbic='$alm[1]' or CodUbic='$alm[2]')";
            $cond1="(CodUbic='$alm[0]' or CodUbic='$alm[1]' or CodUbic='$alm[2]')";
        }else if ($calm=='4'){
            $cond="and (CodUbic='$alm[0]' or CodUbic='$alm[1]' or CodUbic='$alm[2]' or CodUbic='$alm[3]')";
            $cond1="(CodUbic='$alm[0]' or CodUbic='$alm[1]' or CodUbic='$alm[2]' or CodUbic='$alm[3]')";
        }

        $sql = "select CodProd, Descrip, CantEmpaq,
    (SELECT isnull(sum(cantidad),0)+isnull(0,0) FROM SAITEMFAC where esunid='0' and CodProd=CodItem ".$cond." and numerod in (select fa.numerod from SAFACT as fa where TipoFac in ('A','C') and DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) between '$fechai' and '$fechaf' and (NumeroR is null or NumeroD in (select x.NumeroR from SAFACT as x where x.TipoFac = 'B' and x.NumeroR=fa.NumeroD and DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) between '$fechai' and '$fechaf' group by x.NumeroR having cast(sum(x.Monto) as int)<cast(fa.Monto as int))) and NumeroD not in (select numeros from appfacturas_det) and NumeroD not in (select numerof from sanota))) as bultosxdesp,
    (SELECT isnull(sum(cantidad),0)+isnull(0,0) FROM SAITEMFAC where esunid='1' and CodProd=CodItem ".$cond." and numerod in (select fa.numerod from SAFACT as fa where TipoFac in ('A','C') and DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) between '$fechai' and '$fechaf' and (NumeroR is null or NumeroD in (select x.NumeroR from SAFACT as x where x.TipoFac = 'B' and x.NumeroR=fa.NumeroD and DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) between '$fechai' and '$fechaf' group by x.NumeroR having cast(sum(x.Monto) as int)<cast(fa.Monto as int))) and NumeroD not in (select numeros from appfacturas_det) and NumeroD not in (select numerof from sanota))) as paqxdesp,

    (select isnull(sum(exunidad),0)+isnull(0,0) from SAEXIS where CodProd=SAPROD.CodProd ".$cond.") as exunid,
    (select isnull(sum(existen),0)+isnull(0,0) from SAEXIS where CodProd=SAPROD.CodProd ".$cond.") as exis,

    ((select isnull(sum(exunidad),0)+isnull(0,0) from SAEXIS where CodProd=SAPROD.CodProd ".$cond.")+
    (select isnull(sum(existen),0)+isnull(0,0) from SAEXIS where CodProd=SAPROD.CodProd ".$cond.")+
    (SELECT isnull(sum(cantidad),0)+isnull(0,0) FROM SAITEMFAC where esunid='0' and CodProd=CodItem ".$cond." and numerod in (select fa.numerod from SAFACT as fa where TipoFac in ('A','C') and DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) between '$fechai' and '$fechaf' and (NumeroR is null or NumeroD in (select x.NumeroR from SAFACT as x where x.TipoFac = 'B' and x.NumeroR=fa.NumeroD and DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) between '$fechai' and '$fechaf' group by x.NumeroR having cast(sum(x.Monto) as int)<cast(fa.Monto as int))) and NumeroD not in
    (select numeros from appfacturas_det) and NumeroD not in (select numerof from sanota))) +
    (SELECT isnull(sum(cantidad),0)+isnull(0,0) FROM SAITEMFAC where esunid='1' and CodProd=CodItem ".$cond." and numerod in (select fa.numerod from SAFACT as fa where TipoFac in ('A','C') and DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) between '$fechai' and '$fechaf' and (NumeroR is null or NumeroD in (select x.NumeroR from SAFACT as x where x.TipoFac = 'B' and x.NumeroR=fa.NumeroD and DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) between '$fechai' and '$fechaf' group by x.NumeroR having cast(sum(x.Monto) as int)<cast(fa.Monto as int))) and NumeroD not in
    (select numeros from appfacturas_det) and NumeroD not in (select numerof from sanota)))) as tt

    from SAPROD
    where CantEmpaq>0 group by CodProd, Descrip, CantEmpaq having
    ((select isnull(sum(exunidad),0)+isnull(0,0) from SAEXIS where CodProd=SAPROD.CodProd ".$cond.")+
    (select isnull(sum(existen),0)+isnull(0,0) from SAEXIS where CodProd=SAPROD.CodProd ".$cond.")+
    (SELECT isnull(sum(cantidad),0)+isnull(0,0) FROM SAITEMFAC where esunid='0' and CodProd=CodItem ".$cond." and numerod in (select fa.numerod from SAFACT as fa where TipoFac in ('A','C') and DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) between '$fechai' and '$fechaf' and (NumeroR is null or NumeroD in (select x.NumeroR from SAFACT as x where x.TipoFac = 'B' and x.NumeroR=fa.NumeroD and DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) between '$fechai' and '$fechaf' group by x.NumeroR having cast(sum(x.Monto) as int)<cast(fa.Monto as int))) and NumeroD not in (select numeros from appfacturas_det) and NumeroD not in (select numerof from sanota))) +
    (SELECT isnull(sum(cantidad),0)+isnull(0,0) FROM SAITEMFAC where esunid='1' and CodProd=CodItem ".$cond." and numerod in (select fa.numerod from SAFACT as fa where TipoFac in ('A','C') and DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) between '$fechai' and '$fechaf' and (NumeroR is null or NumeroD in (select x.NumeroR from SAFACT as x where x.TipoFac = 'B' and x.NumeroR=fa.NumeroD and DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) between '$fechai' and '$fechaf' group by x.NumeroR having cast(sum(x.Monto) as int)<cast(fa.Monto as int))) and NumeroD not in (select numeros from appfacturas_det) and NumeroD not in (select numerof from sanota)))) >0  order by CodProd";
        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        if ($depo != "()") {
            foreach ($alm AS $num)
                $sql->bindValue($i+=1, $num);
        }
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);

        if ($depo != "()") {
            foreach ($alm AS $num)
                $sql->bindValue($i+=1, $num);
        }
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);

        if ($depo != "()") {
            for($aux=1; $aux<=4; $aux++) {
                foreach ($alm as $num)
                    $sql->bindValue($i += 1, $num);
            }
        }

        if ($depo != "()") {
            foreach ($alm AS $num)
                $sql->bindValue($i+=1, $num);
        }
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);

        if ($depo != "()") {
            foreach ($alm AS $num)
                $sql->bindValue($i+=1, $num);
        }
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);

        if ($depo != "()") {
            for($aux=1; $aux<=2; $aux++) {
                foreach ($alm as $num)
                    $sql->bindValue($i += 1, $num);
            }
        }

        if ($depo != "()") {
            foreach ($alm AS $num)
                $sql->bindValue($i+=1, $num);
        }
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);

        if ($depo != "()") {
            foreach ($alm AS $num)
                $sql->bindValue($i+=1, $num);
        }
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);
        $sql->bindValue($i+=1, $fechai);
        $sql->bindValue($i+=1, $fechaf);

        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    public function facturasindespachar($fechai, $fechaf, $alm=array())
    {

        $conectar = parent::conexion2();
        parent::set_names();


        $calm=count($alm);
        if ($calm=='1'){
            $cond="and CodUbic='$alm[0]'";
            $cond1="CodUbic='$alm[0]'";
        }else if ($calm=='2'){
            $cond="and (CodUbic='$alm[0]' or CodUbic='$alm[1]')";
            $cond1="(CodUbic='$alm[0]' or CodUbic='$alm[1]')";
        }else if ($calm=='3'){
            $cond="and (CodUbic='$alm[0]' or CodUbic='$alm[1]' or CodUbic='$alm[2]')";
            $cond1="(CodUbic='$alm[0]' or CodUbic='$alm[1]' or CodUbic='$alm[2]')";
        }else if ($calm=='4'){
            $cond="and (CodUbic='$alm[0]' or CodUbic='$alm[1]' or CodUbic='$alm[2]' or CodUbic='$alm[3]')";
            $cond1="(CodUbic='$alm[0]' or CodUbic='$alm[1]' or CodUbic='$alm[2]' or CodUbic='$alm[3]')";
        }


    $sql=("select fa.descrip, fa.numerod, fa.NumeroR from SAFACT as fa where TipoFac in ('A','C') ".$cond." and DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) between '$fechai' and '$fechaf'
        and (NumeroR is null or NumeroD in (select x.NumeroR from SAFACT as x where x.TipoFac = 'B' and x.NumeroR=fa.NumeroD and DATEADD(dd, 0, DATEDIFF(dd, 0, FechaE)) between '$fechai' and '$fechaf' group by x.NumeroR having cast(sum(x.Monto) as int)<cast(fa.Monto as int)))
        and NumeroD not in (select numeros from appfacturas_det) and NumeroD not in (select numerof from sanota) order by FechaE asc");

    $sql = $conectar->prepare($sql);
    $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    }

}
