<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("factorcambiario_modelo.php");

//INSTANCIAMOS EL MODELO
$factor = new FactorCambiario();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "mostrar_factor":
        $datos = $factor->get_factor();

        $output["factor"] = (ArraysHelpers::validate($datos))
            ? Strings::rdecimal($datos[0]['factor'], 2)
            : 'error';

        echo json_encode($output);
        break;


    case "guardaryeditar":
        $factor_nuevo = str_replace(",", ".", str_replace(".", "", $_POST['factor_nuevo']));

        $guardar = $factor->editar_factor($factor_nuevo);

        //mensaje
        if ($guardar) {
            $output = [
                "mensaje" => "Guardado con Exito!",
                "icono"   => "success"
            ];
        } else {
            $output = [
                "mensaje" => "Ocurrió un error al Guardar!",
                "icono"   => "error",
                "valor" => $factor_nuevo
            ];
        }

        echo json_encode($output);
        break;
}
