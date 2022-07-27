<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("historicocostos_modelo.php");

//INSTANCIAMOS EL MODELO
$historico = new Historicocostos();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar":

        $fechai = $_POST['fechai'];
        $fechaf = $_POST['fechaf'];

        $datos = $historico->get_historicocostos_por_rango($fechai, $fechaf);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        foreach ($datos as $key=>$row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            $sub_array[] = $key+1;
            $sub_array[] = $row["codprod"];
            $sub_array[] = $row["descrip"];
            $sub_array[] = $row["marca"];
            $sub_array[] = date(FORMAT_DATE, strtotime($row['fechae']));
            $sub_array[] = Strings::rdecimal($row['costo'], 2);
            $sub_array[] = $row["cantidad"];

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
