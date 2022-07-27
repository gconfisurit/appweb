<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("motivonoventa_modelo.php");

//INSTANCIAMOS EL MODELO
$motivonoventa = new MotivoNoVenta();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_motivonoventa":

        $data = array(
            'edv'    => $_POST["vendedor"],
            'fechai' => $_POST["fechai"],
            'fechaf' => $_POST["fechaf"]
        );

        $datos = $motivonoventa->getMotivoNoVenta($data);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            $motivo = '';
            switch (intval($row["motivo"])) {
                case 1: $motivo = "Cliente Cerrado"; break;
                case 2: $motivo = "Cliente con Inventario"; break;
                case 3: $motivo = "Cliente a la espera de pedido anterior"; break;
                case 4: $motivo = "Cliente no visitado"; break;
                case 5: $motivo = "Cliente fuera de ruta"; break;
                case 6: $motivo = "Cliente con deuda y sin pago"; break;
                case 7: $motivo = "Cliente compra a la competencia"; break;
                case 8: $motivo = "Cliente considera altos los precios"; break;
            }

            $sub_array[] = date(FORMAT_DATE, strtotime($row['fecha']));
            $sub_array[] = $row["edv"];
            $sub_array[] = $row["codclie"];
            $sub_array[] = $row["descrip"];
            $sub_array[] = $motivo;

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
