<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
session_start();
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO
require_once("despachos_modelo.php");

//INSTANCIAMOS EL MODELO
$despachos  = new Despachos();

$usuario = $_SESSION["cedula"];

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "obtener_pesomaxvehiculo":

        $peso_max_vehiculo = Vehiculo::getById($_POST["id"]);

        if (ArraysHelpers::validate($peso_max_vehiculo)) {
            $output["capacidad"] = $peso_max_vehiculo[0]["capacidad"];
            $output["cubicajeMax"] = $peso_max_vehiculo[0]["volumen"];
        } else {
            $output["capacidad"] = 0;
            $output["cubicajeMax"] = 0;
        }

        echo json_encode($output);
        break;

    case "validar_documento_para_anadir":
        $datos = array();
        $output = array("cond" => false);
        $tipodoc ='';
        $aux ='';
        $numerod = $_POST["documento"];
        $registros_por_despachar = $_POST["registros_por_despachar"];
        $peso_acum = str_replace(",", ".", $_POST["peso_acum_documentos"]);
        $peso_max  = str_replace(",", ".", $_POST["peso_max_vehiculo"]);
        $cubicaje_acum  = str_replace(",", ".", $_POST["cubicaje_acum_documentos"]);
        $cubicaje_max   = str_replace(",", ".", $_POST["cubicaje_max_vehiculo"]);

        /*echo "<script>console.log('registros_por_despachar: " . $registros_por_despachar . "' );</script>";
        echo "<script>console.log('peso_acum: " . $peso_acum . "' );</script>";*/

         $traerTipoDoc = $despachos->DocumentosporDespachar( $numerod);

          foreach ($traerTipoDoc as $row) {

            $aux = $row["TipoFac"];
          }

          if($aux == 'A' ){
                $tipodoc ='f';
          }else{
            if($aux == 'C'){
                $tipodoc ='n';
            }
          }



        # tipodoc 'f' es Factura
        if ($tipodoc == 'f')
        {
            # obtenemos el parametros de la configJson para validar
            $numerod_doc_json = ConfigJson::getParameterOfModule(Functions::getNameDirectory(), 'numerod_factura');
            if ($numerod > $numerod_doc_json)
            {
                # valida el documento exista
                $datos = $despachos->getFactura($numerod);
                if (ArraysHelpers::validate($datos))
                {
                    # si existe el documento
                    # validamos que no este en el formato antes de ingresar
                    if (DespachosHelpers::validateExistDocumentInString($registros_por_despachar, $numerod, 'A') == 0)
                    {
                        # validamos si existe la factura (tipo A) en despacho
                        $existe_fact = $despachos->getExisteDocumentoEnDespachos($numerod, 'A');
                        if(!ArraysHelpers::validate($existe_fact))
                        {
                            # si no existe el numerod en despacho
                            # validamos el peso (tara) de la factura
                            $arr_tara = $despachos->getCubicajeYPesoTotalporFactura($numerod);
                            if (ArraysHelpers::validate($arr_tara)) {
                                $peso = $cubicaje = 0;
                                foreach ($arr_tara as $tara) {
                                    # valida que si es bulto (0) o paquete (1)
                                    if ($tara['unidad'] == 0) {
                                        $peso += ($tara['tara'] * $tara['cantidad']);
                                    } else {
                                        $peso += (($tara['tara'] / $tara['paquetes']) * $tara['cantidad']);
                                    }
                                    $cubicaje += $tara['cubicaje'];
                                }

                                # valida el peso y obtinene el porcentaje
                                $response = DespachosHelpers::validateWeightAndCubicCapacity(
                                    array(
                                        'peso'      => $peso,
                                        'peso_acum' => $peso,//$peso_acum,
                                        'peso_max'  => $peso,//$peso_max,
                                        'cubicaje_acum' => $cubicaje,//$cubicaje_acum,
                                        'cubicaje'      => $cubicaje,
                                        'cubicaje_max'  => $cubicaje,//$cubicaje_max,
                                    )
                                );
                                $output = $response;

                                # verifica si el peso esta dentro del rango
                              /*  if ($response['cond'] == true)
                                {*/
                                    # responde con todos los datos necesarios
                                    $output['peso']     = $peso;
                                    $output['cubicaje'] = $cubicaje;
                                    $output['numerod']  = $numerod;
                                    $output['tipodoc']  = $datos[0]['tipofac'];
                                /*  } else {
                                    $output["mensaje"] = ("El vehículo excede el límite de peso!");
                                }*/
                            } else {
                                $output["mensaje"] = ("Error al evaluar el peso y cubicaje");
                            }
                        } else {
                            $output["mensaje"] = ("El Número de Factura: $numerod Ya Fue Agregado en Otro Despacho");
                        }
                    } else {
                        $output["mensaje"] = "El Número de Factura: $numerod, Ya fue Agregado";
                    }
                } else {
                    $output["mensaje"] = "El Número de Factura $numerod No Existe en Sistema";
                }
            } else {
                $output["mensaje"] = "El Número de Factura $numerod Está fuera de rango";
            }
        }

        # tipodoc 'n' es Nota de Entrega
        elseif ($tipodoc == 'n')
        {
            # obtenemos el parametros de la configJson para validar
            $numerod_doc_json = ConfigJson::getParameterOfModule(Functions::getNameDirectory(), 'numerod_nota');
            if ($numerod > $numerod_doc_json)
            {
                # valida el documento exista
                $datos = $despachos->getNotaDeEntrega($numerod);
                if (ArraysHelpers::validate($datos))
                {
                    # si existe el documento
                    # validamos que no este en el formato antes de ingresar
                    if (DespachosHelpers::validateExistDocumentInString($registros_por_despachar, $numerod, 'C') == 0)
                    {
                        # validamos si existe la nota de entrega (tipo C) en despacho
                        $existe_fact = $despachos->getExisteDocumentoEnDespachos($numerod, 'C');
                        if(!ArraysHelpers::validate($existe_fact))
                        {
                            # si no existe el numerod en despacho
                            # validamos el peso (tara) de la nota de entrega
                            $arr_tara = $despachos->getCubicajeYPesoTotalporNotaDeEntrega($numerod);
                            if (ArraysHelpers::validate($arr_tara)) {
                                $peso = 0;
                                $cubicaje = 0;
                                foreach ($arr_tara as $tara) {
                                    # valida que si es bulto (0) o paquete (1)
                                    if ($tara['unidad'] == 0) {
                                        $peso += ($tara['tara'] * $tara['cantidad']);
                                    } else {
                                        $peso += (($tara['tara'] / $tara['paquetes']) * $tara['cantidad']);
                                    }
                                    $cubicaje += $tara['cubicaje'];
                                }

                                # valida el peso y obtinene el porcentaje
                                $response = DespachosHelpers::validateWeightAndCubicCapacity(
                                    array(
                                        'peso'      => $peso,
                                        'peso_acum' => $peso,//$peso_acum,
                                        'peso_max'  => $peso,//$peso_max,
                                        'cubicaje_acum' => $cubicaje,//$cubicaje_acum,
                                        'cubicaje'      => $cubicaje,
                                        'cubicaje_max'  => $cubicaje,//$cubicaje_max,
                                    )
                                );
                               $output = $response;

                                # verifica si el peso esta dentro del rango
                               /* if ($response['cond'] == true)
                                {*/
                                    # responde con todos los datos necesarios
                                    $output['peso']     = $peso;
                                    $output['cubicaje'] = $cubicaje;
                                    $output['numerod']  = $numerod;
                                    $output['tipodoc']  = $datos[0]['tipofac'];
                                /* } else {
                                    $output["mensaje"] = ("El vehículo excede el límite de peso!");
                                }*/
                            } else {
                                $output["mensaje"] = ("Error al evaluar el peso y cubicaje");
                            }
                        } else {
                            $output["mensaje"] = ("El Número de Nota de Entrega: $numerod Ya Fue Agregado en Otro Despacho");
                        }
                    } else {
                        $output["mensaje"] = "El Número de Nota de Entrega: $numerod, Ya fue Agregado";
                    }
                } else {
                    $output["mensaje"] = "El Número de Nota de Entrega $numerod No Existe en Sistema";
                }
            } else {
                $output["mensaje"] = "El Número de Nota de Entrega $numerod Está fuera de rango";
            }
        }

        echo json_encode($output);
        break;

    case "eliminar_documento_para_anadir":
        $datos = array();
        $output = array("cond" => false);

        $numerod = $_POST["documento"];
        $tipodoc = $_POST["tipodoc"];
        $registros_por_despachar = $_POST["registros_por_despachar"];
        $peso_acum = str_replace(",", ".", $_POST["peso_acum_documentos"]);
        $peso_max  = str_replace(",", ".", $_POST["peso_max_vehiculo"]);
        $cubicaje_acum  = str_replace(",", ".", $_POST["cubicaje_acum_documentos"]);
        $cubicaje_max   = str_replace(",", ".", $_POST["cubicaje_max_vehiculo"]);

        # si existe el documento
        # validamos que este en el formato antes de eliminar
        if (DespachosHelpers::validateExistDocumentInString($registros_por_despachar, $numerod, $tipodoc) > 0)
        {
            $deleteDocument = true;
            $data = DespachosHelpers::getDocumentInString(
                $registros_por_despachar,
                $numerod,
                $tipodoc
            );
            # [2] -> peso (tara)
            # [3] -> cubicaje
            $peso = $data['2'];
            $cubicaje = $data['3'];
            if (ArraysHelpers::validate($data)) {
                # valida el peso y obtinene el porcentaje
                $response = DespachosHelpers::validateWeightAndCubicCapacity(
                    array(
                        'peso'      => 1,
                        'peso_acum' => 1,
                        'peso_max'  => 1,
                        'cubicaje_acum' => 1,
                        'cubicaje'      => 1,
                        'cubicaje_max'  => 1,
                    ),
                    $deleteDocument
                );
                $output = $response;

                # verifica si el peso esta dentro del rango
                if (isset($response['cond']))
                {
                    if ($response['cond'] == true)
                    {
                        # responde con todos los datos necesarios
                        $output['peso']     = $peso;
                        $output['cubicaje'] = $cubicaje;
                        $output['numerod']  = $numerod;
                        $output['tipodoc']  = $datos[0]['tipofac'];
                    } else {
                        $output["mensaje"] = ("El vehículo excede el límite de peso!");
                    }
                } else {
                    $formato_a_eliminar = $data[0]."-".$data[1]."-".$data[2]."-".$data[3].";";
                    $output['registros_por_despachar'] = str_replace($formato_a_eliminar, "", $registros_por_despachar);
                }
            }
        } else {
            $output["mensaje"] = "Error intentando eliminar!";
        }

        echo json_encode($output);
        break;

    case "obtener_documentosporcargardespacho":

        $array = explode(";", substr($_POST["registros_por_despachar"], 0, -1));

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        foreach ($array as $row) {
            # separa por cada "-" quedando el formato anterior mencionado
            # [0] -> numerod
            # [1] -> tipofac
            # [2] -> peso (tara)
            # [3] -> cubicaje
            $documento_data = explode("-", $row);

            $numerod  = $documento_data[0];
            $tipofac  = $documento_data[1];
            $peso     = $documento_data[2];
            $cubicaje = $documento_data[3];

            $datos = array();
            switch ($tipofac) {
                case 'A': $datos = $despachos->getFactura($numerod); break;
                case 'C': $datos = $despachos->getNotaDeEntrega($numerod); break;
            }
            $datos = ArraysHelpers::validateWithPos($datos, 0);

            $sub_array = array();

            $tipoDocu  = ($tipofac=='A') ? 'Factura' : 'Nota de Entrega';
            $tipoBadge = ($tipofac=='A') ? 'badge-primary' : 'badge-secondary';

            $sub_array[] = $datos["numerod"] .'<br><span class="right badge '.$tipoBadge.'">'.$tipoDocu.'</span>';
            $sub_array[] = date(FORMAT_DATE, strtotime($datos["fechae"]));
            $sub_array[] = $datos["descrip"];
            $sub_array[] = $datos["direccion"];
            $sub_array[] = $datos["codvend"];
            $sub_array[] = Strings::rdecimal($datos["total"], 2);
            $sub_array[] = Strings::rdecimal($peso, 2);
            $sub_array[] = Strings::rdecimal($cubicaje, 2);
            $sub_array[] = '<div class="col text-center">
                                <button type="button" onClick="eliminar(\''.$datos["numerod"].'\',\''.$tipofac.'\');" name="eliminar" id="eliminar" class="btn btn-danger btn-sm eliminar">Eliminar</button>
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

    case "registrar_despacho":

       $insertDet = $creacionDespacho = false;
        $creacionDetalleDespacho = true;
        $MaximoCorrel = "";
        $correl=0;

        $MaximoCorrel = $despachos->MaxCorrelativo();

        $correl = intval($MaximoCorrel[0]["correlativo"]) + 1;

        $values = array(
            'fechad'   => $_POST["fechad"],
            'chofer'   => $_POST["chofer"],
            'vehiculo' => $_POST["vehiculo"],
            'destino'  => $_POST["destino"],
            'usuario'  => $usuario,
            'correlativo'  => ($correl),
        );

        if(isset($_POST["registros_por_despachar"])) {
            $array = explode(";", substr($_POST["registros_por_despachar"], 0, -1));
                       // $array = explode("-", $_POST["registros_por_despachar"]);
          //  echo "<script>console.log('correl: " . $correl.' '. $array[0].'  '.$array[1] . "' );</script>";

            $creacionDespacho = $despachos->insertarDespacho($values);
           // if ($creacionDespacho != -1) {
                foreach ($array AS $item) {
                    # separa por cada "-" quedando el formato anterior mencionado
                    # [0] -> numerod
                    # [1] -> tipofac
                    # [2] -> peso (tara)
                    # [3] -> cubicaje
                    $data = explode("-", $item);
                    //echo "<script>console.log('correl: " . intval($correl).' '. strval($data[0]). "' );</script>";
                    $insertDet = $despachos->insertarDetalleDespacho(intval($correl), strval($data[0]));
                    if (!$insertDet) {
                        $creacionDetalleDespacho = false;
                    }
                }
           // }
        }

        # datos
        $vehiculo = ArraysHelpers::validateWithPos(Vehiculo::getById($values['vehiculo']), 0);
        $chofer   = ArraysHelpers::validateWithPos(Choferes::getByDni($values['chofer']), 0);

        if ($creacionDespacho && $creacionDetalleDespacho)
        {
            /**  enviar correo: despachos_visual **/
            # preparamos los datos a enviar
           /* $dataEmail = EmailData::DataCreacionDeDespacho(
                array(
                    'usuario' => $_SESSION['login'],
                    'correl_despacho' => $creacionDespacho,
                    'vehiculo' => $vehiculo['placa']." ".$vehiculo['modelo']." ".Strings::rdecimal($vehiculo['capacidad'],0)."Kg",
                    'destino'  => $values['destino'],
                    'chofer'   => $chofer['descripcion'],
                    'fechad'   => $values['fechad'],
                )
            );

            # enviar correo
            $status_send = Email::send_email(
                $dataEmail['title'],
                $dataEmail['body'],
                $dataEmail['recipients'],
            );*/

            $output["mensaje"] = "SE HA CREADO UN NUEVO DESPACHO NRO: " . (str_pad($correl, 8, 0, STR_PAD_LEFT));
            $output["icono"] = "success";
            $output["correl"] = $creacionDespacho;
        } else {
            $output["mensaje"] = "ERROR AL CREAR ESTE DESPACHO";
            $output["icono"] = "error";
            $output["correl"] = 0;
        }

        echo json_encode($output);
        break;

    case "listar_productos_despacho":

        //correlativo
        $correlativo = '';
        $correlativo = $_POST["correlativo"];
        echo "<script>console.log('correlativo: " . $correlativo. "' );</script>";

        //obtenemos los registros de los productos en dichos documentos
        $productosDespacho = Array();
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

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        $total_bultos = 0;
        $total_paq = 0;
        foreach ($productosDespacho as $row) {

            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            //REALIZAMOS PROCESOS DE CALCULO
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
                    $decimales = explode(".", $bultos_total);
                    $bultos_deci = $bultos_total - $decimales[0];
                    $paq = $bultos_deci * $row["cantempaq"];
                    $bultos = $decimales[0] + $bultos;
                }
            }
            $total_bultos += $bultos;
            $total_paq    += $paq;

            //agregamos al sub array
            $sub_array[] = $row["coditem"];
            $sub_array[] = $row["descrip"];
            $sub_array[] = round($bultos);
            $sub_array[] = round($paq);

            //agregamos un registro al array principal
            $data[] = $sub_array;
        }

        $totales = array(
            "total_bultos" => $total_bultos,
            "total_paq"    => $total_paq,
        );

        //al terminar, se almacena en una variable de salida el array.
        $output['contenido_tabla'] = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data);

        //de igual forma, se almacena en una variable de salida el array de totales.
        $output['totales_tabla'] = $totales;

        echo json_encode($output);
        break;

    case "buscar_documentoEnDespachos_modal":
        $datos = array();
        $output = array();

        $numerod = $_POST["documento"];
        $tipodoc = $_POST["tipodoc"];

        # tipodoc 'f' es Factura
        if ($tipodoc == 'f') {
            # valida el documento exista
            $datos = $despachos->getFactura($numerod);
            if (ArraysHelpers::validate($datos)) {
                $datos = $despachos->getDocumentoEnDespachos($numerod, 'A');
            } else {
                $output["mensaje"] = "El Número de Factura $numerod No Existe en Sistema";
            }
        }
        # tipodoc 'n' es Nota de Entrega
        elseif ($tipodoc == 'n') {
            # valida el documento exista
            $datos = $despachos->getNotaDeEntrega($numerod);
            if (ArraysHelpers::validate($datos)) {
                $datos = $despachos->getDocumentoEnDespachos($numerod, 'C');
            } else {
                $output["mensaje"] = "El Número de Nota de Entrega $numerod No Existe en Sistema";
            }
        }

        if (!isset($output["mensaje"])) {
            # verificamos que exista datos de la consulta
            if(ArraysHelpers::validate($datos)) {

                $datos = ArraysHelpers::validateWithPos($datos, 0);

                # creamos un array para almacenar los datos procesados
                $data = Array();

                $datos11 = $despachos->getDetallesnDespacho($datos['correl']);

                foreach ($datos11 as $row11) {
                    $correl = $row11["correl"];
                    $fechae = $row11["fechae"];
                    $nota = $row11["nota"];
                    $cedula_chofer = $row11["cedula_chofer"];
                }

                $datos22 = $despachos->getChoferEnDespacho($cedula_chofer);

                foreach ($datos22 as $row22) {
                    $chofer = $row22["descripcion"];
                }

                $data['numerod'] = $numerod;
                $data['tipofac'] = ($tipodoc == 'f') ? "FACTURA" : "NOTA DE ENTREGA";
                $data['correlativo'] = str_pad($correl, 8, 0, STR_PAD_LEFT);
                $data['fechae'] = date(FORMAT_DATETIME2, strtotime($fechae));
                $data['destino'] = $nota." - ".$chofer;

                //al terminar, se almacena en una variable de salida el array.
                $output['documento_en_despacho'] = $data;

                //verificamos si la consulta tiene registro de pagos
                if (isset($datos['fecha_liqui']) AND isset($datos['monto_cancelado'])) {
                    //creamos un array para almacenar los datos procesados
                    $data1 = array();
                    $data1['fecha_liqui'] = date(FORMAT_DATE, strtotime($datos['fecha_liqui']));
                    $data1['monto_cancelado'] = Strings::rdecimal($datos['monto_cancelado'], 1) . " BsS";

                    //al terminar, se almacena en una variable de salida el array.
                    $output['datos_pago'] = $data1;
                }
            }
        }

        echo json_encode($output);
        break;

    case "listar_mercancia_por_despachar":

        //verificamos si existe al menos 1 deposito selecionado
        $depos = $_POST['depo'] ?? array();

        $fechaf = date('Y-m-d');
        $dato = explode("-", $fechaf); //Hasta
        $aniod = $dato[0]; //año
        $mesd = $dato[1]; //mes
        $diad = "01"; //dia
        $fechai = $aniod . "-01-01";


        $coditem = $cantidad = $tipo = array();
        $t = 0;

        $devolucionesDeFactura = Factura::getInvoiceReturns($fechai, $fechaf, $depos);
        if(count($devolucionesDeFactura) > 0) {
            foreach ($devolucionesDeFactura as $devol) {
                $coditem[] = $devol['coditem'];
                $cantidad[] = $devol['cantidad'];
                $tipo[] = $devol['esunid'];
                $t += 1;
            }
        }

        $datos = $despachos->getMercanciaSinDespachar($fechai, $fechaf, $depos);
        $tbulto = $tpaq = $tbultoinv = $tpaqinv = 0;
        $cant_paq = 0;
        $cant_bul = 0;
        $i=0;
        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        $totales = Array();

        foreach ($datos as $key => $row) {

            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            if($t > 0) {
                for($e = 0; $e < $t; $e++)
                {
                    if($coditem[$e] == $row['CodProd']) {
                        switch ($tipo[$e]) {
                            case '0':
                                $cant_bul = $row['todob'] - $cantidad[$e];
                                break;
                            case '1':
                                $cant_paq = $row['todop'] - $cantidad[$e];
                                break;
                        }
//                        $e = $t + 2;
                        break;
                    }else{
                        $cant_bul = $row['todob'];
                        $cant_paq = $row['todop'];
                    }
                }
            } else {
                $cant_bul = $row['todob'];
                $cant_paq = $row['todop'];
            }
            ////conversión de bultos a paquetes
            $cantemp = $row['CantEmpaq'];
            $invbut  = $row['exis'];
            $invpaq  = $row['exunid'];

            $i++;
            if($cant_paq >= $cantemp){
                $conv = floor($cant_paq / $cantemp);
                $cant_paq -= ($conv * $cantemp);
                $cant_bul += $conv;
            }
            if($invpaq >= $cantemp){
                $conv = floor($invpaq / $cantemp);
                $invpaq -= ($conv * $cantemp);
                $invbut += $conv;
            }
            $tinvbult = $invbut + $cant_bul;
            $tinvpaq = $invpaq + $cant_paq;

            if($tinvpaq >= $cantemp){
                $conv1 = floor($tinvpaq / $cantemp);
                $tinvpaq -= ($conv1 * $cantemp);
                $tinvbult += $conv1;
            }

            //ASIGNAMOS EN EL SUB_ARRAY LOS DATOS PROCESADOS
//            $sub_array[] = $key;
            $sub_array['codprod'] = $row["CodProd"];
            $sub_array['descrip'] = $row["Descrip"];
            $sub_array['cant_bul'] = Strings::rdecimal($cant_bul,0);
            $sub_array['cant_paq'] = Strings::rdecimal($cant_paq,0);
            $sub_array['tinvbult'] = Strings::rdecimal($tinvbult,0);
            $sub_array['tinvpaq'] = Strings::rdecimal($tinvpaq, 0);

            //ACUMULAMOS LOS TOTALES
            $tbulto     += $cant_bul;
            $tpaq       += $cant_paq;
            $tbultoinv  += $tinvbult;
            $tpaqinv    += $tinvpaq;

            $data[] = $sub_array;
        }

        //CREAMOS UN SUB_ARRAY PARA ALMACENAR LOS DATOS ACUMULADOS
        $totales = array();
        $totales['tbulto']     = Strings::rdecimal($tbulto,0);
        $totales['tpaq']       = Strings::rdecimal($tpaq,0);
        $totales['tbultoinv']  = Strings::rdecimal($tbultoinv,0);
        $totales['tpaqinv']    = Strings::rdecimal($tpaqinv,0);
        $totales['facturas_sin_despachar'] = count($devolucionesDeFactura);


        //al terminar, se almacena en una variable de salida el array.
        $output['contenido_tabla'] = $data;

        //de igual forma, se almacena en una variable de salida el array de totales.
        $output['totales_tabla'] = $totales;

        echo json_encode($output);
        break;

    case "listar_depositos":

        $output['lista_depositos'] = Almacen::todos();
        echo json_encode($output);
        break;

    case "listar_vehiculo":

        $output["lista_vehiculos"] = Vehiculo::todos();
        echo json_encode($output);
        break;

   case "listar_documentos":

         $fechaf = date('Y-m-d');
        $dato = explode("-", $fechaf); //Hasta
        $aniod = $dato[0]; //año
        $mesd = $dato[1]; //mes
        $fechai = $aniod . "-" ."01". "-01";

            $output["lista_documentos"] = Choferes::DocumentosporDespachar($fechai,$fechaf);
        echo json_encode($output);
        break;

    case "listar_choferes":

        $output["lista_choferes"] = Choferes::todos();
        echo json_encode($output);
        break;
}
