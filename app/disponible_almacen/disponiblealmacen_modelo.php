<?php
set_time_limit(0);
//LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class disponiblealmacen extends Conectar
{
    public function getdisponiblealmacen($marca)
    {
        
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion2();
        parent::set_names();

        if($marca === 'Todos'){
            
            $sql ="SELECT  saprod.codprod, Descrip, marca, saexis.existen  Bultos,  saexis.exunidad Paquetes ,CodUbic
            from saprod inner join saexis on
            saprod.codprod = saexis.codprod where (saexis.existen > 0 or saexis.exunidad > 0) and saexis.CodUbic ='01' or saexis.CodUbic ='03'or saexis.CodUbic ='13'";
        }else{
            $sql ="SELECT  saprod.codprod, Descrip, marca, saexis.existen  Bultos,  saexis.exunidad Paquetes ,CodUbic
                     from saprod inner join saexis on
                     saprod.codprod = saexis.codprod where (saexis.existen > 0 or saexis.exunidad > 0) and saprod.marca ='$marca' and (saexis.CodUbic ='01' or saexis.CodUbic ='03'or saexis.CodUbic ='13') ";
        }

        $sql = $conectar->prepare($sql);
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);
      
       return $result ;
        
    }
}
