<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("proveedor_modelo.php");

//INSTANCIAMOS EL MODELO
$proveedores = new listarProveedores();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR. 
switch ($_GET["op"]) {

        case "listar":

            $datos = $proveedores->getlistaproveedores($_POST["orden"]);
        
            //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
            $data = Array();
            
            foreach ($datos as $row) {
                    //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();
        
                if ($row['Activo'] == 1) {
                    $estado = "ACTIVO";
                } else {
                    if ($row['Activo'] == 0) {
                        $estado = "INACTIVO";
                    }
                }
        
                $sub_array[] = $row["CodProv"];
                $sub_array[] = $row["proveedor"];
                $sub_array[] = $row["ID3"];
                $sub_array[] = $estado;
                $sub_array[] = $row["Direc1"];
                $sub_array[] = $row["Direc2"];
                $sub_array[] = $row["Descrip"];
                $sub_array[] = $row["Telef"];
                $sub_array[] = $row["Movil"];
                $sub_array[] = $row["Email"];
        
                $data[] = $sub_array;
            }
             //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
                 $results = array(
                "sEcho" => 1, //INFORMACION PARA EL DATATABLE
                "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
                "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
                "aaData" => $data);

                echo json_encode($results);
            break;
}