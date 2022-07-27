<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("devolucionessinmotivo_modelo.php");

//INSTANCIAMOS EL MODELO
$sinmotivo = new Devolucionessinmotivo();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_devolucionessinmotivo":

        $data = array(
            'fechai'        => $_POST["fechai"],
            'fechaf'        => $_POST["fechaf"],
            'tipodespacho'  => $_POST["tipodespacho"],
            'tipodoc'       => $_POST["tipodoc"],
        );

        $datos = Array();
        switch ($data['tipodoc']) {
            case 2: $datos = $sinmotivo->getDevolucionesFactura($data); break;
            case 3: $datos = $sinmotivo->getDevolucionesNotadeEntrega($data); break;
        }

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        if (is_array($datos)==true and count($datos)>0)
        {
            $list_rechazos = Functions::selectListCausasRechazos();
            $suma_monto = $suma_peso = $tipo = 0;

            foreach ($datos as $key => $row) {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $op = ($row['numeror'] != null) ? 1 : 2;

                $columna_3 = ($row['numeror'] != null)
                    ? $row['numeror']
                    : '';

                $tipofac = '';
                switch ($row["tipofac"]) {
                    case 'B': $tipofac = 'Devolución Factura'; break;
                    case 'D': $tipofac = 'Devolución Nota de Entrega'; break;
                }

                $tipoBadge = ($row["tipofac"]=='B' ? 'badge-primary' : 'badge-secondary');

                $sub_array[] = $key+1;
                $sub_array[] = $row["code_vendedor"];
                $sub_array[] = $row["numerod"];
                $sub_array[] = $columna_3 .'<br><span class="right badge '.$tipoBadge.'">'.$tipofac.'</span>';
                $sub_array[] = date(FORMAT_DATE, strtotime($row['fecha_fact']));
                $sub_array[] = $row["cod_clie"];
                $sub_array[] = $row["cliente"];
                $sub_array[] = Strings::rdecimal($row["monto"],2);
                $sub_array[] = '<div align="text-center">
								<div id="causa'.$key.'_div" class="input-group">
									<select id="causa'.$key.'" name="causa'.$key.'" class="form-control custom-select" onchange="guardarCausaRechazoSeleccionada(\''. $row["numerod"] .'\',\''. $row["tipofac"] .'\',\''. $columna_3 .'\',\''. $op .'\',\''. $key .'\')">
										'.$list_rechazos.'
									</select>
								</div>
							</div>';

                $suma_monto += $row['monto'];

                $data[] = $sub_array;
            }
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $results = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data
        );

        echo json_encode($results);
        break;

    case 'insertar_motivo':
        $output = array();
        $motivo = false;

        $data = array(
            'numerod'   => $_POST["num_nota"],
            'tipo_nota' => $_POST["tipo_nota"],
            'numeror'   => $_POST["numeror"],
            'op'        => $_POST["op"],
            'motivo'    => $_POST["motivo"],
        );

        //consultamos si existe
        $datos = $sinmotivo->getDocumentoEnDespacho($data);

        if (is_array($datos) == true and count($datos) > 0) {

            //si existe entonces se actualiza
            $motivo = $sinmotivo->editar_motivo($data);
            $output["mensaje"] = "Motivo de devolución " . $data['numerod'] . " actualizado correctamente";

        } elseif(count($datos)==0 and ($data['op'] == 2 or $data['op'] == 1)) {

            //sino entoces insertamos el documento con el motivo
            $motivo = $sinmotivo->insertar_motivo($data);
            $output["mensaje"] = hash_equals('1', $data['op'])
                ? "Motivo de devolución " . $data['numeror'] . " insertado correctamente"
                : "Motivo de devolución " . $data['numerod'] . " insertado correctamente";

        } else {
            $output["mensaje"] = "Ya está actualizada";
        }

        if ($motivo) {
//            $output["mensaje"] = "Se registró correctamente";
            $output["icono"] = "success";
        } else {
            $output["mensaje"] = "Error al insertar";
            $output["icono"] = "error";
        }

        echo json_encode($output);
        break;

    case "listar_tipodespacho":

        $arr = array();

        array_push($arr, array('id' => 0, 'desrip' => 'Sin Despacho'));
        array_push($arr, array('id' => 1, 'desrip' => 'Con Despacho'));
        $output["lista_tipodespacho"] = $arr;

        echo json_encode($output);
        break;

    case "listar_tipodoc":

        $arr = array();

        array_push($arr, array('id' => 2, 'desrip' => 'Devolución Factura'));
        array_push($arr, array('id' => 3, 'desrip' => 'Devolución Nota de Entrega'));
        $output["lista_tipodoc"] = $arr;

        echo json_encode($output);
        break;

}
