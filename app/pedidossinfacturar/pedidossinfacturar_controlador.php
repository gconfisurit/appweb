<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("pedidossinfacturar_modelo.php");

//INSTANCIAMOS EL MODELO
$pedsinfacturar = new Pedidossinfacturar();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_pedidossinfacturar":

        $data = array(
            'fechai' => $_POST["fechai"],
            'fechaf' => $_POST["fechaf"],
            'marca'  => $_POST["marca"]
        );

        $datos = $pedsinfacturar->getPedidos($data);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        foreach ($datos as $key => $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            $unidad = ($row['unidad'] == 1)
                ? Strings::titleFromJson('paquete')
                : Strings::titleFromJson('bulto');

            $sub_array[] = $key+1;
            $sub_array[] = date(FORMAT_DATE, strtotime($row["fechae"]));
            $sub_array[] = $row["marca"];
            $sub_array[] = $row["coditem"];
            $sub_array[] = $row["producto"];
            $sub_array[] = $row["cliente"];
            $sub_array[] = $unidad;
            $sub_array[] = Strings::rdecimal($row["cantidad"],0);
            $sub_array[] = Strings::rdecimal($row["total"],2);
            $sub_array[] = $row["ruta"];

            $data[] = $sub_array;
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data
        );

        echo json_encode($output);
        break;

    case "listar_marcas":

        $output["lista_marcas"] = Marcas::todos();

        echo json_encode($output);
        break;

}
