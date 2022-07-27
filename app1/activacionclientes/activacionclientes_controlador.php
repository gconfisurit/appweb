<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("activacionclientes_modelo.php");

//INSTANCIAMOS EL MODELO
$actclientes = new Activacionclientes();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_activacionclientes":

        $datos = $actclientes->lista_busca_activacionclientes($_POST["fecha_final"]);

            //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();
            $sub_array[] = date(FORMAT_DATE, strtotime($row["fechauv"]));
            $sub_array[] = $row["codclie"];
            $sub_array[] = $row["descrip"];
            $sub_array[] = $row["id3"];
            $sub_array[] = $row["codvend"];
            $sub_array[] = Strings::rdecimal($row["total"], 2);

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
