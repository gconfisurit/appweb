<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("skunovendidos_modelo.php");

//INSTANCIAMOS EL MODELO
$sku = new Skunovendidos();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_skunovendidos":

        $data = array(
            'fechai' => $_POST["fechai"],
            'fechaf' => $_POST["fechaf"],
        );

        $datos = $sku->getnovendidos($data);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        foreach ($datos as $key => $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            $esunid = ($row["esunid"]=='1') ? 'PAQUETE' : 'BULTO';

            $sub_array[] = $key;
            $sub_array[] = $row["numerod"];
            $sub_array[] = $row["codvend"];
            $sub_array[] = $row["vendedor"];
            $sub_array[] = $row["codclie"];
            $sub_array[] = $row["cliente"];
            $sub_array[] = $row["coditem"];
            $sub_array[] = $row["descrip1"];
            $sub_array[] = $row["marca"];
            $sub_array[] = $esunid;
            $sub_array[] = Strings::rdecimal($row["cantidad"], 1);
            $sub_array[] = Strings::rdecimal($row["totalitem"], 2);
            $sub_array[] = Strings::rdecimal($row["bultos"], 2);
            $sub_array[] = Strings::rdecimal($row["paquetes"], 2);
            $sub_array[] = date('d-m-Y h:m A', strtotime($row['fechae']));

            $data[] = $sub_array;
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $results = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data
        );

        echo json_encode($results);
        break;

}
