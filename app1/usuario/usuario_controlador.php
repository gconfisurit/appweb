<?php

//llamar a la conexion de la base de datos
require_once("../../config/conexion.php");

//llamar a el modelo Usuarios
require_once("Usuario_modelo.php");

$usuarios = new Usuario();

switch ($_GET["op"])
{
    case "guardaryeditar":
        $usuario = false;

        $id_usuario = $_POST['id_usuario'];

        $data = array(
            'id_usuario'=> $id_usuario,
            'cedula'    => $_POST['cedula'],
            'login'     => $_POST['login'],
            'nomper'    => $_POST['nomper'],
            'email'     => $_POST['email'],
            'clave'     => $_POST['clave'],
            'rol'       => $_POST['rol'],
            'estado'    => $_POST['estado'],
        );

        $rol_user_old = '';
        $is_new_user = false;
        /*si el id no existe entonces lo registra
        importante: se debe poner el $_POST sino no funciona*/
        if ( !Strings::avoidNullOrEmpty($id_usuario) ) {

            /*verificamos si existe la cedula y correo en la base de datos, si ya existe un registro con la cedula o correo entonces no se registra el usuario*/
            $datos = $usuarios->get_cedula_correo_del_usuario($data["cedula"], $data["email"]);

            if (is_array($datos) == true and count($datos) == 0) {
                //no existe el usuario por lo tanto hacemos el registros
                $usuario = $usuarios->registrar_usuario($data);
                if ($usuario) { $is_new_user = true; }
            } else {
                /*   $errors[]="La cédula o el correo ya existe";*/
            }

        } else {
            $rol_user_old = Usuarios::byDni($data['cedula'])['id_rol'];

            /*si ya existe entonces editamos el usuario*/
            $usuario = $usuarios->editar_usuario($data);
        }

        // si $usuario == true
        // registrara los permisos del rol al usuario
        if ($usuario) {
            $user_id = $data['cedula'];
            if ($is_new_user or (($rol_user_old != '') and ($data['rol'] != $rol_user_old))) {
                // evaluamos si tiene permisos, de ser verdadero los elimina de la base de datos
                $cantidad_permisos = count( Permisos::getPermisosPorUsuarioID($user_id) );
                if ($cantidad_permisos > 0)
                    Permisos::borrar_permiso_user($user_id);
                // registramos los permisos del rol seleccionado al usuario
                PermisosHelpers::registrarPermisoUsuarioPorRol([
                    'user_id' => $user_id,
                    'rol_id'  => $data['rol'],
                ]);
            }
        }

        //mensaje
        if($usuario){
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

        $output['lista_roles'] = Rol::todos();

        if($_POST["id_usuario"] != -1){
            //el parametro id_usuario se envia por AJAX cuando se edita el usuario
            $datos = Usuarios::byDni($_POST["id_usuario"]);

            if (ArraysHelpers::validate($datos)) {
                $output["cedula"] = $datos["cedula"];
                $output["login"] = $datos["login"];
                $output["nomper"] = $datos["nomper"];
                $output["email"] = $datos["email"];
//                $output["clave"] = $datos["clave"];
                $output["estado"] = $datos["estado"];
                $output["rol"] = $datos["id_rol"];
            }
        }

        echo json_encode($output);
        break;

    case "activarydesactivar":
        $id = $_POST["id"];
        $activo  = $_POST["est"];
        //los parametros id_usuario y est vienen por via ajax
        $datos = Usuarios::byDni($id);
        //valida el id del usuario
        if (ArraysHelpers::validate($datos)) {
            //si esta activo(1) lo situamos cero(0), y viceversa
            ($activo == "0") ? $activo = 1 : $activo = 0;
            //edita el estado
            $estado = $usuarios->editar_estado($id, $activo);
            //evalua que se realizara el query
            ($estado) ? $output["mensaje"] = "Actualizacion realizada Exitosamente" : $output["mensaje"] = "Error al Actualizar";
        }

        echo json_encode($output);
        break;

    case "listar":
        $datos = Usuarios::todos();

        //declaramos el array
        $data = array();

        foreach ($datos as $row) {
            $sub_array = array();

            //ESTADO
            $est = '';
            $atrib = "btn btn-success btn-sm estado";
            if ($row["estado"] == 0) {
                $est = 'INACTIVO';
                $atrib = "btn btn-warning btn-sm estado";
            } else {
                if ($row["estado"] == 1) {
                    $est = 'ACTIVO';
                }
            }
            //nivel del rol asignado
            $rol_data = Rol::getById($row["id_rol"]);

            $Fecha_Registro = date(FORMAT_DATE, strtotime($row['fecha_registro']));
            $Fecha_Ult_Ingreso = date(FORMAT_DATE, strtotime($row['fecha_ult_ingreso']));

            $sub_array[] = $row["cedula"];
            $sub_array[] = $row["login"];
            $sub_array[] = $row["nomper"];
            $sub_array[] = $row["email"];
            $sub_array[] = $rol_data["descripcion"];

            # el parametro t es el tipo:
            #        0 el tipo es rol
            #        1 el tipo es usuario (este caso)
            if (hash_equals("1", $row["cedula"])) {
                $sub_array[] = 'Permisos No Editables';
            } else {
                $sub_array[] = '<div align="center form-check-inline p-t-30">
                                    <a href="../permiso/permiso.php?&t='. 1 .'&i='. $row["cedula"] .'">Ver Permisos</a>
                                </div>';
            }
            $sub_array[] = $Fecha_Ult_Ingreso;
            if (hash_equals("1", $row["cedula"])) {
                $sub_array[] = '<div class="col text-center">
                                    <button type="button" onClick="mostrar(\'' . $row["cedula"] . '\');"  id="' . $row["cedula"] . '" class="btn btn-info btn-sm update">Editar</button>' . " " . '
                                </div>';
            } else {
                $sub_array[] = '<div class="col text-center">
                                    <button type="button" onClick="cambiarEstado(\'' . $row["cedula"] . '\',\'' . $row["estado"] . '\');" name="estado" id="' . $row["cedula"] . '" class="' . $atrib . '">' . $est . '</button>' . " " . '
                                    <button type="button" onClick="mostrar(\'' . $row["cedula"] . '\');"  id="' . $row["cedula"] . '" class="btn btn-info btn-sm update">Editar</button>' . " " . '
                                </div>';
                /*$sub_array[] = '<div class="col text-center">
                                <button type="button" onClick="cambiarEstado(\'' . $row["cedula"] . '\',\'' . $row["estado"] . '\');" name="estado" id="' . $row["cedula"] . '" class="' . $atrib . '">' . $est . '</button>' . " " . '
                                <button type="button" onClick="mostrar(\'' . $row["cedula"] . '\');"  id="' . $row["cedula"] . '" class="btn btn-info btn-sm update">Editar</button>' . " " . '
                                <button type="button" onClick="eliminar(\'' . $row["cedula"] . '\',\''. $row["login"] . '\');"  id="' . $row["cedula"] . '" class="btn btn-danger btn-sm eliminar">Eliminar</button>
                            </div>';*/
            }

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

    case "eliminar":
        $eliminar = false;
        $cedula = $_POST["cedula"];

        $usuario = Usuarios::byDni($cedula);
        if(is_array($usuario) == true and count($usuario) > 0) {
            $eliminar = $usuarios->eliminar_usuario($cedula);
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
