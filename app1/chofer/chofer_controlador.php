<?php

//llamar a la conexion de la base de datos
require_once("../../config/conexion.php");

//llamar a el modelo
require_once("chofer_modelo.php");

$chofer = new Chofer();

switch ($_GET["op"]) {

    case "activarydesactivar":
        $id = $_POST["id"];
        $activo  = $_POST["est"];
        //los parametros id_chofer y est vienen por via ajax
        $datos = Choferes::getByDni($id);
        //valida el id del usuario
        if (is_array($datos) == true and count($datos) > 0) {
            //si esta activo(1) lo situamos cero(0), y viceversa
            ($activo == "0") ? $activo = 1 : $activo = 0;
            //edita el estado
            $repuesta = $chofer->editar_estado($id, $activo);
            //evalua que se realizara el query
            ($estado) ? $output["mensaje"] = "Actualizacion realizada Exitosamente" : $output["mensaje"] = "Error al Actualizar";
        }

        echo json_encode($output);
        break;

    case "listar":

        $datos = Choferes::todos();
        //declaramos el array
        $data = array();
        foreach ($datos as $row) {

            $sub_array = array();

            //ESTADO
            $est = '';
            $atrib = "btn btn-success btn-sm estado";
            switch ($row["estatus"]){
                case 0:
                    $est = 'INACTIVO';
                    $atrib = "btn btn-warning btn-sm estado";
                    break;
                case 1:
                    $est = 'ACTIVO';
                    break;
            }

           // $Fecha_Registro = date('d/m/Y', strtotime($row['Fecha_Registro']));

            $sub_array[] = $row["cedula"];
            $sub_array[] = $row["descripcion"];
            //$sub_array[] = $Fecha_Registro;
            $sub_array[] = '<div class="col text-center">
                                <button type="button" onClick="cambiarEstado(\'' . $row["cedula"] . '\',\'' . $row["estatus"] . '\');" name="estado" id="' . $row["cedula"] . '" class="' . $atrib . '">' . $est . '</button>' . " " . '
                                <button type="button" onClick="mostrar(\'' . $row["cedula"] . '\');"  id="' . $row["cedula"] . '" class="btn btn-info btn-sm update">Editar</button>' . " " . '
                                <button type="button" onClick="eliminar(\'' . $row["cedula"] . '\',\'' . $row["descripcion"] . '\');"  id="' . $row["cedula"] . '" class="btn btn-danger btn-sm eliminar">Eliminar</button>
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

    case "guardaryeditar":
        $chofer_estatus = false;

        $id_chofer = $_POST["cedula"];

        $data = array(
            'cedula'        => $_POST["cedula"],
            'descripcion'        => $_POST['nomper'],
           // 'fecha_ingreso' => date('Y-m-d H:i:s'),
            'estatus'        => $_POST['estado'],
        );
        /* consulta si la variable id_chofer llego vacia para realizar
           operaciones de creacion o actualizacion  */
        if (!empty($id_chofer)) {
            /*verificamos si existe un registro en la base de datos, si ya existe un registro entonces no se registra*/
            $datos = Choferes::getByDni($id_chofer);
            if (is_array($datos) == true and count($datos) == 0) {
                //no existe, por lo tanto hacemos el registro
                $chofer_estatus = $chofer->registrar_chofer($data);

            }
        }  else {
            /*si ya existe entonces actualizamos */
            $chofer_estatus = $chofer->editar_chofer($data);
        }

        //mensaje
        if($chofer_estatus){
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
        $output = array();
        $id_chofer = $_POST["cedula"];

        $datos = Choferes::getByDni($id_chofer);

        if (is_array($datos) == true and count($datos) > 0) {
            $output["cedula"] = $datos[0]["Cedula"];
            $output["descripcion"] = $datos[0]["descripcion"];
           // $output["fecha_ingreso"] = $datos[0]["Fecha_Registro"];
            $output["estatus"] = $datos[0]["estatus"];
        }

        echo json_encode($output);
        break;

    case "eliminar":
        $eliminar = false;
        $id = $_POST["cedula"];

        $datos = Choferes::getByDni($id);
        if(is_array($datos) == true and count($datos) > 0) {
            $eliminar = $chofer->eliminar_chofer($id);
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

    case "listar_choferes":

        $output["lista_choferes"] = Choferes::todos();

        echo json_encode($output);

        break;

}
