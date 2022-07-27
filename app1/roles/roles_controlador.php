<?php
//llamar a la conexion de la base de datos
require_once("../../config/conexion.php");

//llamar a el modelo Usuarios
require_once("roles_modelo.php");

$roles = new Roles();


switch ($_GET["op"]) {

    case "guardaryeditar":
        $rol_estatus = false;

        $id_rol = $_POST["id_rol"];

        $data = array(
            'id_rol' => $id_rol,
            'descripcion' => strtoupper($_POST['rol']),
        );

        if (empty($id_rol)) {
            /*verificamos si existe la cedula y correo en la base de datos, si ya existe un registro con la cedula o correo entonces no se registra el usuario*/
            $datos = $roles->get_nombre_rol($data['descripcion']);

            if (is_array($datos) == true and count($datos) == 0) {
                //no existe el usuario por lo tanto hacemos el registros
                $rol_estatus = $roles->registrar_rol($data);

            } else {


            }

        } else {
            /*si ya existe entonces editamos el usuario*/
            $rol_estatus = $roles->editar_rol($data);
        }

        //mensaje
        if($rol_estatus){
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
        $id_rol = $_POST["id_rol"];

        $datos = $roles->get_rol_por_id($id_rol);

        if (is_array($datos) == true and count($datos) > 0) {
            $output["descripcion"] = $datos[0]["descripcion"];
        }

        echo json_encode($output);
        break;


    case "listar":

        $datos = $roles->get_roles();
        //declaramos el array
        $data = array();
        foreach ($datos as $row) {

            $sub_array = array();

            $sub_array[] = $row["id"];
            $sub_array[] = $row["descripcion"];

            # el parametro t es el tipo:
            #        0 el tipo es rol (este caso)
            #        1 el tipo es usuario
            $sub_array[] = '<div align="center form-check-inline p-t-30">
								<a href="../permiso/permiso.php?&t='. 0 .'&i='. $row["id"] .'">Ver Permisos</a>
							</div>';
            if (hash_equals("1", $row["id"])) {
                $sub_array[] = 'No Editable';
            } else {
                $sub_array[] = '<div class="col text-center">
                                    <button type="button" onClick="mostrar(\'' . $row["id"] . '\');"  id="' . $row["id"] . '" class="btn btn-info btn-sm update">Editar</button>' . " " . '
                                    <button type="button" onClick="eliminar(\'' . $row["id"] . '\',\'' . $row["descripcion"] . '\');"  id="' . $row["id"] . '" class="btn btn-danger btn-sm eliminar">Eliminar</button>
                                </div>';
            }

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
        $relacion = true;
        $id_rol = $_POST["id_rol"];

        $datos = $roles->get_rol_por_id($id_rol);
        if(is_array($datos) == true and count($datos) > 0)
        {
            // verifica si el rol esta relacionado con usuarios
            $relacion_usuario = $roles->get_relacion_rol_usuario($id_rol);
            if (is_array($relacion_usuario) == true and count($relacion_usuario) == 0) {
                $relacion = true;
                $eliminar = $roles->eliminar_chofer($id_rol);
            } else {
                $relacion = false;
            }
        }

        //mensaje
        if($eliminar and $relacion){
            $output = [
                "mensaje" => "Se eliminó exitosamente!",
                "icono"   => "success"
            ];
        }elseif (!$relacion) {
            $output = [
                "mensaje" => "Existen Usuarios con el rol asignado!",
                "icono"   => "error"
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
