<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO
require_once ("relacionclientesinactivos_modelo.php");
require_once("../relacionclientes/relacionclientes_modelo.php");

//INSTANCIAMOS EL MODELO
$relacion = new RelacionClientes();
$inactivos = new RelacionClientesInactivos();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "activarydesactivar":

        $codclie = $_POST["codclie"];
        $activo = $_POST["est"];
        //consultamos el registro del cliente
        $datos = $relacion->get_cliente_por_id($codclie);
        //valida el id del cliente
        if (is_array($datos) == true and count($datos) > 0) {
            //si esta activo(1) lo situamos cero(0), y viceversa
            ($activo == "0") ? $activo = 1 : $activo = 0;
            //edita el estado del cliente
            $estado = $relacion->editar_estado($codclie, $activo);
            //evalua que se realizara el query
            ($estado) ? $output["mensaje"] = "Actualizacion realizada Exitosamente" : $output["mensaje"] = "Error al Actualizar";
        }

        echo json_encode($output);

        break;


    case "listar":

        $datos = $inactivos->get_todos_los_clientes_inactivos();

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        foreach ($datos as $row){

            $sub_array = array();

            //ESTADO
            $est = '';
            $atrib = "btn btn-success btn-sm estado";
            switch ($row["idactivo"]){
                case 0:
                    $est = 'INACTIVO';
                    $atrib = "btn btn-warning btn-sm estado";
                    break;
                case 1:
                    $est = 'ACTIVO';
                    break;
            }

            $sub_array[] = $row["codclie"];
            $sub_array[] = $row["descrip"];
            $sub_array[] = $row["id3"];
            $sub_array[] = '<div class="col text-center"><button type="button" onClick="cambiarEstado(\''.$row["codclie"].'\',\''.$row["idactivo"].'\');" name="estado" id="' . $row["codclie"] . '" class="' . $atrib . '">' . $est . '</button></div>';

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