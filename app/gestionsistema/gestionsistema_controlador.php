<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
session_start();
//llamar a la conexion de la base de datos
require_once("../../config/conexion.php");

//llamar a el modelo Usuarios
require_once("gestionsistema_modelo.php");

$gestion = new Gestionsistema();

switch ($_GET["op"]) {

    case "listar_modulos_json":
        $datos = ConfigJson::get();

        //declaramos el array
        $data = array();

        foreach ($datos as $key => $row) {
            $sub_array = array();

            $sub_array[] = $key;
            $sub_array[] = count($row);
            $sub_array[] = '<div class="col text-center">
                                <button type="button" onClick="mostrar_parametros(\'' . $key . '\');"  id="' . $key . '" class="btn btn-info btn-sm update">Editar parámetros</button>' . " " . '
                                <button type="button" onClick="eliminar_modulo(\'' . $key . '\');"  id="' . $key . '" class="btn btn-danger btn-sm eliminar">Eliminar</button>
                            </div>';

            $data[] = $sub_array;
        }

        $output = array(
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data
        );

        echo json_encode($output);
        break;

    case "mostrar_parametros_modal":
        $i = 0;
        $name_modulo = $_POST["name_modulo"];
        $datos = ConfigJson::getParameters($name_modulo);

        //declaramos el array
        $data = array();

        foreach ($datos as $parametro => $value) {

            $i += 1;
            $sub_array = array();

            $sub_array[] = $parametro;
            $sub_array[] = '<div class="col text-center">
                                <input type="text" maxlength="15"
                                onchange="guardarParametro(\'' . $name_modulo . '\',\'' . $parametro . '\',\'' . $i . '\',\'' . "" . '\')" 
                                class="form-control input-sm"  
                                id="parametro_'.$i.'" 
                                name="parametro_'.$i.'" 
                                value="'. $value .'"
                                placeholder="Valor de parámetro">
                            </div>';
            $sub_array[] = '<div class="col text-center">
                                <button type="button" onClick="eliminar_parametro(\'' . $name_modulo . '\',\'' . $parametro . '\');"  id="' . $parametro . '" class="btn btn-danger btn-sm eliminar">Eliminar</button>
                            </div>';

            $data[] = $sub_array;
        }

        $output = array(
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data
        );

        echo json_encode($output);
        break;

    case "guardar_parametro_modal":
        $guardar = false;

        $name_modulo = $_POST["name_modulo"];
        $parameter = $_POST["parameter"];
        $value = $_POST["value"];

        $arr_data = ConfigJson::get();

        if (!empty($name_modulo) and !empty($parameter)) {

            # evalua el nuevo value
            if (strval($value) != '') {

                # si no esta vacio setea el parametro
                $arr_data[$name_modulo][$parameter] = $value;
            } else {

                #si esta vacio ingresa un nuevo parametro
                $arr_data[$name_modulo][$parameter] = '';
            }

            $guardar = ConfigJson::set($arr_data);
        }

        //mensaje
        if($guardar){
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

    case "eliminar_parametro_modal":
        $eliminar = false;
        $name_modulo = $_POST['name_modulo'];
        $parameter = $_POST['parameter'];
        $arr_data = ConfigJson::get();

        if (!empty($name_modulo)) {
            unset($arr_data[$name_modulo][$parameter]);
            $eliminar = ConfigJson::set($arr_data);
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

    case "mostrar_modulo":
        $output=array();
        $name_modulo = $_POST["name_modulo"];
        $output['lista_modulos'] = Functions::listModulesAvailableJson($name_modulo);

        if($name_modulo != -1){
            //el parametro name_usuario se envia por AJAX cuando se edita el usuario
            $datos = Modulos::getByName($name_modulo);

            foreach ($datos as $row) {
                $output["nombre"] = $row["nombre"];
            }
        }

        echo json_encode($output);
        break;

    case "guardar_modulo":
        $modulo = false;

        $name_modulo = $_POST['name_modulo'];
        $arr_data = ConfigJson::get();

        if (!empty($name_modulo)) {
            $arr_data[$name_modulo] = array();
            $modulo = ConfigJson::set($arr_data);
        }

        //mensaje
        if($modulo){
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

    case "eliminar_modulo":
        $eliminar = false;
        $name_modulo = $_POST['name_modulo'];
        $arr_data = ConfigJson::get();

        if (!empty($name_modulo)) {
            unset($arr_data[$name_modulo]);
            $eliminar = ConfigJson::set($arr_data);
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

    case "enviar_correo_error":
        $status_send = false;
        $mensaje = $_POST['message'];

        if (!empty($mensaje)) {
            # preparamos los datos a enviar
            $dataEmail = EmailData::DataErrorConexion(
                array(
                    'usuario' => $_SESSION['login'],
                    'mensaje' => $mensaje,
                )
            );

            # enviar correo
            $status_send = Email::send_email(
                $dataEmail['title'],
                $dataEmail['body'],
                $dataEmail['recipients'],
            );
        }

        $output["mensaje"] = ($status_send)
            ? "Correo de error enviado exitosamente"
            : "Error al enviar correo de error.";

        echo json_encode($output);
        break;
}

?>
