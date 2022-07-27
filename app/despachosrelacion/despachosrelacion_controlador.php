<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
session_start();
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("despachosrelacion_modelo.php");
require_once("../despachos/despachos_modelo.php");

//INSTANCIAMOS EL MODELO
$relacion = new DespachosRelacion();
$despachos = new Despachos();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_RelacionDespachos":
        # DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = array();

        # consulta a la base de datos para obtener todos los despachos creados
        $datos = $relacion->getRelacionDespachos();
      
            foreach ($datos as $row) {
                # DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $datos2 = $relacion->contarfactura($row["correl"]);

                foreach ($datos2 as $row2) {
                    $count=$row2["contador"];
                }

                $datos3 = $relacion->getchofer($row["cedula_chofer"]);

                foreach ($datos3 as $row3) {
                    $chofer=$row3["descripcion"];
                }
                $fecha_E = date('d/m/Y', strtotime($row["fechae"]));
                $sub_array[] = '<div class="col text-center"><a href="./detallesdepacho.php?despacho='.$row["correl"].'"> '.$row["correl"].'</div>';
                $sub_array[] = $fecha_E;
                $sub_array[] = $row["descrip"];
                $sub_array[] = $count;
                $sub_array[] = $row["nota"].' - '.$chofer;
                $sub_array[] = '<div class="col text-center">
                                    <button type="button" onClick="modalVerDetalleDespacho(\'' . $row["correl"] . '\');" id="' . $row["correl"] . '" class="btn btn-info btn-sm ver_detalles">Detalle</button>' . " " . '
                                    <button type="button" onClick="modalEditarDespachos(\'' . $row["correl"] . '\');"    id="' . $row["correl"] . '" class="btn btn-info btn-sm update">Editar</button>' . " " . '
                                    <button type="button" onClick="EliminarUnDespacho(\'' . $row["correl"] . '\');"      id="' . $row["correl"] . '" class="btn btn-danger btn-sm eliminar">Eliminar</button>
                                </div>';

                /*$sub_array[] = '<div class="col text-center"><a href="#" onclick="modalTipoReporte(\'' . $row["correl"] . '\');" class="nav-link">
                                    <i class="far fa-file-pdf fa-2x" style="color:red"></i>
                                </a></div>';
                $sub_array[] = str_pad($row["correl"], 8, 0, STR_PAD_LEFT)
                    . '<br><span class="right badge badge-secondary mt-1">' . date(FORMAT_DATE, strtotime($row["fechae"])) . '</span>';
                $sub_array[] = $row["nomper"];
                $sub_array[] = $row["cantDocumentos"];
                $sub_array[] = $row["destino"] . " - " . $row["NomperChofer"];
                $sub_array[] = '<div class="col text-center"><a href="#" onclick="" class="nav-link">
                                    <img src="../../public/build/images/bs.png" width="25" height="25" border="0" />
                                </a></div>';
                $sub_array[] = '<div class="col text-center">
                                    <button type="button" onClick="modalVerDetalleDespacho(\'' . $row["correl"] . '\');" id="' . $row["correl"] . '" class="btn btn-info btn-sm ver_detalles">Detalle</button>' . " " . '
                                    <button type="button" onClick="modalEditarDespachos(\'' . $row["correl"] . '\');"    id="' . $row["correl"] . '" class="btn btn-info btn-sm update">Editar</button>' . " " . '
                                    <button type="button" onClick="EliminarUnDespacho(\'' . $row["correl"] . '\');"      id="' . $row["correl"] . '" class="btn btn-danger btn-sm eliminar">Eliminar</button>
                                </div>';*/

                $data[] = $sub_array;
            }
        

        # RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "sEcho" => 1, # INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), # ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), # ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data);
        echo json_encode($output);
        break;

    case "buscar_despacho_por_correlativo":

        # obtenemos el valor enviado por ajax
        $correlativo = $_POST['correlativo'];
        $chofer =$modelo = $capacidad ="";

        # buscamos en la bd la cabecera del despacho
        $cabecera_despacho = $relacion->get_despacho_por_correlativo($correlativo);

        # validamos que la consulta de la cabecera tenga registro
        if (ArraysHelpers::validate($cabecera_despacho)) {

            $cabecera_despacho = ArraysHelpers::validateWithPos($cabecera_despacho, 0);

            # si tiene se asignan a variables de salida
            $output["correl"] = str_pad($cabecera_despacho['correl'], 8, 0, STR_PAD_LEFT);

            $traerchofe= $relacion->getchofer($cabecera_despacho['cedula_chofer']);
                foreach ($traerchofe as $consul_chofer) {
                    $chofer=$consul_chofer["descripcion"];
                }

                $traerVehiculo= $relacion->getVehiculo($cabecera_despacho['placa']);
                foreach ($traerVehiculo as $consul_vehi) {
                    $modelo=$consul_vehi["modelo"];
                    $capacidad=$consul_vehi["capacidad"];
                }

            $output["destino"] = $cabecera_despacho["nota"]. " - " .  $chofer;
            $output["fechad"] = date(FORMAT_DATE, strtotime($cabecera_despacho['fechad']));
            $output["vehiculo"] = $cabecera_despacho['placa'] . " " . $modelo . " " . $capacidad . " Kg";
            $output["cantDocumentos"] = 3 /*$cabecera_despacho['cantDocumentos']*/;
        }

        echo json_encode($output);
        break;

    case "buscar_destalle_despacho_por_correlativo":

        # obtenemos el valor enviado por ajax
        $correlativo = $_POST['correlativo'];
        # DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = array();
        $sumad=0;
        $sumabs=0;
        $tipo="";

        # buscamos en la bd la el detalle
        $detalle_despacho = $relacion->get_documentos_por_correlativo($correlativo);
       
            foreach ($detalle_despacho as $consul) {

                //$documento = array();
               /* switch ($row['tipofac']) {
                    case 'A':
                        $documento = $despachos->getFactura($row['numerod']);
                        break;
                    case 'C':
                        $documento = $despachos->getNotaDeEntrega($row['numerod']);
                        break;
                }*/
               // $documento = ArraysHelpers::validateWithPos($documento, 0);
               $datos3 = $relacion->get_detalle_clientes_por_correlativo($consul["numeros"]);
                $tipo1=$consul["TipoFac"];
               // echo "<script>console.log('Console: " . $tipo1 . "' );</script>";
                foreach ($datos3 as $row3) {
                    $CodClie=$row3["CodClie"];
                    $cliente=$row3["Descrip"];
                    $monto=$row3["MtoTotal"];

                }

                $datos2 = $relacion->fecha_despacho($correlativo);

                foreach ($datos2 as $row2) {
                    $fecha = date('d/m/Y', strtotime($row2["fechad"]));
                }

                # DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();
                if($tipo1=='A'){
                    $sub_array[] = "FACT";
                }else{
                    if($tipo1=='C'){
                      $sub_array[] = "N/E";
                    }

                }
                $sub_array[] = $consul["numeros"];
                $sub_array[] = $CodClie;
                $sub_array[] = $cliente;
                $sub_array[] = $fecha;
                
                if($tipo1=='A'){
                    $sub_array[] =Strings::rdecimal($monto, 2).' BS';
                }else{
                    if($tipo1=='C'){
                      $sub_array[] =Strings::rdecimal($monto, 2).' $';
                    }

                }

                if($tipo1=='A'){
                    $sumabs += $monto;
                }else{
                    if($tipo1=='C'){
                      $sumad += $monto;
                    }

                }
                


               /* $tipoDocu = ($row['tipofac'] == 'A') ? 'Factura' : 'Nota de Entrega';
                $tipoBadge = ($row['tipofac'] == 'A') ? 'badge-primary' : 'badge-secondary';

                $sub_array[] = $row["numerod"] . '<br><span class="right badge ' . $tipoBadge . '">' . $tipoDocu . '</span>';
                $sub_array[] = $documento["codclie"];
                $sub_array[] = $documento["descrip"];
                $sub_array[] = date(FORMAT_DATETIME2, strtotime($documento["fechae"]));
                $sub_array[] = Strings::rdecimal($documento["total"], 2);*/
                $sub_array[] = '<div class="col text-center">
                                      <button type="button" onClick="modalMostrarDocumentoEnDespacho(\'' . $consul["numeros"] . '\',\'' . $consul["TipoFac"] . '\',\'' . $correlativo . '\');"   id="' . $consul["numeros"] . '" class="btn btn-info btn-sm update">Editar</button>' . " " . '
                                      <button type="button" onClick="modalEliminarDocumentoEnDespacho(\'' . $consul["numeros"] . '\',\'' . $consul["TipoFac"] . '\',\'' . $correlativo . '\');"  id="' . $consul["numeros"] . '" class="btn btn-danger btn-sm eliminar">Eliminar</button>
                                </div>';

                $data[] = $sub_array;
            }
        

        # RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "sEcho" => 1, # INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), # ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            'Mtototal' => Strings::rdecimal($sumad, 2),
            'Mtototalbs' => Strings::rdecimal($sumabs, 2),
            "iTotalDisplayRecords" => count($data), # ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data
        );
        echo json_encode($output);
        break;

    case "buscar_cabeceraDespacho_para_editar":

        # obtenemos el correlativo enviado por ajax
        $correlativo = $_POST['correlativo'];

        # consultamos el despacho y la lista de choferes y vehiculos
        $despacho = $relacion->get_despacho_por_correlativo($correlativo);
        $output["lista_choferes"] = Choferes::todos();
        $output["lista_vehiculos"] = Vehiculo::todos();

        if (ArraysHelpers::validate($despacho)) {
            $despacho = ArraysHelpers::validateWithPos($despacho, 0);

            # asignamos en una variable de salida los datos necesarios del despacho
            $output["destino"] = $despacho["nota"];
            $output["fecha"] = $despacho['fechad'];
            $output["chofer"] = $despacho["cedula_chofer"];
            $output["vehiculo"] = $despacho['placa'];
        }

        echo json_encode($output);
        break;

    case "actualizar_cabeceraDespacho_para_editar":

        $actualizar_despacho = false;

        $fecha_ant = $destino_ant = $cedula_ant = $placa_ant = "";

           $capacidad =  $modelo =  $placa = $capacidadn = $modelon = $placan = "";

        $correlativo = $_POST["correlativo"];
        $id_vehiculo = $_POST["vehiculo"];
        $destino = $_POST["destino"];
        $id_chofer = $_POST["chofer"];
        $fechad = $_POST["fechad"];

        //consultamos si existe el despacho en la bd
        $despacho = $relacion->get_despacho_por_correlativo($correlativo);
        foreach ($despacho as $dat_despacho) {
           $placa_despa = $dat_despacho["placa"];
            $destino_despa = $dat_despacho["nota"];
            $chofer_despa = $dat_despacho["cedula_chofer"];
            $fecha_despa = $dat_despacho["fechad"];
              }
        //validamos si el despacho existe
        if (ArraysHelpers::validate($despacho)) {
            $despacho = ArraysHelpers::validateWithPos($despacho, 0);
            $id_vehiculo_ant = $placa_despa;
            $destino_ant = $destino_despa;
            $id_chofer_ant =  $chofer_despa;
            $fechad_ant = $fecha_despa;

            # datos anterior
            $vehiculo_ant = Vehiculo::getById($id_vehiculo_ant);
              foreach ($vehiculo_ant as $dat_vehi) {
                 $capacidad = $dat_vehi["capacidad"];
                 $modelo = $dat_vehi["modelo"];
                 $placa = $dat_vehi["placa"];
              }

            $chofer_ant = Choferes::getByDni($id_chofer_ant);
            foreach ($chofer_ant as $dat_chofer) {
                 $nombre = $dat_chofer["descripcion"];
              }
           
           
            # datos nuevo
            $vehiculo = Vehiculo::getById($id_vehiculo);
                foreach ($vehiculo as $dat_vehi1) {
                 $capacidadn = $dat_vehi1["capacidad"];
                 $modelon = $dat_vehi1["modelo"];
                 $placan = $dat_vehi1["placa"];
                }

            $chofer = Choferes::getByDni($id_chofer);
            foreach ($chofer as $dat_chofer1) {
                 $nombren = $dat_chofer1["descripcion"];
              }

            # actualizar despacho
            $actualizar_despacho = $despachos->updateDespacho(
                $correlativo, $destino, $id_chofer, $id_vehiculo, $fechad
            );

            /**  enviar correo: despachos_edita_2 **/
            if ($actualizar_despacho) {
                # preparamos los datos a enviar
                $dataEmail = EmailData::DataEditarChoferesyDestinoDeDespacho(
                    array(
                        'usuario' => $_SESSION['login'],
                        'correl_despacho' => $correlativo,
                        'vehiculo_ant' => $placa . " " . $modelo . " " . $capacidad . "Kg",
                        'destino_ant' => $destino_ant,
                        'chofer_ant' => $nombre,
                        'fechad_ant' => $fechad_ant,
                        'vehiculo' => $placan. " " . $modelon . " " . $capacidadn . "Kg",
                        'destino' => $destino,
                        'chofer' => $nombren,
                        'fechad' => $fechad,
                    )
                );

                # enviar correo
                $status_send = Email::send_email(
                    $dataEmail['title'],
                    $dataEmail['body'],
                    $dataEmail['recipients'],
                );
            }
        }

        ($actualizar_despacho) ? ($output["mensaje"] = "ACTUALIZADO CORRECTAMENTE") : ($output["mensaje"] = "ERROR");

        echo json_encode($output);
        break;

    case "actualizar_documento_en_despacho":

        $actualizar_documento = false;

        $correlativo = $_POST["correlativo"];
        $documento_nuevo = $_POST["documento_nuevo"];
        $documento_viejo = $_POST["documento_viejo"];
        $tipodoc_nuevo = $_POST["tipodoc_nuevo"];
        $tipodoc_viejo = $_POST["tipodoc_viejo"];

        # evalua si no son iguales el documento ingresado al original
        if (!hash_equals($documento_nuevo, $documento_viejo))
        {
            # tipodoc 'f' es Factura
            if ($tipodoc_nuevo == 'f')
            {
                # consultamos si la factura existe en la bd
                $existe_factura = $despachos->getFactura($documento_nuevo);
                if (ArraysHelpers::validate($existe_factura))
                {
                    # validamos si existe la factura (tipo A) en despacho
                    $existe_fact_en_despachos = $despachos->getExisteDocumentoEnDespachos($documento_nuevo, 'A');
                    if(!ArraysHelpers::validate($existe_fact_en_despachos))
                    {
                        # si no existe el numerod en despacho
                        # validamos y obtenemos el peso (tara) y cubicaje del documento
                        $arr_tara = $despachos->getCubicajeYPesoTotalporFactura($documento_nuevo);
                        if (ArraysHelpers::validate($arr_tara)) {
                            $peso = 0;
                            $cubicaje = 0;
                            # obtenemos el peso y cubicaje del documento
                            $peso_cubicaje = DespachosHelpers::getWeightAndCubicCapacity($arr_tara);
                            if (ArraysHelpers::validate($peso_cubicaje)) {
                                $peso = $peso_cubicaje['tara'];
                                $cubicaje = $peso_cubicaje['cubicaje'];
                            }

                            $peso_acum = 0;
                            $cubicaje_acum = 0;
                            # obtenemos los documentos del despacho
                            # para obtener el peso acumulado y cubicaje acumulado
                            $arr_documentos = $despachos->getDocumentosPorCorrelativo($correlativo);
                            if (ArraysHelpers::validate($arr_documentos)) {
                                foreach ($arr_documentos as $documento) {
                                    $arr_tara = array();
                                    switch ($documento['tipofac']) {
                                        case 'A': $arr_tara = $despachos->getCubicajeYPesoTotalporFactura($documento['numerod']); break;
                                        case 'C': $arr_tara = $despachos->getCubicajeYPesoTotalporNotaDeEntrega($documento['numerod']); break;
                                    }

                                    # obtenemos el peso y cubicaje del documento existente en despacho
                                    $peso_cubicaje = DespachosHelpers::getWeightAndCubicCapacity($arr_tara);
                                    if (ArraysHelpers::validate($peso_cubicaje)) {
                                        $peso_acum += $peso_cubicaje['tara'];
                                        $cubicaje_acum += $peso_cubicaje['cubicaje'];
                                    }
                                }
                            }

                            $peso_max = 0;
                            $cubicaje_max = 0;
                            # obtenemos los datos del despacho para obtener los datos del vehiculo
                            $data_despacho = $despachos->get_despacho_por_id($correlativo);
                            foreach ($data_despacho as $placa_vehi) {
                                $placa = $placa_vehi["placa"];
                            }
                            if (ArraysHelpers::validate($data_despacho)) {
                                $data_vehiculo = Vehiculo::getById($placa);

                                foreach ($data_vehiculo as $dat_vehi) {
                                  $capacidad = $dat_vehi["capacidad"];
                                  $volumen = $dat_vehi["volumen"];
                                }

                                $peso_max = Numbers::avoidNull( ArraysHelpers::validateWithPos($capacidad ));
                                $cubicaje_max = Numbers::avoidNull( ArraysHelpers::validateWithPos($volumen ));
                            }

                            # valida el peso y obtinene el porcentaje
                            $response = DespachosHelpers::validateWeightAndCubicCapacityInExistingDispatch(
                                array(
                                    'peso'      => $peso,
                                    'peso_acum' => $peso,
                                    'peso_max'  => $peso_max,
                                    'cubicaje_acum' => $cubicaje,
                                    'cubicaje'      => $cubicaje,
                                    'cubicaje_max'  => $cubicaje_max,
                                )
                            );
                            $output = $response;

                            # verifica si el peso esta dentro del rango
                          /*  if ($response['cond'] == true)
                            {*/
                               /* $factura_estado_1 = $relacion->get_documentos_por_correlativo($correlativo);
                                if (count($factura_estado_1) == 0) {*/
                                    # si cumple con todas las condiciones ACTUALIZA la factura en un despacho en especifico
                                    $actualizar_documento = $despachos->updateDetalleDespacho(
                                        $correlativo, $documento_nuevo, $documento_viejo, 'A', $tipodoc_viejo
                                    );

                                    $despacho = ArraysHelpers::validateWithPos(
                                        $relacion->get_despacho_por_correlativo($correlativo),
                                        0
                                    );

                                    /**  enviar correo: despachos_edita_3 **/
                                    if ($actualizar_documento) {
                                        # preparamos los datos a enviar
                                        $dataEmail = EmailData::DataDespachoEditarDocumento(
                                            array(
                                                'usuario' => $_SESSION['login'],
                                                'correl_despacho' => $correlativo,
                                                'nroplanilla' => '0', #PENDIENTE
                                                'destino' => $despacho['nota'],
                                                'chofer' => $despacho['cedula_chofer'],
                                                'doc_viejo' => $documento_viejo,
                                                'doc_nuevo' => $documento_nuevo,
                                            )
                                        );

                                        # enviar correo
                                        $status_send = Email::send_email(
                                            $dataEmail['title'],
                                            $dataEmail['body'],
                                            $dataEmail['recipients'],
                                        );
                                    }
                               // }

                                # verificamos que se haya realizado la actualizacion correctamente y devolvemos el mensaje
                                ($actualizar_documento)
                                    ? ($output["mensaje"] = "ACTUALIZADO CORRECTAMENTE")
                                    : ($output["mensaje"] = "ERROR AL ACTUALIZAR");

                            /* } else {
                                $output["mensaje"] = ("El vehículo excede el límite de peso!");
                            }*/
                        } else {
                            $output["mensaje"] = ("Error al evaluar el peso y cubicaje");
                        }
                    } else {
                        $output["mensaje"] = ("El Número de Factura: $documento_nuevo Ya Fue Despachado");
                    }
                } else {
                    $output["mensaje"] = "El Número de Factura $documento_nuevo No Existe en Sistema";
                }
            }
            # tipodoc 'n' es Nota de Entrega
            elseif ($tipodoc_nuevo == 'n')
            {
                # consultamos si la nota de entrega existe en la bd
                $existe_nota_de_entrega = $despachos->getNotaDeEntrega($documento_nuevo);
                if (ArraysHelpers::validate($existe_nota_de_entrega))
                {
                    # validamos si existe la nota de entrega (tipo C) en despacho
                    $existe_notadeentrega_en_despachos = $despachos->getExisteDocumentoEnDespachos($documento_nuevo, 'C');
                    if(!ArraysHelpers::validate($existe_notadeentrega_en_despachos))
                    {
                        # si no existe el numerod en despacho
                        # validamos el peso (tara) de la nota de entrega
                        $arr_tara = $despachos->getCubicajeYPesoTotalporNotaDeEntrega($documento_nuevo);
                        if (ArraysHelpers::validate($arr_tara)) {
                            $peso = 0;
                            $cubicaje = 0;
                            # obtenemos el peso y cubicaje del documento
                            $peso_cubicaje = DespachosHelpers::getWeightAndCubicCapacity($arr_tara);
                            if (ArraysHelpers::validate($peso_cubicaje)) {
                                $peso = $peso_cubicaje['tara'];
                                $cubicaje = $peso_cubicaje['cubicaje'];
                            }

                            $peso_acum = 0;
                            $cubicaje_acum = 0;
                            # obtenemos los documentos del despacho
                            # para obtener el peso acumulado y cubicaje acumulado
                            $arr_documentos = $despachos->getDocumentosPorCorrelativo($correlativo);
                            if (ArraysHelpers::validate($arr_documentos)) {
                                foreach ($arr_documentos as $documento) {
                                    $arr_tara = array();
                                    switch ($documento['TipoFac']) {
                                        case 'A': $arr_tara = $despachos->getCubicajeYPesoTotalporFactura($documento['numeros']); break;
                                        case 'C': $arr_tara = $despachos->getCubicajeYPesoTotalporNotaDeEntrega($documento['numeros']); break;
                                    }

                                    # obtenemos el peso y cubicaje del documento existente en despacho
                                    $peso_cubicaje = DespachosHelpers::getWeightAndCubicCapacity($arr_tara);
                                    if (ArraysHelpers::validate($peso_cubicaje)) {
                                        $peso_acum += $peso_cubicaje['tara'];
                                        $cubicaje_acum += $peso_cubicaje['cubicaje'];
                                    }
                                }
                            }

                            $peso_max = 0;
                            $cubicaje_max = 0;
                            # obtenemos los datos del despacho para obtener los datos del vehiculo
                            $data_despacho = $despachos->get_despacho_por_id($correlativo);
                             foreach ($data_despacho as $placa_vehi) {
                                $placa = $placa_vehi["placa"];
                            }
                            if (ArraysHelpers::validate($data_despacho)) {
                                $data_vehiculo = Vehiculo::getById($placa);

                                foreach ($data_vehiculo as $dat_vehi) {
                                  $capacidad = $dat_vehi["capacidad"];
                                  $volumen = $dat_vehi["volumen"];
                                }

                                $peso_max = Numbers::avoidNull( ArraysHelpers::validateWithPos($capacidad ));
                                $cubicaje_max = Numbers::avoidNull( ArraysHelpers::validateWithPos($volumen ));
                            }

                           /* echo "<script>console.log('peso: " . $peso . "' );</script>";
                            echo "<script>console.log('peso_acum: " . $peso_acum . "' );</script>";
                            echo "<script>console.log('peso_max: " . $peso_max . "' );</script>";
                            echo "<script>console.log('cubicaje_acum: " . $cubicaje_acum . "' );</script>";
                            echo "<script>console.log('cubicaje: " . $cubicaje . "' );</script>";
                            echo "<script>console.log('cubicaje_max: " . $cubicaje_max . "' );</script>";*/



                            # valida el peso y obtinene el porcentaje
                            $response = DespachosHelpers::validateWeightAndCubicCapacityInExistingDispatch(
                                array(
                                    'peso'      => $peso,
                                    'peso_acum' => $peso,
                                    'peso_max'  => $peso_max,
                                    'cubicaje_acum' => $cubicaje,
                                    'cubicaje'      => $cubicaje,
                                    'cubicaje_max'  => $cubicaje_max,
                                )
                            );
                            $output = $response;

                            # verifica si el peso esta dentro del rango
                           /* if ($response['cond'] == true)
                            {*/
                               /* $notadeentrega_estado_1 = $relacion->get_documentos_por_correlativo($correlativo);
                                if (count($notadeentrega_estado_1) == 0) {*/
                                    $actualizar_documento = $despachos->updateDetalleDespacho(
                                        $correlativo, $documento_nuevo, $documento_viejo, 'C', $tipodoc_viejo
                                    );

                                    $despacho = ArraysHelpers::validateWithPos(
                                        $relacion->get_despacho_por_correlativo($correlativo),
                                        0
                                    );

                                    /**  enviar correo: despachos_edita_3 **/
                                    if ($actualizar_documento) {
                                        # preparamos los datos a enviar
                                        $dataEmail = EmailData::DataDespachoEditarDocumento(
                                            array(
                                                'usuario' => $_SESSION['login'],
                                                'correl_despacho' => $correlativo,
                                                'nroplanilla' => '0', #PENDIENTE
                                                'destino' => $despacho['nota'],
                                                'chofer' => $despacho['cedula_chofer'],
                                                'doc_viejo' => $documento_viejo,
                                                'doc_nuevo' => $documento_nuevo,
                                            )
                                        );

                                        # enviar correo
                                        $status_send = Email::send_email(
                                            $dataEmail['title'],
                                            $dataEmail['body'],
                                            $dataEmail['recipients'],
                                        );
                                    }
                                //}

                                # verificamos que se haya realizado la actualizacion correctamente y devolvemos el mensaje
                                ($actualizar_documento)
                                    ? ($output["mensaje"] = "ACTUALIZADO CORRECTAMENTE")
                                    : ($output["mensaje"] = "ERROR AL ACTUALIZAR");
                                # verificamos que se haya realizado la insercion correctamente

                            /* } else {
                                $output["mensaje"] = ("El vehículo excede el límite de peso!");
                            }*/
                        } else {
                            $output["mensaje"] = ("Error al evaluar el peso y cubicaje");
                        }
                    } else {
                        $output["mensaje"] = ("El Número de Nota de Entrega: $documento_nuevo Ya Fue Despachado");
                    }
                } else {
                    $output["mensaje"] = "El Número de Nota de Entrega $documento_nuevo No Existe en Sistema";
                }
            }
        } else {
            $output["mensaje"] = ('ATENCION! Por favor ingrese un documento diferente');
        }

        echo json_encode($output);
        break;

    case "eliminar_documento_en_despacho":
        $eliminar_documento = false;
         $nroplanilla=$contar = 0;

        $correlativo = $_POST["correlativo"];
        $nro_documento = $_POST["nro_documento"];
        $tipofac = $_POST["tipodoc"];

        # validamos si el despacho existe en la bd
        $despacho = $relacion->get_despacho_por_correlativo($correlativo);
        if (ArraysHelpers::validate($despacho))
        {
            # validamos si existe el documento en despacho
            $existe_en_despachos = $despachos->getExisteDocumentoEnDespachos($nro_documento, $tipofac);
            if (ArraysHelpers::validate($existe_en_despachos))
            {


                # eliminamos de un despacho en especifico
       
                    $del=$despachos->eliminar_del_documento($correlativo,$nro_documento);


                # eliminamos de un despacho en especifico
              /*  $eliminar_documento = $despachos->deleteDetalleDespacho($correlativo, $nro_documento, $tipofac);

                /**  enviar correo: despachos_elimina_fact **/
                if ($del) {
                    # preparamos los datos a enviar
                    $dataEmail = EmailData::DataEliminarDocumentoDespacho(
                        array(
                            'usuario' => $_SESSION['login'],
                            'correl_despacho' => $correlativo,
                            'nroplanilla' => '0', #PENDIENTE
                            'doc' => $nro_documento,
                        )
                    );

                    # enviar correo
                    $status_send = Email::send_email(
                        $dataEmail['title'],
                        $dataEmail['body'],
                        $dataEmail['recipients'],
                    );
                    $output["mensaje"] = 'ELIMINADO EXITOSAMENTE';
                }
            } else {
                $output["mensaje"] = "El Número de Documento: $nro_documento No Existe en Despachos";
            }
        } else {
            $output["mensaje"] = "ERROR AL ELIMINAR";
        }

        echo json_encode($output);
        break;



