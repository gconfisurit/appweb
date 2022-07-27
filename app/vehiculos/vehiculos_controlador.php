<?php

//llamar a la conexion de la base de datos
require_once("../../config/conexion.php");

//llamar a el modelo vehiculo
require_once("vehiculos_modelo.php");

$vehiculo = new Vehiculos();

switch ($_GET["op"]) {

    case "guardaryeditar":
        $est_vehiculo = false;

        $id_vehiculo = $_POST["placa"];

        $data = array(
            'placa'         => strtoupper($_POST['placa']),
            'modelo'        =>strtoupper($_POST['modelo']),
            'capacidad'     => str_replace(".","", str_replace(",","", $_POST['capacidad'])),
            'volumen'       => $_POST["volumen"],
        );

        /*si el id no existe entonces lo registra
        importante: se debe poner el $_POST sino no funciona*/
        if (!empty($id_vehiculo)) {

            /*verificamos si existe la placa y correo en la base de datos, si ya existe un registro con la placa o correo entonces no se registra el usuario*/
            $datos = Vehiculo::getByRegistration($data["placa"]);

            if (is_array($datos) == true and count($datos) == 0) {
                //no existe el usuario por lo tanto hacemos el registros
                $est_vehiculo = $vehiculo->registrar_vehiculo($data);
            } else {

            }

        } else {
            /*si ya existe entonces editamos el usuario*/
            $est_vehiculo = $vehiculo->editar_vehiculo($data);
        }

        //mensaje
        if($est_vehiculo){
            $output = [
                "mensaje" => "Guardado con Exito!",
                "icono"   => "success"
            ];
        } else {
            $output = [
                "mensaje" => "Ocurrió un error al Guardar!",
                "icono"   => "error"
            ];
        }

        echo json_encode($output);
        break;

    case "mostrar":
        $output=array();

        //selecciona el id del usuario
        //el parametro id_vehiculo se envia por AJAX cuando se edita el usuario
        $datos = Vehiculo::getById($_POST["id_vehiculo"]);

        foreach ($datos as $row) {
            $output["id_vehiculo"] = $row["id"];
            $output["placa"] = $row["placa"];
            $output["modelo"] = $row["modelo"];
            $output["capacidad"] = $row["capacidad"];
            $output["volumen"] = $row["volumen"];
            $output["estado"] = $row["estado"];
        }

        echo json_encode($output);
        break;

    case "activarydesactivar":
        $id = $_POST["id"];
        $activo  = $_POST["est"];
        //los parametros id_vehiculo y est vienen por via ajax
        $datos = Vehiculo::getById($id);
        //valida el id del usuario
        if (is_array($datos) == true and count($datos) > 0) {
            //si esta activo(1) lo situamos cero(0), y viceversa
            ($activo == "0") ? $activo = 1 : $activo = 0;
            //edita el estado
            $vehiculo->editar_estado($id, $activo);
            //evalua que se realizara el query
            ($estado) ? $output["mensaje"] = "Actualizacion realizada Exitosamente" : $output["mensaje"] = "Error al Actualizar";
        }

        echo json_encode($output);
        break;

    case "listar":
        $datos = Vehiculo::todos();

        //declaramos el array
        $data = Array();

        foreach ($datos as $row) {
            $sub_array = array();

            //ESTADO
           /* $est = '';
            $atrib = "btn btn-success btn-sm estado";
            if ($row["estado"] == 0) {
                $est = 'INACTIVO';
                $atrib = "btn btn-warning btn-sm estado";
            } else {
                if ($row["estado"] == 1) {
                    $est = 'ACTIVO';
                }
            }*/


           // $Fecha_Registro = date('d/m/Y', strtotime($row['fecha_registro']));

            $sub_array[] = $row["placa"];
            $sub_array[] = $row["modelo"];
            $sub_array[] = $row["capacidad"];
            $sub_array[] = $row["volumen"];
           // $sub_array[] = $Fecha_Registro;
            $sub_array[] = '<div class="col text-center">
                                <button type="button" onClick="mostrar(\'' . $row["placa"] . '\');"  id="' . $row["placa"] . '" class="btn btn-info btn-sm update">Editar</button>' . " " . '
                                <button type="button" onClick="eliminar(\'' . $row["placa"] . '\',\'' . $row["modelo"] . '\');"  id="' . $row["placa"] . '" class="btn btn-danger btn-sm eliminar">Eliminar</button>
                            </div>';

            $data[] = $sub_array;
        }

        $results = array(
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data);

        echo json_encode($results);
        break;

    case "eliminar":
        $eliminar = false;
        $id = $_POST["id"];

        $datos = Vehiculo::getById($id);
        if(is_array($datos) == true and count($datos) > 0) {
            $eliminar = $vehiculo->eliminar_vehiculo($id);
        }

        //mensaje
        if($eliminar){
            $output = [
                "mensaje" => "Se eliminó exitosamente!",
                "icono"   => "success"
            ];
        } else {
            $output = [
                "mensaje" => "Ocurrió un error al eliminar!",
                "icono"   => "error"
            ];
        }

        echo json_encode($output);
        break;
}
?>
