<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("maestroclientes_modelo.php");

//INSTANCIAMOS EL MODELO
$maestro = new Maestroclientes();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_maestroclientes":

        $datos = $maestro->getMaestro($_POST["vendedor"]);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            //ESTADO
            $est = '';
            $atrib = "btn btn-success btn-sm estado";
            if ($row["activo"] == 0) {
                $est = 'INACTIVO';
                $atrib = "btn btn-warning btn-sm estado";
            } else {
                if ($row["activo"] == 1) {
                    $est = 'ACTIVO';
                }
            }

            $sub_array[] = $row["codclie"];
            $sub_array[] = $row["descrip"];
            $sub_array[] = '<div class="col text-center">
                                <button type="button" onClick="#" name="estado" id="' . $row["codclie"] . '" class="' . $atrib . '">' . $est . '</button>' . " " . '
                            </div>';
            $sub_array[] = $row["codvend"];
            $sub_array[] = $row["Ruta_Alternativa"];
            $sub_array[] = $row["Ruta_Alternativa_2"];
            $sub_array[] = strtoupper($row["DiasVisita"]);
            $sub_array[] = $row["Direc1"].' '.$row["Direc2"];
            $sub_array[] = $row["CodNestle"];

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