case "eliminar_un_despacho":
        $eliminar_documento = false;
        $contar = 0;

        $correlativo = $_POST["correlativo"];
       /* $nro_documento = $_POST["nro_documento"];
        $tipofac = $_POST["tipodoc"];*/

        # validamos si el despacho existe en la bd
        $despacho = $relacion->get_despacho_por_correlativo($correlativo);
        if (ArraysHelpers::validate($despacho))
        {
                # eliminamos de un despacho en especifico
 

                    $del=$despachos->eliminar_del($correlativo);
                    $del_det=$despachos->eliminar_del_det($correlativo);


               


                # eliminamos de un despacho en especifico
              //$eliminar_documento = $despachos->deleteDetalleDespacho($correlativo, $nro_documento, $tipofac);

                /**  enviar correo: despachos_elimina_fact **/
               if ($del && $del_det) {
                    # preparamos los datos a enviar
                     $dataEmail = EmailData::DataEliminarDespacho(
                    array(
                        'usuario' => $_SESSION['login'],
                        'correl_despacho' => $correlativo,
                        'nroplanilla' => '0', #PENDIENTE
                    )
                );

                # enviar correo
                $status_send = Email::send_email(
                    $dataEmail['title'],
                    $dataEmail['body'],
                    $dataEmail['recipients'],
                );
                    $output["mensaje"] = 'ELIMINADO EXITOSAMENTE';
                }
            
        } else {
            $output["mensaje"] = "ERROR AL ELIMINAR";
        }

        echo json_encode($output);
        break;



    case "agregar_documento_en_despacho":
        $output = array("cond" => false);
        $insertar_documento = false;

        $correlativo = $_POST["correlativo"];
        $nro_documento = $_POST["documento_agregar"];
        $tipodoc = $_POST["tipodoc"];

        # tipodoc 'f' es Factura
        if ($tipodoc == 'f')
        {
            # consultamos si la factura existe en la bd
            $existe_factura = $despachos->getFactura($nro_documento);
            if (ArraysHelpers::validate($existe_factura))
            {
                # validamos si existe la factura (tipo A) en despacho
                $existe_fact_en_despachos = $despachos->getExisteDocumentoEnDespachos($nro_documento, 'A');
                if(!ArraysHelpers::validate($existe_fact_en_despachos))
                {
                    # si no existe el numerod en despacho
                    # validamos y obtenemos el peso (tara) y cubicaje del documento
                    $arr_tara = $despachos->getCubicajeYPesoTotalporFactura($nro_documento);
                    if (ArraysHelpers::validate($arr_tara)) {
                        $peso = 0;
                        $cubicaje = 0;
                        # obtenemos el peso y cubicaje del documento
                        $peso_cubicaje = DespachosHelpers::getWeightAndCubicCapacity($arr_tara);
                        if (ArraysHelpers::validate($peso_cubicaje)) {
                            $peso = $peso_cubicaje['tara'];
                            $cubicaje = $peso_cubicaje['cubicaje'];
                        }

                        $peso_acum = 0;
                        $cubicaje_acum = 0;
                        # obtenemos los documentos del despacho
                        # para obtener el peso acumulado y cubicaje acumulado
                        $arr_documentos = $despachos->getDocumentosPorCorrelativo($correlativo);
                        if (ArraysHelpers::validate($arr_documentos)) {
                            foreach ($arr_documentos as $documento) {
                                $arr_tara = array();
                                switch ($documento['TipoFac']) {
                                    case 'A': $arr_tara = $despachos->getCubicajeYPesoTotalporFactura($documento['numeros']); break;
                                    case 'C': $arr_tara = $despachos->getCubicajeYPesoTotalporNotaDeEntrega($documento['numeros']); break;
                                }

                                # obtenemos el peso y cubicaje del documento existente en despacho
                                $peso_cubicaje = DespachosHelpers::getWeightAndCubicCapacity($arr_tara);
                                if (ArraysHelpers::validate($peso_cubicaje)) {
                                    $peso_acum += $peso_cubicaje['tara'];
                                    $cubicaje_acum += $peso_cubicaje['cubicaje'];
                                }
                            }
                        }

                        $peso_max = 0;
                        $cubicaje_max = 0;
                        # obtenemos los datos del despacho para obtener los datos del vehiculo
                        $data_despacho = $despachos->get_despacho_por_id($correlativo);
                         foreach ($data_despacho as $placa_vehi) {
                                $placa = $placa_vehi["placa"];
                            }
                            if (ArraysHelpers::validate($data_despacho)) {
                                $data_vehiculo = Vehiculo::getById($placa);

                                foreach ($data_vehiculo as $dat_vehi) {
                                  $capacidad = $dat_vehi["capacidad"];
                                  $volumen = $dat_vehi["volumen"];
                                }

                                $peso_max = Numbers::avoidNull( ArraysHelpers::validateWithPos($capacidad ));
                                $cubicaje_max = Numbers::avoidNull( ArraysHelpers::validateWithPos($volumen ));
                            }

                      /*  echo "<script>console.log('peso: " . $peso . "' );</script>";
                            echo "<script>console.log('peso_acum: " . $peso_acum . "' );</script>";
                            echo "<script>console.log('peso_max: " . $peso_max . "' );</script>";
                            echo "<script>console.log('cubicaje_acum: " . $cubicaje_acum . "' );</script>";
                            echo "<script>console.log('cubicaje: " . $cubicaje . "' );</script>";
                            echo "<script>console.log('cubicaje_max: " . $cubicaje_max . "' );</script>";*/


                        # valida el peso y obtinene el porcentaje
                        $response = DespachosHelpers::validateWeightAndCubicCapacityInExistingDispatch(
                            array(
                                'peso'      => $peso,
                                'peso_acum' => $peso_acum,
                                'peso_max'  => $peso_max,
                                'cubicaje_acum' => $cubicaje_acum,
                                'cubicaje'      => $cubicaje,
                                'cubicaje_max'  => $cubicaje_max,
                            )
                        );
                        $output = $response;

                        # verifica si el peso esta dentro del rango
                        if ($response['cond'] == true)
                        {
                            $factura_estado_1 = $relacion->get_documentos_por_correlativo($correlativo);
                            if (count($factura_estado_1) == 0) {
                                # si cumple con todas las condiciones INSERTA la factura en un despacho en especifico
                                $insertar_documento = $despachos->insertarDetalleDespacho(
                                    $correlativo, $nro_documento, 'A', ''
                                );

                                $despacho = ArraysHelpers::validateWithPos(
                                    $relacion->get_despacho_por_correlativo($correlativo),
                                    0
                                );

                                /**  enviar correo: despachos_edita_4 **/
                                if ($insertar_documento) {
                                    # preparamos los datos a enviar
                                    $dataEmail = EmailData::DataDespachoAgregarDocumento(
                                        array(
                                            'usuario' => $_SESSION['login'],
                                            'correl_despacho' => $correlativo,
                                            'nroplanilla' => '0', #PENDIENTE
                                            'destino' => $despacho['destino'],
                                            'chofer' => $despacho['NomperChofer'],
                                            'doc' => $nro_documento,
                                        )
                                    );

                                    # enviar correo
                                    $status_send = Email::send_email(
                                        $dataEmail['title'],
                                        $dataEmail['body'],
                                        $dataEmail['recipients'],
                                    );
                                }
                            }

                            # verificamos que se haya realizado la insercion correctamente
                            # y devolvemos el mensaje
                            ($insertar_documento)
                                ? $output["mensaje"] = ("INSERTADO CORRECTAMENTE ")
                                : $output["mensaje"] = ("ERROR AL INSERTAR");


                        } else {
                            $output["mensaje"] = ("El vehículo excede el límite de peso!");
                        }
                    } else {
                        $output["mensaje"] = ("Error al evaluar el peso y cubicaje");
                    }
                } else {
                    $output["mensaje"] = ("El Número de Factura: $nro_documento Ya Fue Despachado");
                }
            } else {
                $output["mensaje"] = "El Número de Factura $nro_documento No Existe en Sistema";
            }
        }
        # tipodoc 'n' es Nota de Entrega
        elseif ($tipodoc == 'n')
        {
            # consultamos si la nota de entrega existe en la bd
            $existe_nota_de_entrega = $despachos->getNotaDeEntrega($nro_documento);
            if (ArraysHelpers::validate($existe_nota_de_entrega))
            {
                # validamos si existe la nota de entrega (tipo C) en despacho
                $existe_notadeentrega_en_despachos = $despachos->getExisteDocumentoEnDespachos($nro_documento, 'C');
                if(!ArraysHelpers::validate($existe_notadeentrega_en_despachos))
                {
                    # si no existe el numerod en despacho
                    # validamos el peso (tara) de la nota de entrega
                    $arr_tara = $despachos->getCubicajeYPesoTotalporNotaDeEntrega($nro_documento);
                    if (ArraysHelpers::validate($arr_tara)) {
                        $peso = 0;
                        $cubicaje = 0;
                        # obtenemos el peso y cubicaje del documento
                        $peso_cubicaje = DespachosHelpers::getWeightAndCubicCapacity($arr_tara);
                        if (ArraysHelpers::validate($peso_cubicaje)) {
                            $peso = $peso_cubicaje['tara'];
                            $cubicaje = $peso_cubicaje['cubicaje'];
                        }

                        $peso_acum = 0;
                        $cubicaje_acum = 0;
                        # obtenemos los documentos del despacho
                        # para obtener el peso acumulado y cubicaje acumulado
                        $arr_documentos = $despachos->getDocumentosPorCorrelativo($correlativo);
                        if (ArraysHelpers::validate($arr_documentos)) {
                            foreach ($arr_documentos as $documento) {
                                $arr_tara = array();
                                switch ($documento['TipoFac']) {
                                    case 'A': $arr_tara = $despachos->getCubicajeYPesoTotalporFactura($documento['numeros']); break;
                                    case 'C': $arr_tara = $despachos->getCubicajeYPesoTotalporNotaDeEntrega($documento['numeros']); break;
                                }

                                # obtenemos el peso y cubicaje del documento existente en despacho
                                $peso_cubicaje = DespachosHelpers::getWeightAndCubicCapacity($arr_tara);
                                if (ArraysHelpers::validate($peso_cubicaje)) {
                                    $peso_acum += $peso_cubicaje['tara'];
                                    $cubicaje_acum += $peso_cubicaje['cubicaje'];
                                }
                            }
                        }

                        $peso_max = 0;
                        $cubicaje_max = 0;
                        # obtenemos los datos del despacho para obtener los datos del vehiculo
                        $data_despacho = $despachos->get_despacho_por_id($correlativo);
                        if (ArraysHelpers::validate($data_despacho)) {
                            $data_vehiculo = Vehiculo::getById($data_despacho[0]['placa']);
                            $peso_max = Numbers::avoidNull( ArraysHelpers::validateWithPos($data_vehiculo, 0)['capacidad'] );
                            $cubicaje_max = Numbers::avoidNull( ArraysHelpers::validateWithPos($data_vehiculo, 0)['volumen'] );
                        }

                        # valida el peso y obtinene el porcentaje
                        $response = DespachosHelpers::validateWeightAndCubicCapacityInExistingDispatch(
                            array(
                                'peso'      => $peso,
                                'peso_acum' => $peso_acum,
                                'peso_max'  => $peso_max,
                                'cubicaje_acum' => $cubicaje_acum,
                                'cubicaje'      => $cubicaje,
                                'cubicaje_max'  => $cubicaje_max,
                            )
                        );
                        $output = $response;

                        # verifica si el peso esta dentro del rango
                       /* if ($response['cond'] == true)
                        {*/
                           /* $notadeentrega_estado_1 = $relacion->get_documentos_por_correlativo($correlativo);
                            if (count($notadeentrega_estado_1) == 0) {*/
                                # si cumple con todas las condiciones INSERTA la factura en un despacho en especifico
                                $insertar_documento = $despachos->insertarDetalleDespacho(
                                    $correlativo, $nro_documento, 'C', ''
                                );

                                $despacho = ArraysHelpers::validateWithPos(
                                    $relacion->get_despacho_por_correlativo($correlativo),
                                    0
                                );

                                /**  enviar correo: despachos_edita_4 **/
                                if ($insertar_documento) {
                                    # preparamos los datos a enviar
                                    $dataEmail = EmailData::DataDespachoAgregarDocumento(
                                        array(
                                            'usuario' => $_SESSION['login'],
                                            'correl_despacho' => $correlativo,
                                            'nroplanilla' => '0', #PENDIENTE
                                            'destino' => $despacho['nota'],
                                            'chofer' => $despacho['cedula_chofer'],
                                            'doc' => $nro_documento,
                                        )
                                    );

                                    # enviar correo
                                    $status_send = Email::send_email(
                                        $dataEmail['title'],
                                        $dataEmail['body'],
                                        $dataEmail['recipients'],
                                    );
                                }
                         //   }

                            # verificamos que se haya realizado la insercion correctamente
                            # y devolvemos el mensaje
                            ($insertar_documento)
                                ? $output["mensaje"] = ("INSERTADO CORRECTAMENTE ")
                                : $output["mensaje"] = ("ERROR AL INSERTAR");


                        /* } else {
                            $output["mensaje"] = ("El vehículo excede el límite de peso!");
                        }*/
                    } else {
                        $output["mensaje"] = ("Error al evaluar el peso y cubicaje");
                    }
                } else {
                    $output["mensaje"] = ("El Número de Nota de Entrega: $nro_documento Ya Fue Despachado");
                }
            } else {
                $output["mensaje"] = "El Número de Nota de Entrega $nro_documento No Existe en Sistema";
            }
        }

        echo json_encode($output);
        break;

    case "eliminar_un_despacho332":

        $eliminar_despacho = false;
        $correlativo = $_POST["correlativo"];

        $existe_despacho = $despachos->get_despacho_por_id($correlativo);
        if (ArraysHelpers::validate($existe_despacho)) {
            //eliminamos de un despacho en especifico
            $eliminar_despacho = $despachos->deleteDespacho($correlativo);

            /**  enviar correo: despachos_elimina **/
            if ($eliminar_despacho) {
                # preparamos los datos a enviar
                $dataEmail = EmailData::DataEliminarDespacho(
                    array(
                        'usuario' => $_SESSION['login'],
                        'correl_despacho' => $correlativo,
                        'nroplanilla' => '0', #PENDIENTE
                    )
                );

                # enviar correo
                $status_send = Email::send_email(
                    $dataEmail['title'],
                    $dataEmail['body'],
                    $dataEmail['recipients'],
                );
            }
        }

        //verificamos que se haya realizado la eliminacion del documento correctamente y devolvemos el mensaje
        if ($eliminar_despacho) {
            $output["mensaje"] = 'ELIMINADO EXITOSAMENTE';
            $output["icono"] = "success";
        } else {
            $output["mensaje"] = 'ERROR AL ELIMINAR';
            $output["icono"] = "error";
        }

        echo json_encode($output);
        break;

    case "listar_productos_de_un_despacho":

        # correlativo
        $correlativo = $_POST["correlativo"];

        # obtenemos los registros de los productos en dichos documentos
        $productosDespacho = array();
        $datos_f = $despachos->getProductosDespachoCreadoEnFacturas($correlativo);
        $datos_n = $despachos->getProductosDespachoCreadoEnNotaDeEntrega($correlativo);

        foreach (array($datos_f, $datos_n) as $dato) {
            foreach ($dato as $row) {
                $arr = array_map(function ($arr) { return $arr['coditem']; }, $productosDespacho);

                if (!in_array($row['coditem'], $arr)) {
                    #no existe en el array
                    $productosDespacho[] = $row;
                } else {
                    # si existe en el array
                    $pos = array_search($row['coditem'], $arr);
                    $productosDespacho[$pos]['bultos'] += intval($row['bultos']);
                    $productosDespacho[$pos]['paquetes'] += intval($row['paquetes']);
                }
            }
        }

        # DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        $total_bultos = 0;
        $total_paq = 0;
        foreach ($productosDespacho as $key => $row) {

            # DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            # REALIZAMOS PROCESOS DE CALCULO
            $bultos = 0;
            $paq = 0;
            if ($row["bultos"] > 0){
                $bultos = $row["bultos"];
            }
            if ($row["paquetes"] > 0){
                $paq = $row["paquetes"];
            }

            if ($row["esempaque"] != 0){
                if ($row["paquetes"] > $row["cantempaq"]){

                    if ($row["cantempaq"] != 0) {
                        $bultos_total = $row["paquetes"] / $row["cantempaq"];
                    }else{
                        $bultos_total = 0;
                    }
                    $decimales = explode(".",$bultos_total);
                    $bultos_deci = $bultos_total - $decimales[0];
                    $paq = $bultos_deci * $row["cantempaq"];
                    $bultos = $decimales[0] + $bultos;
                }
            }
            $total_bultos += $bultos;
            $total_paq += $paq;

            # agregamos al sub array
            $sub_array[] = $key+1;
            $sub_array[] = $row["coditem"];
            $sub_array[] = $row["descrip"];
            $sub_array[] = round($bultos);
            $sub_array[] = round($paq);

            # agregamos un registro al array principal
            $data[] = $sub_array;
        }

        # totalizado que se imprimira en la parte inferior de la tabla
        $output["total_bultos"] = $total_bultos;
        $output["total_paq"] = $total_paq;

        # RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output['tabla'] = array(
            "sEcho" => 1, # INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), # ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), # ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data);

        echo json_encode($output);
        break;

}