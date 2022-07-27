<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("kpimarca_modelo.php");

//INSTANCIAMOS EL MODELO
$kpiMarca = new KpiMarcaModel();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_marcas":
        $marcas = Marcas::todos();
        $kpi_marcas = array_map(function ($arr) { return $arr['descripcion']; }, KpiMarcas::todos());

        $arr = Array();
        foreach ($marcas as $m){
            $arr[] = [
                "marca" => $m["marca"],
                "selec" => in_array($m["marca"], $kpi_marcas)
            ];
        }

        $output["lista_marcas"] = $arr;
        echo json_encode($output);
        break;


    case "guardar_kpiMarcas":
        $marcas = isset($_POST['marcas']) ? $_POST['marcas'] : array();
        $guardar = $eliminar = true;

        if(count(KpiMarcas::todos()) > 0) {
            $eliminar = $kpiMarca->eliminar_kpiMarcas();
        }

        if (!empty($marcas) and $eliminar) {
            foreach ($marcas as $marca) {
                $guardar = $kpiMarca->registrar_kpiMarcas($marca);
            }
        }

        //mensaje
        if($eliminar && $guardar){
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
}
