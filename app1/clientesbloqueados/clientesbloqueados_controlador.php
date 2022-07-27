<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("clientesbloqueados_modelo.php");

//INSTANCIAMOS EL MODELO
$clientesbloqueados = new Clientesbloqueados();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_clientesbloqueados":

    $datos = $clientesbloqueados->ClientesBloqueadosPorVendedor($_POST["vendedor"]);

    //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
    $data = Array();


    foreach ($datos as $row) {
        //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();

        if ($row['escredito'] == 1) {
            $estado = "SOLVENTE";
        } else {
            $estado = "BLOQUEADO: " . utf8_encode($row['observa']);
        }

        $sub_array[] = $row["codclie"];
        $sub_array[] = $row["descrip"];
        $sub_array[] = $row["id3"];
        $sub_array[] = $row["direc1"];
        $sub_array[] = $estado;
        $sub_array[] = $row["diasvisita"];

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

    case "listar_vendedores":

        $output['lista_vendedores'] = Vendedores::todos();

        echo json_encode($output);
        break;

}
