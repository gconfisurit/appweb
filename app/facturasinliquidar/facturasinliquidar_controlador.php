<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("facturasinliquidar_modelo.php");

//INSTANCIAMOS EL MODELO
$facturas = new facturasinliquidar();
//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_facturasinliquidar":

    $datos = $facturas->getfacturasinliquidar( $_POST["fechai"], $_POST["fechaf"],$_POST["chofer"],$_POST["tipo"]);

    //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
    $data = Array();

    foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();

        if($_POST["tipo"]=='0'){
            $tipo='PENDIENTE';
        }else{
            $tipo='COBRADA';
        }

        $fecha_E = date('d/m/Y', strtotime($row["FechaEmi"]));
        $fecha_D = date('d/m/Y', strtotime($row["FechaDespacho"]));

        $total = number_format($row["MtoTotal"], 2, ',', '.');

        $sub_array[] = $row["Ruta"];
        $sub_array[] = $row["CodClie"];
        $sub_array[] = $row["Cliente"];
        $sub_array[] = $row["Chofer"];
        $sub_array[] = $row["Factura"];
        $sub_array[] = $fecha_E;
        $sub_array[] = $fecha_D;
        $sub_array[] = $total;
        $sub_array[] = $tipo;

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

    case "listar_chofer":

        $output['lista_chofer'] = Choferes::todos();

        echo json_encode($output);
        break;

    
}