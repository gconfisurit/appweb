<?php
 //LLAMAMOS A LA CONEXION.
require_once("../../config/conexion.php");

class listarProveedores extends Conectar{


	public function getlistaproveedores($total){

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
       $conectar= parent::conexion2();
       parent::set_names();

        //QUERY
     if(hash_equals("Todos", $total)) { //todos los proveedores
            $opc = 1;
       }else{
        if(hash_equals("Activos", $total)) { // proveedores Activos
            $opc = 2;
            }else{
                if(hash_equals("Inactivos", $total)) { // proveedores inactivos
                    $opc = 3;
                }
            }
       }

       switch ($total) {

           case "Todos":

                $sql= "SELECT CodProv , saprov.Descrip as proveedor , ID3 , Activo , Direc1 , Direc2 , saestado.Descrip , Telef , Movil , Email
                FROM saprov inner join saestado ON saprov.Estado = saestado.Estado ";

           break;

           case "Activos":
           
                $sql= "SELECT CodProv , saprov.Descrip as proveedor , ID3 , Activo , Direc1 , Direc2 , saestado.Descrip , Telef , Movil , Email
                FROM SAPROV inner join SAESTADO ON SAPROV.Estado = SAESTADO.Estado where Activo='1'";

           break;

           case "Inactivos":
           
            $sql= "SELECT  CodProv ,saprov. Descrip as proveedor , ID3 , Activo , Direc1 , Direc2 , saestado.Descrip , Telef , Movil , Email
            FROM SAPROV inner join SAESTADO ON SAPROV.Estado = SAESTADO.Estado where Activo='0' ";
             break;
       }

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
       $sql = $conectar->prepare($sql);
       $sql->execute();
       $result = $sql->fetchAll(PDO::FETCH_ASSOC);
      
       return $result ;
   }


}