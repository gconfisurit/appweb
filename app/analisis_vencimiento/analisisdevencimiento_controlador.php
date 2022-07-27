<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("analisisdevencimiento_modelo.php");

//INSTANCIAMOS EL MODELO
$analisis = new analisisdevencimiento();
$proveedores = new analisisdevencimiento();
//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_analisis":

    $datos = $analisis->getanalisisdevencimiento( $_POST["fechai"], $_POST["fechaf"], $_POST["proveedor"]);

    $dias = $analisis->dias_transcurridos( $_POST["fechai"], $_POST["fechaf"]);

    //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
    $data = Array();

    foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();

        $fecha_E = date('d/m/Y', strtotime($row["FechaE"]));
        $fecha_V = date('d/m/Y', strtotime($row["FechaV"]));
        $Montonew = number_format($row["Monto"], 2, ',', '.');
        
        $sub_array[] = $row["CodProv"];
        $sub_array[] = $row["Descrip"];
        $sub_array[] = $row["NumeroD"];
        $sub_array[] = $fecha_E;
        $sub_array[] = $fecha_V;
        $sub_array[] = $dias;
        $sub_array[] = $Montonew;

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

    case "listar_proveedores":

        $output['lista_proveedores'] = Proveedores::todos();

        echo json_encode($output);
        break;
}