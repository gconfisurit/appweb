<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("kpimanager_modelo.php");

//INSTANCIAMOS EL MODELO
$kpiManager = new KpiManager();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "activarydesactivar":
        $id = $_POST["id"];
        $activo  = $_POST["est"];
        //consultamos si existe el registro
        $datos = $kpiManager->get_datos_edv($id);
        //valida el id del cliente
        if (is_array($datos) == true and count($datos) > 0) {
            //si esta activo(1) lo situamos cero(0), y viceversa
            ($activo == "0") ? $activo = 1 : $activo = 0;
            //edita el estado
            $estado = $kpiManager->editar_estado_edv($id, $activo);
            //evalua que se realizara el query
            ($estado) ? $output["mensaje"] = "Actualizacion realizada Exitosamente" : $output["mensaje"] = "Error al Actualizar";
        }

        echo json_encode($output);
        break;

    case "listar":
        $edv = $_POST['edv'];
        $datos = $kpiManager->get_datos_edv($edv);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        foreach ($datos as $row){

            $sub_array = array();

            //ESTADO
            $est = '';
            $atrib = "btn btn-success btn-sm estado";
            switch ($row["activo"]){
                case 0:
                    $est = 'INACTIVO';
                    $atrib = "btn btn-warning btn-sm estado";
                    break;
                case 1:
                    $est = 'ACTIVO';
                    break;
            }

            $coordinador = ($row["activo"]==1 and empty($row["Coordinador"])) ? '<br><span class="right badge badge-primary">Coordinador no asignado</span>' : '';

            $sub_array[] = $row["CodVend"];
            $sub_array[] = $row["Descrip"] . $coordinador;
            $sub_array[] = $row["clase"];
            $sub_array[] = !empty($row["ubicacion"]) ? $row["ubicacion"] : "-";
            $sub_array[] = '<div class="col text-center">
                                <button type="button" onClick="cambiarEstado(\''.$row["CodVend"].'\',\''.$row["activo"].'\');" name="estado" id="' . $row["CodVend"] . '" class="' . $atrib . '">' . $est . '</button>' . " " . '
                                <button type="button" onClick="mostrar(\''. $row["CodVend"] .'\');"  id="' . $row["CodVend"] . '" class="btn btn-info btn-sm update">Editar</button>
                            </div>';

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


    case "mostrar":

        $codvend = $_POST["edv"];

        if($codvend != "-") {
            $row = $kpiManager->get_datos_edv($codvend);
            $clientes = $kpiManager->get_clientesPorEdv($codvend);

            $obj_dropsize = (count($clientes)>0 and (!empty($row[0]['Frecuencia']) and $row[0]['Frecuencia']>0))
                                                        ? ($row[0]['ObjVentasBs'])/count($clientes)*$row[0]['Frecuencia'] : 0;

            $tiempo_estimado_despacho = !empty($row[0]['Tiempo_Estimado_Despacho']) ? trim($row[0]['Tiempo_Estimado_Despacho']) : 0;
            $obj_ventas_kg      = !empty($row[0]['ObjVentasKg']) ? trim($row[0]['ObjVentasKg']) : 0;
            $obj_ventas_bul     = !empty($row[0]['ObjVentasBu']) ? trim($row[0]['ObjVentasBu']) : 0;
            $obj_ventas_und     = !empty($row[0]['ObjVentasUn']) ? trim($row[0]['ObjVentasUn']) : 0;
            $obj_logro_especial = !empty($row[0]['ObjLogEspec']) ? trim($row[0]['ObjLogEspec']) : 0;
            $obj_ventas_divisas = !empty($row[0]['ObjVentasBs']) ? trim($row[0]['ObjVentasBs']) : 0;

            $output = array(
                'codvend'        => $row[0]['CodVend'],
                'obj_ventas_kg'  => str_replace(',', '', Strings::rdecimal($obj_ventas_kg, 0)),
                'nombre'         => trim($row[0]['apenom']),
                'obj_ventas_bul' => str_replace(',', '', Strings::rdecimal($obj_ventas_bul, 0)),
                'cedula'         => trim($row[0]['cedula']),
                'obj_ventas_und' => str_replace(',', '', Strings::rdecimal($obj_ventas_und, 0)),
                'ubicacion'      => trim($row[0]['ubicacion']),
                'obj_dropsize'   => str_replace(',', '', Strings::rdecimal($obj_dropsize, 0)),
                'clase'          => trim($row[0]['clase']),
                'obj_captar_clientes' => $row[0]['ObjCaptar'],
                'coordinador'         => trim($row[0]['Coordinador']),
                'obj_especial'        =>str_replace(',', '', Strings::rdecimal(trim($row[0]['ObjEspecial'], 0))),
                'deposito'            => '01',
                'obj_logro_especial'  => Strings::rdecimal($obj_logro_especial, 0),
                'frecuencia'          => trim($row[0]['Frecuencia']),
                'tiempo_estimado_despacho'  => $tiempo_estimado_despacho,
                'obj_ventas_divisas'        => str_replace(',', '', Strings::rdecimal($obj_ventas_divisas, 0)),
                'objetivo_kpi'      => str_replace(',', '', Strings::rdecimal(trim($row[0]['Requerido_Bult_Und']), 0)),
                'ava'               => trim($row[0]['ava']),
                'ava_fotos'         => trim($row[0]['ava_fotos']),
                'lista_obj_kpi'     => $kpiManager->get_objetivos_kpi(),
                'lista_clases_kpi'  => $kpiManager->get_clases_edv(),
            );
        }

        echo json_encode($output);

        break;


    case "guardar":

        $hist_cambio_kpi = false;
        $cambio_hist_kpi = false;

        $values = array(
            'supervisor'          => $_POST["supervisor"],
            'ruta'                => $_POST["ruta"],
            'cedula'              => '',
            'nombre'              => $_POST["nombre"],
            'obj_ventas_kg'       => $_POST["obj_ventas_kg"],
            'obj_ventas_und'      => $_POST["obj_ventas_und"],
            'obj_ventas_bul'      => $_POST["obj_ventas_bul"],
            'ubicacion'           => $_POST["ubicacion"],
            'drop_size'           => $_POST["drop_size"],
            'clase'               => $_POST["clase"],
            'obj_clientes_captar' => '',
            'obj_especial'        => $_POST["obj_especial"],
            'deposito'            => $_POST["deposito"],
            'logro_obj_especial'  => $_POST["logro_obj_especial"],
            'frecuencia'          => $_POST["frecuencia"],
            'tiempo_est_despacho' => $_POST["tiempo_est_despacho"],
            'obj_ventas_divisas'  => str_replace(".", "", str_replace(",", ".", $_POST["obj_ventas_divisas"])),
            'obj_ava'             => $_POST["obj_ava"],
            'objetivo_kpi'        => $_POST["objetivo_kpi"],
            'fotos_ava'           => $_POST["fotos_ava"],
        );

        # obtenemos los datos antes de actualizarlos para almacenarlos en un historico
        $antesActualizar = $kpiManager->get_datos_edv_antesactualizar($values['ruta']);

        # actualizamos los datos del EDV
        $actualizarEdv    = $kpiManager->actualizar_edv($values);
        $actualizarEdv_02 = $kpiManager->actualizar_edv_02($values);

        # si la actualizacion fue existosa
        if($actualizarEdv_02) {
            $change = 0;

            # creamos un array con el valor segun el campo para almarcenarlo en el historico
            $array = array(
                0 => $values['obj_clientes_captar'],
                1 => $values['obj_especial'],
                2 => $values['logro_obj_especial'],
                3 => $values['obj_ventas_divisas'],
                4 => $values['obj_ventas_und'],
                5 => $values['obj_ventas_kg'],
                6 => $values['obj_ventas_bul']
            );

            #verificamos logicamente cuales campos modificaron
            for ($i=0; $i < count(array_keys($antesActualizar[0])); $i++) {
                if($antesActualizar[0][array_keys($antesActualizar[0])[$i]] != $array[$i]) {
                    $change++;
                }
            }
            if ($change > 0) {
                # insertamos el dato de historico y obtenemos el codigo de insersion
                $hist_cambio_kpi = $kpiManager->insertar_historico_cambio_kpi('1', $values['ruta']);
                if ($hist_cambio_kpi != -1) {
                    for($i=0; $i < count(array_keys($antesActualizar[0])); $i++) {
                        if($antesActualizar[0][array_keys($antesActualizar)[$i]] != $array[$i]) {
                            $antes   = $antesActualizar[0][array_keys($antesActualizar[0])[$i]];
                            $despues = $array[$i];
                            # insertamos el historico por campo
                            $cambio_hist_kpi = $kpiManager->insertar_cambio_historico_kpi($hist_cambio_kpi, $i, $antes, $despues);
                        }
                    }
                }

            }
        }

        //mensaje
        $output["icono"] = "error";
        if ($actualizarEdv and $actualizarEdv_02 and ($hist_cambio_kpi and $cambio_hist_kpi)) {
            $output["mensaje"] = 'Datos actualizados';
            $output["icono"] = "success";
        } elseif (!$actualizarEdv) {
            $output["mensaje"] = 'Error al actualizar datos';
        } elseif (!$actualizarEdv_02) {
            $output["mensaje"] = 'Error al actualizar clase';
        } elseif (!$hist_cambio_kpi or !$cambio_hist_kpi) {
            $output["mensaje"] = 'Error al insertar historico';
        } else {
            $output["mensaje"] = 'Error al actualizar';
        }

        echo json_encode($output);
        break;
}
