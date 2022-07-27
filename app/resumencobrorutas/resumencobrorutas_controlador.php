<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("resumencobrorutas_modelo.php");

//INSTANCIAMOS EL MODELO
$cobros = new resumencobrorutas();
//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_cobros":

    $datos = $cobros->getcobros( $_POST["fechai"], $_POST["fechaf"],$_POST["ruta"],$_POST["tipo"]);

    //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
    $data = Array();
    $suma_monto=0;
    foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();

        $De_0_a_7_Dias = number_format($row["De_0_a_7_Dias"], 2, ',', '.');
        $De_8_a_14_Dias = number_format($row["De_8_a_14_Dias"], 2, ',', '.');
        $De_15_a_21_Dias = number_format($row["De_15_a_21_Dias"], 2, ',', '.');
        $De_22_a_31_Dias = number_format($row["De_22_a_31_Dias"], 2, ',', '.');
        $Mas_31_Dias = number_format($row["Mas_31_Dias"], 2, ',', '.');

        $total = number_format($row["Total"], 2, ',', '.');

        $sub_array[] = $row["EDV"];
        $sub_array[] = $De_0_a_7_Dias;
        $sub_array[] = $De_8_a_14_Dias;
        $sub_array[] = $De_15_a_21_Dias;
        $sub_array[] = $De_22_a_31_Dias;
        $sub_array[] = $Mas_31_Dias;
        $sub_array[] = $total;
        $suma_monto += $row["Total"];

        $data[] = $sub_array;

    }

    //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
    $results = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            'Mtototal' => Strings::rdecimal($suma_monto, 2),
            "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data);

    echo json_encode($results);
    break;

    case "listar_vendedores":

        $output['lista_vendedores'] = vendedores::todos();

        echo json_encode($output);
        break;

    
}