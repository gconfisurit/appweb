<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("indicadoresdespacho_modelo.php");

//INSTANCIAMOS EL MODELO
$indicadores = new InidicadoresDespachos();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_choferes":
        $output["lista_choferes"] = Choferes::todos();

        echo json_encode($output);
        break;

    case "listar_periodos":
        $indicador_seleccionado = $_GET['s'];
        $tipoPeriodo = $_POST['tipoPeriodo'];
        $chofer_id   = $_POST['chofer_id'];

        $datos = $indicadores->get_periodos($indicador_seleccionado, $tipoPeriodo, $chofer_id);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $output = Array();
        if(is_array($datos) and count($datos) > 0)
        {
            foreach ($datos as $row) {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                switch($tipoPeriodo) {
                    case "Anual":
                        $sub_array["label"] = $row["anio"];
                        $sub_array["value"] = $row["anio"];
                        break;
                    case "Mensual":
                        $sub_array["label"] = Dates::month_name(Strings::addCero($row["mes"]))." ".$row["anio"];
                        $sub_array["value"] = $row["anio"]."-".Strings::addCero($row["mes"]);
                        break;  
                }
                $output[] = $sub_array;
            }
        } else {
            $output['error'] = "NO HAY DATOS PARA EL CHOFER SELECCIONADO";
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        echo json_encode($output);

        break;


    case "listar_entregas_efectivas":
        $tipoPeriodo = $_POST['tipoPeriodo'];
        $periodo   = $_POST['periodo'];
        $chofer_id = $_POST['chofer'];

        switch($tipoPeriodo) {
            case "Anual":
                $fechai = $periodo."-01-01";
                $fechaf = $periodo."-12-31";
                break;
            case "Mensual":
                $fechai = $periodo."-01";
                $fechaf = date("Y-m-t", strtotime($periodo));
                break;  
        }

        $datos = $indicadores->get_entregasefectivas_por_chofer($fechai, $fechaf, $chofer_id);

        //inicializamos la variables
        $chofer = Choferes::getByDni($chofer_id);
        $chofer = (count($chofer) > 0) ? $chofer[0]['cedula'].' - '.$chofer[0]['descripcion'] : "";
        $formato_fecha = $tipoPeriodo=="Anual" ? 'm-Y' : 'd-m-Y';
        $ordenes_despacho_string = "";
        $fact_sinliquidar_string = "";
        $totaldespacho = 0;
        $total_ped_entregados = 0;
        $total_ped_porliquidar = 0;
        $promedio_diario_despacho = 0;

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        if(is_array($datos) and count($datos) > 0)
        {
            //almacenamos el total de despachos para calcular la efectividad posteriormente
            foreach ($datos as $row)
                $totaldespacho += intval($row['cant_documentos']);

            foreach ($datos as $key => $row)
            {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $ordenes_despacho_string .= ($row['correlativo'] . "(" . Strings::addCero($row['cant_documentos']) . "), ");

                $porcentaje = Strings::rdecimal(($row['cant_documentos'] / $totaldespacho) * 100, 1);

                /** entregas efectivas **/
                if ($row['tipo_pago'] !='N/C' and $row['tipo_pago'] !='N/C/P'
                    and $row['fecha_entre'] != null or Dates::check_in_range($fechai, $fechaf, $row['fecha_entre'])
                ) {
                    //consultamos si la de la iteracion actual tiene fecha igual a la insertada en la interacion anterior
                    if(count($data)>0 and date_format(date_create($row['fecha_entre']), $formato_fecha) == $data[count($data)-1]['fecha_entrega'])
                    {
                        $data[count($data)-1]['cant_documentos'] += intval($row['cant_documentos']);
                        $data[count($data)-1]['porc'] += floatval($porcentaje);
                        $data[count($data)-1]['ordenes_despacho'] .= (", " . $row['correlativo']);
                    }
                    //si no es igual, solo inserta un nuevo registro al array
                    else {
                        $sub_array['fecha_entrega'] = date_format(date_create($row['fecha_entre']), $formato_fecha);
                        $sub_array['cant_documentos'] = intval($row['cant_documentos']);
                        $sub_array['porc'] = floatval($porcentaje);
                        $sub_array['ordenes_despacho'] = $row['correlativo'];
                        $sub_array['nombre_mes'] = Dates::month_name(date_format(date_create($row['fecha_entre']), 'm'), true);

                        $data[] = $sub_array;
                    }
                }

                /** facturas sin liquidar **/
                if(strlen($row['fact_sin_liquidar'])>0)
                {
                    $fact_sinliquidar_string .= ($row['fact_sin_liquidar'].", ");
                    $array = explode(", ", $fact_sinliquidar_string);
                    $array = array_unique($array);
                    /* sort($array, SORT_ASC); */
                    $fact_sinliquidar_string = implode(", ", $array);
                }
            }
        }
        /** calcular los pedidos entregados **/
        foreach ($data as $arr){
            $total_ped_entregados += intval($arr['cant_documentos']);
        }

        /** calcular los pedidos sin liquidar **/
        $total_ped_porliquidar = $totaldespacho - $total_ped_entregados;

        /** calcular el promedio diario de despachos **/
        $promedio_diario_despacho = (count($data) > 0) ? $total_ped_entregados / count($data) : 0;

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "chofer" => $chofer,
            "ordenes_despacho" => $ordenes_despacho_string,
            "fact_sinliquidar" => str_ireplace(",",", ",$fact_sinliquidar_string),
            "totaldespacho" => $totaldespacho,
            "total_ped" => $total_ped_entregados,
            "total_ped_porliquidar" => $total_ped_porliquidar,
            "promedio_diario_despacho" => Strings::rdecimal($promedio_diario_despacho,0),
            "fechai" => $fechai,
            "fechaf" => $fechaf,
            "tabla" => $data
        );
        echo json_encode($output);

        break;


    case "obtener_causas_rechazo":

        $output["lista_causas"] = CausasRechazos::todos();

        echo json_encode($output);

        break;


    case "listar_causas_rechazo":
        $tipoPeriodo = $_POST['tipoPeriodo'];
        $periodo   = $_POST['periodo'];
        $chofer_id = $_POST['chofer'];
        $causa = $_POST['causa'];

        switch($tipoPeriodo) {
            case "Anual":
                $fechai = $periodo."-01-01";
                $fechaf = $periodo."-12-31";
                break;
            case "Mensual":
                $fechai = $periodo."-01";
                $fechaf = date("Y-m-t", strtotime($periodo));
                break;  
        }

        $datos = $indicadores->get_causasrechazo_por_chofer($fechai, $fechaf, $chofer_id, $causa);

        //inicializamos la variables
        if($chofer_id!="-") {
            $chofer = Choferes::getByDni($chofer_id);
            $chofer = (count($chofer) > 0) ? $chofer[0]['cedula'].' - '.$chofer[0]['descripcion'] : "";
        } else {
            $chofer = "Todos los Choferes";
        }
        $formato_fecha = $tipoPeriodo=="Anual" ? 'm-Y' : 'd-m-Y';
        $ordenes_despacho = Array();
        $ordenes_despacho_string = "";
        $totaldespacho = Array();
        $totalendespacho = 0;
        $total_ped_devueltos = 0;

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        if(is_array($datos) and count($datos) > 0)
        {
            //almacenamos el total de despachos agrupado
            foreach ($datos as $row){
                if($row['fecha_entre']==null or Dates::check_in_range($fechai, $fechaf, $row['fecha_entre'])) {
                    $fecha_entrega = $row['fecha_entre'] != null ? date_format(date_create($row['fecha_entre']), $formato_fecha) : "sin fecha de entrega";

                    //consultamos si la fecha a consultar existe en el array totaldespacho
                    if(count($totaldespacho)>0 and in_array($fecha_entrega, array_keys($totaldespacho)))
                        $totaldespacho[$fecha_entrega] += intval($row['cant_documentos']);
                    //si no existe, solo inserta un nuevo registro al array
                    else $totaldespacho[$fecha_entrega] = intval($row['cant_documentos']);
                }
            }

            //empezamos con el proceso de la data
            foreach ($datos as $key => $row)
            {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                /** obtener los despachos realizados para imprimirlos en un string ordenado al final **/
                if(count($ordenes_despacho)>0 and $row['correlativo'] == $ordenes_despacho[count($ordenes_despacho)-1]["correlativo"]) {
                    $ordenes_despacho[count($ordenes_despacho)-1]['cant_documentos'] += intval($row['cant_documentos']);
                } else {
                    $ordenes_despacho[] = array(
                        "correlativo"     => $row['correlativo'],
                        "cant_documentos" => $row['cant_documentos']
                    );
                }

                /** causas de rechazo **/
                if(($row['tipo_pago'] =='N/C' or $row['tipo_pago'] =='N/C/P') and $key>=0 )
                {
                    //consultamos si la de la iteracion actual tiene fecha igual a la insertada en la interacion anterior
                    if(count($data)>0 and  ($row['fecha_entre'] != null
                        and date_format(date_create($row['fecha_entre']), $formato_fecha) == $data[count($data)-1]['fecha_entrega']) ||
                        ($row['fecha_entre']==null and "sin fecha de entrega"==$data[count($data)-1]['fecha_entrega'])
                    ) {
                        $porcentaje = ($row['cant_documentos'] / $totaldespacho[$data[count($data)-1]['fecha_entrega']]) * 100;
                        $data[count($data)-1]['cant_documentos'] += intval($row['cant_documentos']);
                        $data[count($data)-1]['porc'] += $porcentaje;
                        $data[count($data)-1]['ordenes_despacho'] .= (", " . $row['correlativo']);

                        $arr = array_map(function ($arr) { return $arr['tipo']; }, $data[count($data)-1]['observacion']);
                        //verifica si existe la observacion
                        if (!in_array(strtoupper($row['observacion']), $arr)) {
                            # no existe, le agrega en una nueva posicion
                            $data[count($data)-1]['observacion'][] = Array(
                                "tipo" => strtoupper($row['observacion']),
                                "cant" => intval($row['cant_documentos']),
                                "color" => Array("id" => $row['color_id'], "hex" => $row['color'])
                            );
                        } else {
                            # si existe, le suma la cantidad de documentos
                            $pos = array_search($row['observacion'], $arr);
                            $data[count($data)-1]['observacion'][$pos]['cant'] += intval($row['cant_documentos']);
                        }
                    }
                    //si no es igual, solo inserta un nuevo registro al array
                    elseif($row['fecha_entre']==null or Dates::check_in_range($fechai, $fechaf, $row['fecha_entre'])){
                        $fecha_entrega = ($row['fecha_entre'] != null and strlen($row['fecha_entre'])>0)
                            ? date_format(date_create($row['fecha_entre']), $formato_fecha) : "sin fecha de entrega";
                        $nombre_mes = ($row['fecha_entre'] != null and strlen($row['fecha_entre'])>0)
                            ? Dates::month_name(date_format(date_create($row['fecha_entre']), 'm'), true) : "sin f. entreg.";
                        $porcentaje = ($row['cant_documentos'] / $totaldespacho[$fecha_entrega]) * 100;

                        $sub_array['fecha_entrega'] = $fecha_entrega;
                        $sub_array['nombre_mes'] = $nombre_mes;
                        $sub_array['cant_documentos'] = intval($row['cant_documentos']);
                        $sub_array['porc'] = $porcentaje;
                        $sub_array['ordenes_despacho'] = $row['correlativo'];
                        $sub_array['observacion'][] = Array(
                            "tipo"  => strtoupper($row['observacion']),
                            "cant"  => intval($row['cant_documentos']),
                            "color" => Array("id" => $row['color_id'], "hex" => $row['color'])
                        );

                        $data[] = $sub_array;
                    }
                }
            }
        }
        /** los despachos realizados obtenidos se agregan a un string **/
        foreach ($ordenes_despacho as $arr){
            $ordenes_despacho_string .= ($arr['correlativo'] . "(" . Strings::addCero($arr['cant_documentos']) . "), ");
        }

        /** calcular los pedidos devueltos **/
        foreach ($data as $arr){
            $total_ped_devueltos += intval($arr['cant_documentos']);
        }

        /** calcular el total despachos **/
        foreach ($totaldespacho as $key => $arr){
            $totalendespacho += intval($arr);
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "chofer" => $chofer,
            "ordenes_despacho" => $ordenes_despacho_string,
            "totaldespacho" => $totalendespacho,
            "total_ped" => $total_ped_devueltos,
            "fechai" => $fechai,
            "fechaf" => $fechaf,
            "tabla" => $data
        );
        echo json_encode($output);

        break;


    case "listar_oportunidad_despacho":
        $tipoPeriodo = $_POST['tipoPeriodo'];
        $periodo   = $_POST['periodo'];
        $chofer_id = $_POST['chofer'];

        switch($tipoPeriodo) {
            case "Anual":
                $fechai = $periodo."-01-01";
                $fechaf = $periodo."-12-31";
                break;
            case "Mensual":
                $fechai = $periodo."-01";
                $fechaf = date("Y-m-t", strtotime($periodo));
                break;  
        }

        $datos = $indicadores->get_oportunidaddespacho_por_chofer($fechai, $fechaf, $chofer_id);

        //inicializamos la variables
        $chofer = Choferes::getByDni($chofer_id);
        $chofer = (count($chofer) > 0) ? $chofer[0]['cedula'].' - '.$chofer[0]['descripcion'] : "";
        $formato_fecha = $tipoPeriodo=="Anual" ? 'm-Y' : 'd-m-Y';
        $ordenes_despacho_string = '';
        $oportunidad_promedio = 0;
        $total_ped = 0;
        $documentos = "";
        $objetivo = 80;
        $totaldoc = 0;
        $fechaaevaluar = "00/00/0000";

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        foreach ($datos as $key => $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            $fecha_entrega = $row["fecha_entre"] ?? date('Y-m-d');
            $tiempo_entrega_estimado = intval($row['tiempo_estimado']);

            $tiempo = strtotime($fecha_entrega)-strtotime($row["fecha_desp"]);
            $tiempo_entrega = intval($tiempo/60/60/24);

            $oportunidad = ($tiempo_entrega <= $tiempo_entrega_estimado) ? 100 : ($tiempo_entrega_estimado/$tiempo_entrega)*100;
            $oportunidad_promedio += $oportunidad;

            /** oportunidad despachos **/
            if($row['fecha_desp'] != null)
            {
                //almacenamos el total de documentos para calcular la oportunidad posteriormente
                if(!Dates::check_in_range($fechaaevaluar, $fechaaevaluar, $row['fecha_desp'])) {
                    $fechaaevaluar = $row['fecha_desp'];
                    $totaldoc = Functions::searchQuantityDocumentsByDates($datos, "fecha_desp", $fechaaevaluar, $formato_fecha);
                }

                //consultamos si la de la iteracion actual tiene fecha igual a la insertada en la interacion anterior
                if(count($data)>0 and date_format(date_create($row['fecha_desp']), $formato_fecha) == $data[count($data)-1]['fecha_desp'])
                {
                    $data[count($data)-1]['cant_documentos'] += 1;
                    $data[count($data)-1]['oportunidad'] += floatval($oportunidad/$totaldoc);
                    $data[count($data)-1]['documentos'] .= (", " . $row['numerod']);
                }
                //si no es igual, solo inserta un nuevo registro al array
                else {
                    $sub_array['fecha_desp'] = date_format(date_create($row['fecha_desp']), $formato_fecha);
                    $sub_array['cant_documentos'] = 1;
                    $sub_array['oportunidad'] = floatval($totaldoc>1 ? $oportunidad/$totaldoc : $oportunidad);
                    $sub_array['documentos'] = $row['numerod'];
                    $sub_array['nombre_mes'] = Dates::month_name(date_format(date_create($row['fecha_desp']), 'm'), true);

                    $data[] = $sub_array;
                }
            }
        }

        /** calculamos el porcentaje de oportunidad de despacho promedio **/
        if(count($data) > 0) {
            $oportunidad_promedio = ($oportunidad_promedio/count($datos));
        } else {
            $oportunidad_promedio = 0;
        }

        /** calcular la cantidad de documentos **/
        foreach ($data as $arr){
            $total_ped += intval($arr['cant_documentos']);
        }

        /** concatenar la cantidad de documentos **/
        foreach ($data as $arr){
            $ordenes_despacho_string .= (", " . $arr['documentos']);
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "chofer"     => $chofer,
            "oportunidad_promedio" => Strings::rdecimal($oportunidad_promedio,2),
            "ordenes_despacho" => $ordenes_despacho_string,
            "total_ped"  => $total_ped,
            "fechai"     => $fechai,
            "fechaf"     => $fechaf,
            "objetivo"   => $objetivo,
            "documentos" => $documentos,
            "tabla"      => $data
        );
        echo json_encode($output);

        break;
}
