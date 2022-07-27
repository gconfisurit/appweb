<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("auditoriacomisioneskpi_modelo.php");

//INSTANCIAMOS EL MODELO
$auditoriacomisioneskpi = new Auditoriacomisioneskpi();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_auditoriacomisioneskpi":

        $datos = $auditoriacomisioneskpi->getauditoriacomisioneskpi($_POST["fechai"], $_POST["fechaf"], $_POST["vendedor"]);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            $campo = "";
            switch ($row["campo"]) {
                case 1:
                  $campo = "Cobranza 0 a 7 días";
                  break;
                case 2:
                  $campo = "Comisión 0 a 7 días";
                  break;
                case 3:
                  $campo = "Cobranza 8 a 14 días";
                  break;
                case 4:
                  $campo = "Comisión 8 a 14 días";
                  break;
                case 5:
                  $campo = "Cobranza 15 a 21 días";
                  break;
                case 6:
                  $campo = "Comisión 15 a 21 días";
                  break;
                case 7:
                  $campo = "Cobranza mayor a 21 días";
                  break;
                case 8:
                  $campo = "Activación de Clintes";
                  break;
                case 9:
                  $campo = "Efectividad de Facturación (EVA)";
                  break;
              }

            $sub_array[] = $campo;
            $sub_array[] = Strings::rdecimal($row["antes"]);
            $sub_array[] = Strings::rdecimal($row["despu"]);
            $sub_array[] = Strings::rdecimal($row["despu"]-$row["antes"], 2);
            $sub_array[] = utf8_encode($row["descrip"]);
            $sub_array[] = date(FORMAT_DATETIME2, strtotime($row["fechah"]));

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
