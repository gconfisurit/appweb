<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("cliente_modelo.php");

//INSTANCIAMOS EL MODELO
$clientesnoactivos = new Clientestodos();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_cliente":

        $vendedor = $_POST["vendedor"];
        $total = 0;
        $activos = 0;
        $inactivos = 0;

        if($vendedor == "Todos"){ 

                $datos = Vendedores::todos();

                //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
                $data = Array();

                foreach ($datos as $row) {
                    //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();
                    
                    $extra = $clientesnoactivos->getClientesTODOS($row["CodVend"]);

                    $sub_array[] = $row["CodVend"];
                    $sub_array[] = $row["Descrip"];
                    $sub_array[] = $extra[0]["cliente_activo"];
                    $sub_array[] = $extra[0]["cliente_inactivo"];
                    $sub_array[] = ( $extra[0]["cliente_activo"] + $extra[0]["cliente_inactivo"] );
                    $total = $total + ( $extra[0]["cliente_activo"] + $extra[0]["cliente_inactivo"] );
                    $activos = ( $activos + $extra[0]["cliente_activo"]);
                    $inactivos = ( $inactivos + $extra[0]["cliente_inactivo"] );
                    $data[] = $sub_array;

                }
        }
        else{


            $datos = Vendedores::getVendedor($vendedor);

            //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
            $data = Array();

            foreach ($datos as $row) {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

                $extra = $clientesnoactivos->getClientesTODOS($vendedor);

                $sub_array[] = $row["CodVend"];
                $sub_array[] = $row["Descrip"];
                $sub_array[] = $extra[0]["cliente_activo"];
                $sub_array[] = $extra[0]["cliente_inactivo"];
                $sub_array[] = ( $extra[0]["cliente_activo"] + $extra[0]["cliente_inactivo"] );
                $total = ( $extra[0]["cliente_activo"] + $extra[0]["cliente_inactivo"] );
                $activos = ( $extra[0]["cliente_activo"]);
                $inactivos = ( $extra[0]["cliente_inactivo"] );
                $data[] = $sub_array;

            }

        }
         //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $results = array(
        "sEcho" => 1, //INFORMACION PARA EL DATATABLE
        "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
        "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
        'Mtototal' => $total,
        'activos' => $activos,
        'inactivos' => $inactivos,
        "aaData" => $data);


    echo json_encode($results);
    break;

    case "listar_vendedores":

        $output['lista_vendedores'] = Vendedores::todos();

        echo json_encode($output);
        break;
}
