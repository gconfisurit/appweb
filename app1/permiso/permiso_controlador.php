<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("permiso_modelo.php");
require_once("../roles/roles_modelo.php");

//INSTANCIAMOS EL MODELO
$permisos = new Permiso();
$roles = new Roles();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "obtener_descripcion":
        $output = array();
        $id = $_POST['id'];
        $tipo = $_POST['tipo'];

        # el parametro tipo:
        #        0 el tipo es rol
        #        1 el tipo es usuario
        switch ($tipo) {
            case 0:
                $output['descripcion'] = $roles->get_rol_por_id($id)[0]['descripcion'];
                break;
            case 1:
                $output['descripcion'] = Usuarios::byDni($id)['nomper'];
                break;
        }

        echo json_encode($output);
        break;

    case "listar_permisos":
        $id = $_POST['id'];
        $tipo = $_POST['tipo'];
        $esMenuLateral = $_POST['esMenuLateral']==1; #variable entera, que utilizamos en forma de bandera para condicionar la obtencionde datos

        $output = Functions::organigramaMenusWithModules(-1, $tipo, $id, $esMenuLateral);

        echo json_encode($output);
        break;

    case 'guardar_permisos':
        $reponse = false;
        $output = array();

        $state = $_POST['state'];
        $tipo = $_POST['tipo'];

        $data = array(
            'id' => $_POST['id'],
            'modulo_id' => $_POST['modulo_id'],
        );

        if ($state=="true") {
            $mensajeError = 'registrar';
            $mensajeSuccess = 'registró';
            switch ($tipo) {
                case 0: // permisos por rol
                    $reponse = Permisos::registrar_rolmod($data) and PermisosHelpers::registrarPermisoPorRol($data);
                    break;
                case 1: // permisos por usuario
                    $reponse = Permisos::registrar_permiso($data);
                    break;
            }
        } else {
            $mensajeError = 'eliminar';
            $mensajeSuccess = 'eliminó';
            switch ($tipo) {
                case 0: // permisos por rol
                    $reponse = Permisos::borrar_rolmod($data) and PermisosHelpers::borrarPermisoPorRol($data);
                    break;
                case 1: // permisos por usuario
                    $reponse = Permisos::borrar_permiso($data);
                    break;
            }
        }

        if ($reponse) {
            $output["mensaje"] = "Se $mensajeSuccess correctamente";
            $output["icono"] = "success";
        } else {
            $output["mensaje"] = "Error al $mensajeError";
            $output["icono"] = "error";
        }

        echo json_encode($output);
        break;
}
