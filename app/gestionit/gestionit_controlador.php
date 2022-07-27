<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
session_start();
//llamar a la conexion de la base de datos
require_once("../../config/conexion.php");

//llamar a el modelo Usuarios
require_once("gestionit_modelo.php");

$gestionit = new GestionIt();

switch ($_GET["op"]) {

    case "listar_modulos":
        $datos = Modulos::todos();

        //declaramos el array
        $data = array();

        foreach ($datos as $key => $row) {
            $sub_array = array();

            //ESTADO
            $est = '';
            $atrib = "btn btn-success btn-sm estado";
            switch ($row["estatus"]) {
                case 0:
                    $est = 'INACTIVO';
                    $atrib = "btn btn-warning btn-sm estado";
                    break;
                case 1:
                    $est = 'ACTIVO';
                    break;
            }

            $sub_array[] = '/'.$row["ruta"];
            $sub_array[] = '<div align="text-center">
								<div id="menu'.$key.'_div" class="input-group">
									<select id="menu'.$key.'" name="menu'.$key.'" class="form-control custom-select" onchange="guardarMenuSeleccionado(\''. $row["id"] .'\',\''. $key .'\',\'modulo\')">
										'.Functions::selectListMenus($row["menu_id"]).'
									</select>
								</div>
							</div>';
            $sub_array[] = $row["nombre"];
            $sub_array[] = '<i class="'.$row["icono"].'"></i> ' . $row["icono"];
            $sub_array[] = '<div class="col text-center">
                                <button type="button" onClick="cambiarEstado_modulo(\'' . $row["id"] . '\',\'' . $row["estatus"] . '\');" name="estado" id="' . $row["id"] . '" class="' . $atrib . '">' . $est . '</button>' . " " . '
                                <button type="button" onClick="mostrar_modulo(\'' . $row["id"] . '\');"  id="' . $row["id"] . '" class="btn btn-info btn-sm update">Editar</button>' . " " . '
                                <button type="button" onClick="eliminar_modulo(\'' . $row["id"] . '\',\''. $row["nombre"] . '\');"  id="' . $row["id"] . '" class="btn btn-danger btn-sm eliminar">Eliminar</button>
                            </div>';

            $data[] = $sub_array;
        }

        $results = array(
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data
        );

        echo json_encode($results);
        break;

    case "mostrar_modulo":
        $output=array();
        $id_modulo = $_POST["id_modulo"];
        $output['lista_menus'] = Menu::todos();
        $output['lista_modulos'] = Functions::listModulesAvailable($id_modulo);

        if($id_modulo != -1){
            //el parametro id_usuario se envia por AJAX cuando se edita el usuario
            $datos = Modulos::getById($id_modulo);

            foreach ($datos as $row) {
                $output["id"] = $row["id"];
                $output["nombre"] = $row["nombre"];
                $output["icono"] = $row["icono"];
                $output["ruta"] = $row["ruta"];
                $output["menu_id"] = $row["menu_id"];
                $output["estatus"] = $row["estatus"];
            }
        }

        echo json_encode($output);
        break;

    case "guardaryeditar_modulo":
        $modulo = false;

        $id_modulo = $_POST['id_modulo'];

        $data = array(
            'id_modulo' => $id_modulo,
            'nombre'    => ucwords($_POST['nombre']),
            'icono'     => !empty($_POST['icono']) ? $_POST['icono'] : 'far fa-dot-circle',
            'ruta'      => $_POST['ruta'],
            'menu_id'   => $_POST['menu_id'],
            'estado'    => $_POST['estado'],
        );

        /*si el id no existe entonces lo registra
        importante: se debe poner el $_POST sino no funciona*/
        if (empty($id_modulo)) {

            /*verificamos si existe la cedula y correo en la base de datos, si ya existe un registro con la cedula o correo entonces no se registra el usuario*/
            $datos = Modulos::getByRoute($data["ruta"]);

            if (is_array($datos) == true and count($datos) == 0) {
                //no existe el usuario por lo tanto hacemos el registros
                $modulo = $gestionit->registrar_modulo($data);

            } else {
                /*   $errors[]="La cédula o el correo ya existe";*/
            }

        } else {
            /*si ya existe entonces editamos el usuario*/
            $modulo = $gestionit->editar_modulo($data);
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

    case "activarydesactivar_modulo":
        $id = $_POST["id"];
        $activo  = $_POST["est"];

        $datos = Modulos::getById($id);
        //valida el id
        if (is_array($datos) == true and count($datos) > 0) {
            //si esta activo(1) lo situamos cero(0), y viceversa
            ($activo == "0") ? $activo = 1 : $activo = 0;
            //edita el estado
            $gestionit->editar_estado_modulo($id, $activo);
            //evalua que se realizara el query
            ($estado) ? $output["mensaje"] = "Actualizacion realizada Exitosamente" : $output["mensaje"] = "Error al Actualizar";
        }

        echo json_encode($output);
        break;

    case "eliminar_modulo":
        $eliminar = false;
        $id = $_POST["id"];

        $modulo = Modulos::getById($id);
        if(is_array($modulo) == true and count($modulo) > 0) {
            $eliminar = $gestionit->eliminar_modulo($id);
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

    case 'guardarseleccionado':
        $guardar = false;
        $id = $_POST["id"];
        $tipo = $_POST["tipo"];
        $menu_id = $_POST["tipo_value"];

        switch ($tipo) {
            case 'modulo':
                $guardar = $gestionit->editar_menuid_en_modulo($id, $menu_id);
                break;
            case 'menu_padre':
                $guardar = $gestionit->editar_menuportipo_en_menu($id, $menu_id, $tipo);
                break;
            case 'menu_hijo':
                $guardar = $gestionit->editar_menuportipo_en_menu($id, $menu_id, $tipo);
                break;
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

    case 'datos_grafico_menus':
        $output = array(
            'title'    => 'Menú lateral',
            'name' 	   => 'menu principal',
            'children' => Functions::orgranigramaMenus(),
        );

        array_push($output['children'],
            array(
                'title' => 'Inicio',
                'name' => 'vista home'
            )
        );

        echo json_encode($output);
        break;

    case "listar_menu":
        $datos = Menu::todos();

        //declaramos el array
        $data = array();

        foreach ($datos as $key => $row) {
            $sub_array = array();

            //ESTADO
            $est = '';
            $atrib = "btn btn-success btn-sm estado";
            switch ($row["estatus"]) {
                case 0:
                    $est = 'INACTIVO';
                    $atrib = "btn btn-warning btn-sm estado";
                    break;
                case 1:
                    $est = 'ACTIVO';
                    break;
            }

            $sub_array[] = $row["nombre"];
            $sub_array[] = '<i class="'.$row["icono"].'"></i> ' . $row["icono"];
            $sub_array[] = '<div align="text-center">
								<div id="menu_padre'.$key.'_div" class="input-group">
									<select id="menu_padre'.$key.'" name="menu_padre'.$key.'" class="form-control custom-select" onchange="guardarMenuSeleccionado(\''. $row["id"] .'\',\''. $key .'\',\'menu_padre\')">
										'.Functions::selectListMenus($row["menu_padre"], true, $row['id']).'
									</select>
								</div>
							</div>';
            $sub_array[] = '<div align="text-center">
								<div id="menu_hijo'.$key.'_div" class="input-group">
									<select id="menu_hijo'.$key.'" name="menu_hijo'.$key.'" class="form-control custom-select" onchange="guardarMenuSeleccionado(\''. $row["id"] .'\',\''. $key .'\',\'menu_hijo\')">
										'.Functions::selectListMenus($row["menu_hijo"], true, $row['id']).'
									</select>
								</div>
							</div>';
            $sub_array[] = $row["menu_orden"];
            $sub_array[] = '<div class="col text-center">
                                <button type="button" onClick="cambiarEstado_menu(\'' . $row["id"] . '\',\'' . $row["estatus"] . '\');" name="estado" id="' . $row["id"] . '" class="' . $atrib . '">' . $est . '</button>' . " " . '
                                <button type="button" onClick="mostrar_menu(\'' . $row["id"] . '\');"  id="' . $row["id"] . '" class="btn btn-info btn-sm update">Editar</button>' . " " . '
                                <button type="button" onClick="eliminar_menu(\'' . $row["id"] . '\',\''. $row["nombre"] . '\');"  id="' . $row["id"] . '" class="btn btn-danger btn-sm eliminar">Eliminar</button>
                            </div>';

            $data[] = $sub_array;
        }

        $results = array(
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data
        );

        echo json_encode($results);
        break;

    case "mostrar_menu":
        $output=array();
        $id_menu = $_POST["id_menu"];
        $output['lista_menus'] = Menu::todos();

        if($id_menu != -1){
            //el parametro id_usuario se envia por AJAX cuando se edita el usuario
            $datos = Menu::getById($id_menu);

            foreach ($datos as $row) {
                $output["id"]           = $row["id"];
                $output["nombre"]       = $row["nombre"];
                $output["menu_orden"]   = $row["menu_orden"];
                $output["menu_padre"]   = $row["menu_padre"];
                $output["menu_hijo"]    = $row["menu_hijo"];
                $output["icono"]        = $row["icono"];
                $output["estatus"]      = $row["estatus"];
            }
        }

        echo json_encode($output);
        break;

    case "guardaryeditar_menu":
        $menu = false;

        $id_menu = $_POST['id_menu'];

        $data = array(
            'id_menu'       => $id_menu,
            'nombre'        => ucwords($_POST['nombre']),
            'menu_orden'    => $_POST['orden'],
            'menu_padre'    => $_POST['menu_padre'],
            'menu_hijo'     => $_POST['menu_hijo'],
            'icono'         => !empty($_POST['icono']) ? $_POST['icono'] : 'far fa-circle',
            'estado'        => $_POST['estado'],
        );

        /*si el id no existe entonces lo registra
        importante: se debe poner el $_POST sino no funciona*/
        if (empty($id_menu)) {
            $menu = $gestionit->registrar_menu($data);

        } else {
            /*si ya existe entonces editamos el usuario*/
            $menu = $gestionit->editar_menu($data);
        }

        //mensaje
        if($menu){
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

    case "activarydesactivar_menu":
        $id = $_POST["id"];
        $activo  = $_POST["est"];
        //los parametros id_usuario y est vienen por via ajax
        $datos = Menu::getById($id);
        //valida el id del usuario
        if (is_array($datos) == true and count($datos) > 0) {
            //si esta activo(1) lo situamos cero(0), y viceversa
            ($activo == "0") ? $activo = 1 : $activo = 0;
            //edita el estado
            $gestionit->editar_estado_menu($id, $activo);
            //evalua que se realizara el query
            ($estado) ? $output["mensaje"] = "Actualizacion realizada Exitosamente" : $output["mensaje"] = "Error al Actualizar";
        }

        echo json_encode($output);
        break;

    case "eliminar_menu":
        $eliminar = false;
        $id = $_POST["id"];

        $menu = Menu::getById($id);
        if(is_array($menu) == true and count($menu) > 0) {
            $eliminar = $gestionit->eliminar_menu($id);
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
