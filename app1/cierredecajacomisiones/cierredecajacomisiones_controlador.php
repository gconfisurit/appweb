<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("cierredecajacomisiones_modelo.php");

//INSTANCIAMOS EL MODELO
$comisiones = new cierredecajacomisiones();
//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_comisiones":

    $datos = $comisiones->getcomision( $_POST["fechai"], $_POST["fechaf"],$_POST["ruta"],$_POST["tipo"]);

    //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
    $data = Array();
    $suma_monto=0;
    foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();

       

        $fecha_E = date('d/m/Y', strtotime($row["Emision"]));
        $fecha_P = date('d/m/Y', strtotime($row["Pagado"]));

        $total = number_format($row["Monto"], 2, ',', '.');

        $sub_array[] = $row["EDV"];
        $sub_array[] = $row["NroUnico"];
        $sub_array[] = $row["Ope"];
        $sub_array[] = $row["NumeroFac"];
        $sub_array[] =  $row["Emision"];
        $sub_array[] =  $row["Pagado"];
        $sub_array[] = $row["DiasTrans"];
        $sub_array[] = $row["Codclie"];
        $sub_array[] = $row["Descrip"];
        $sub_array[] = $total;
        
        $suma_monto += $row["Monto"];

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