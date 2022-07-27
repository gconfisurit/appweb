<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO
require_once("kpi_modelo.php");
require_once("../kpimanager/kpimanager_modelo.php");

//INSTANCIAMOS EL MODELO
$kpi = new Kpi();
$kpiManager = new KpiManager();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_marcaskpi":
        $output['lista_marcaskpi'] = array_map(function ($arr) { return $arr['descripcion']; }, KpiMarcas::todos('DESC'));

        echo json_encode($output);
        break;

    case "listar_kpi":
        $fechai     = $_POST['fechai'];
        $fechaf     = $_POST['fechaf'];
        $d_habiles  = $_POST['d_habiles'];
        $d_trans    = $_POST['d_trans'];

        $fechai2 = str_replace('/','-',$fechai); $fechai2 = date('Y-m-d', strtotime($fechai2));
        $fechaf2 = str_replace('/','-',$fechaf); $fechaf2 = date('Y-m-d', strtotime($fechaf2));

        $coordinadores = $kpi->get_coordinadores();
        if (is_array($coordinadores) == true and count($coordinadores) > 0)
        {
            $marcasKpi = array_map(function ($arr) { return $arr['descripcion']; }, KpiMarcas::todos());
            $ttl_marcas = new Kpimarca($marcasKpi);

            # inicializacion variables ttl
            $ttl_clientes           = 0;
            $ttl_clientes_activos   = 0;
            $ttl_clientes_noactivos = 0;
            $ttl_activacionBultos   = array();
            $ttl_porc_activacion    = 0;
            $ttl_obj_documentos_mensual  = 0;
            $ttl_facturas_realizadas     = 0;
            $ttl_notas_realizadas        = 0;
            $ttl_devoluciones_realizadas = 0;
            $ttl_montoendivisa_devoluciones = 0;
            $ttl_efec_alcanzada_fecha       = 0;
            $ttl_objetivo_bulto             = 0;
            $ttl_logro_bulto                = 0;
            $ttl_porc_alcanzado_bulto       = 0;
            $ttl_objetivo_kg                = 0;
            $ttl_logro_kg                   = 0;
            $ttl_porc_alcanzado_kg          = 0;
            $ttl_objetivo_ventas_divisas    = 0;
            $ttl_logro_ventas_divisas       = 0;
            $ttl_porc_ventas_divisas        = 0;
            $ttl_real_dz_dolares            = 0;
            $ttl_logro_ventas_divisas_pepsico        = 0;
            $ttl_porcentaje_ventas_divisas_pepsico   = 0;
            $ttl_logro_ventas_divisas_complementaria = 0;
            $ttl_porcentaje_ventas_divisas_complementaria = 0;
            $ttl_cobranzasRebajadas = 0;

            //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
            $data = Array();
            foreach ($coordinadores as $coord)
            {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();
                $sub_array['coordinador'] = $coord["coordinador"];
                $subttl_marcas = new Kpimarca($marcasKpi);

                # inicializacion variables subttl
                $subttl_clientes           = 0;
                $subttl_clientes_activos   = 0;
                $subttl_clientes_noactivos = 0;
                $subttl_activacionBultos   = array();
                $subttl_porc_activacion    = 0;
                $subttl_obj_documentos_mensual  = 0;
                $subttl_facturas_realizadas     = 0;
                $subttl_notas_realizadas        = 0;
                $subttl_devoluciones_realizadas = 0;
                $subttl_montoendivisa_devoluciones = 0;
                $subttl_efec_alcanzada_fecha       = 0;
                $subttl_objetivo_bulto             = 0;
                $subttl_logro_bulto                = 0;
                $subttl_porc_alcanzado_bulto       = 0;
                $subttl_objetivo_kg                = 0;
                $subttl_logro_kg                   = 0;
                $subttl_porc_alcanzado_kg          = 0;
                $subttl_objetivo_ventas_divisas    = 0;
                $subttl_logro_ventas_divisas       = 0;
                $subttl_porc_ventas_divisas        = 0;
                $subttl_real_dz_dolares            = 0;
                $subttl_logro_ventas_divisas_pepsico        = 0;
                $subttl_porcentaje_ventas_divisas_pepsico   = 0;
                $subttl_logro_ventas_divisas_complementaria = 0;
                $subttl_porcentaje_ventas_divisas_complementaria = 0;
                $subttl_cobranzasRebajadas = 0;

                $vendedores = $kpi->get_rutasPorCoordinador($coord["coordinador"]);
                if (is_array($vendedores) == true and count($vendedores) > 0) {
                    foreach ($vendedores as $vend)
                    {
                        $ruta = $vend["ID3"];
                        $clientes           = count($kpi->get_MaestroClientesPorRuta($ruta));
                        $clientes_activos   = count($kpi->get_ClientesActivosPorRuta($ruta, $fechai2, $fechaf2));
                        $clientes_noactivos = $clientes - $clientes_activos;
                        $activacionBultos   = KpiHelpers::activacionBultosPorMarcasKpi($ruta, $marcasKpi, $fechai2, $fechaf2);
                        $porc_activacion    = ($clientes!=0) ? ($clientes_activos/$clientes) * 100 : 0;
                        $frecuenciadata         = $kpi->get_frecuenciaVisita($ruta);
                        foreach ($frecuenciadata as $row){


                           $frecuencia = $row["Frecuencia"];
                        }

                        $frecuenciaVisita   = KpiHelpers::frecuenciaVisita($frecuencia);
                        $obj_documentos_mensual = KpiHelpers::objetivoFacturasMasNotasMensual($clientes, $d_habiles, $frecuencia);
                        $facturas_realizadas    = count($kpi->get_ventasFactura($ruta, $fechai2, $fechaf2));
                        $notas_realizadas = count($kpi->get_ventasNotas($ruta, $fechai2, $fechaf2));
                        $devolucionesFact = $kpi->get_devolucionesFactura($ruta, $fechai2, $fechaf2);
                        $devolucionesNota = $kpi->get_devolucionesNotas($ruta, $fechai2, $fechaf2);
                        $devoluciones_realizadas = count($devolucionesFact) + count($devolucionesNota);
                        $montoendivisa_devoluciones_fact = floatval($kpi->get_montoDivisasDevolucionesFactura($ruta, $fechai2, $fechaf2)[0]["MontoD"]);
                        $montoendivisa_devoluciones_nt   = floatval($kpi->get_montoDivisasDevolucionesNotas($ruta, $fechai2, $fechaf2)[0]["MontoD"]);
                        $montoendivisa_devoluciones = $montoendivisa_devoluciones_fact + $montoendivisa_devoluciones_nt;
                        $efec_alcanzada_fecha = KpiHelpers::efectividadAlcanzadaAlaFecha($d_trans, $d_habiles, $obj_documentos_mensual, $facturas_realizadas, $notas_realizadas);
                        $objetivo_bultoda       = $kpi->obtenerObjetivo($ruta);
                        foreach ($objetivo_bultoda as $row2){


                           $objetivo_bulto = $row2["ObjVentasBu"];
                        }
                        $logro_bulto          = KpiHelpers::logroPorTipo($ruta, $fechai2, $fechaf2, 'BUL');
                        $porc_alcanzado_bulto = (($objetivo_bulto!=0) ? ($logro_bulto/$objetivo_bulto)*100 : 0) ?? 0;
                        $objetivo_kg          = KpiHelpers::obtenerObjetivo($frecuencia, 'ObjVentasKG');
                        $logro_kg             = KpiHelpers::logroPorTipo($ruta, $fechai2, $fechaf2, 'KG');
                        $porc_alcanzado_kg    = (($objetivo_kg!=0) ? ($logro_kg/$objetivo_kg)*100 : 0) ?? 0;
                        $objetivo_ventas_divisas = KpiHelpers::obtenerObjetivo($frecuencia, 'ObjVentasBs'); # realmente objetivo $$
                        $ventas_divisas_fact  = $kpi->get_ventasDivisasFactura($ruta, $fechai2, $fechaf2)[0]["MontoD"];
                        $ventas_divisas_nt    = $kpi->get_ventasDivisasNotas($ruta, $fechai2, $fechaf2)[0]["MontoD"];
                        $logro_ventas_divisas = floatval($ventas_divisas_fact) + floatval($ventas_divisas_nt);
                        $porc_ventas_divisas  = (($objetivo_ventas_divisas!=0) ? ($logro_ventas_divisas/$objetivo_ventas_divisas)*100 : 0) ?? 0;
                        $real_dz_dolares      = (($facturas_realizadas+$notas_realizadas) > 0) ? $logro_ventas_divisas/($facturas_realizadas+$notas_realizadas) : 0;
                        $ventas_divisas_pepsico_fact         = $kpi->get_ventasDivisasPepsicoFactura($ruta, $fechai2, $fechaf2)[0]["MontoD"];
                        $ventas_divisas_pepsico_nt           = $kpi->get_ventasDivisasPepsicoNotas($ruta, $fechai2, $fechaf2)[0]["MontoD"];
                        $logro_ventas_divisas_pepsico        = floatval($ventas_divisas_pepsico_fact) + floatval($ventas_divisas_pepsico_nt);
                        $porcentaje_ventas_divisas_pepsico   = ($logro_ventas_divisas > 0) ? ($logro_ventas_divisas_pepsico / $logro_ventas_divisas) * 100 : 0;
                        $ventas_divisas_complementaria_fact  = $kpi->get_ventasDivisasComplementariaFactura($ruta, $fechai2, $fechaf2)[0]["MontoD"];
                        $ventas_divisas_complementaria_nt    = $kpi->get_ventasDivisasComplementariaNotas($ruta, $fechai2, $fechaf2)[0]["MontoD"];
                        $logro_ventas_divisas_complementaria = floatval($ventas_divisas_complementaria_fact) + floatval($ventas_divisas_complementaria_nt);
                        $porcentaje_ventas_divisas_complementaria = ($logro_ventas_divisas > 0) ? ($logro_ventas_divisas_complementaria / $logro_ventas_divisas) * 100 : 0;
                        $cobranzasRebajadas = KpiHelpers::totalCobranzasRebajadas($ruta, $fechai2, $fechaf2);

                        #llenado de los subtotals
                        $subttl_marcas->set_acumKpiMarcas($activacionBultos);
                        $subttl_clientes                                 += $clientes;
                        $subttl_clientes_activos                         += $clientes_activos;
                        $subttl_activacionBultos                         = $subttl_marcas->get_totalKpiMarcas();
                        $subttl_clientes_noactivos                       += $clientes_noactivos;
                        $subttl_porc_activacion                          = ($subttl_clientes!=0) ? ($subttl_clientes_activos/$subttl_clientes) * 100 : 0;
                        $subttl_obj_documentos_mensual                   += $obj_documentos_mensual;
                        $subttl_facturas_realizadas                      += $facturas_realizadas;
                        $subttl_notas_realizadas                         += $notas_realizadas;
                        $subttl_devoluciones_realizadas                  += $devoluciones_realizadas;
                        $subttl_montoendivisa_devoluciones               += $montoendivisa_devoluciones;
                        $subttl_efec_alcanzada_fecha                     = KpiHelpers::efectividadAlcanzadaAlaFecha($d_trans, $d_habiles, $subttl_obj_documentos_mensual, $subttl_facturas_realizadas, $subttl_notas_realizadas);
                        $subttl_objetivo_bulto                           += $objetivo_bulto;
                        $subttl_logro_bulto                              += $logro_bulto;
                        $subttl_porc_alcanzado_bulto                     = (($subttl_objetivo_bulto!=0) ? ($subttl_logro_bulto/$subttl_objetivo_bulto)*100 : 0) ?? 0;
                        $subttl_objetivo_kg                              += $objetivo_kg;
                        $subttl_logro_kg                                 += $logro_kg;
                        $subttl_porc_alcanzado_kg                        = (($subttl_objetivo_kg!=0) ? ($subttl_logro_kg/$subttl_objetivo_kg)*100 : 0) ?? 0;
                        $subttl_objetivo_ventas_divisas                  += $objetivo_ventas_divisas;
                        $subttl_logro_ventas_divisas                     += $logro_ventas_divisas;
                        $subttl_porc_ventas_divisas                      = (($subttl_objetivo_ventas_divisas!=0) ? ($subttl_logro_ventas_divisas/$subttl_objetivo_ventas_divisas)*100 : 0) ?? 0;
                        $subttl_real_dz_dolares                          += $real_dz_dolares;
                        $subttl_logro_ventas_divisas_pepsico             += $logro_ventas_divisas_pepsico;
                        $subttl_porcentaje_ventas_divisas_pepsico        += $porcentaje_ventas_divisas_pepsico;
                        $subttl_logro_ventas_divisas_complementaria      += $logro_ventas_divisas_complementaria;
                        $subttl_porcentaje_ventas_divisas_complementaria += $porcentaje_ventas_divisas_complementaria;
                        $subttl_cobranzasRebajadas                       += $cobranzasRebajadas;


                        $sub_array1 = array(
                            'ruta'            => $ruta,
                            'maestro'         => $clientes,
                            'activos'         => $clientes_activos,
                            'marcas'          => $activacionBultos,
                            'porc_activacion' => Strings::rdecimal($porc_activacion),
                            'por_activar'     => $clientes_noactivos,
                            'visita'          => $frecuenciaVisita,
                            'obj_documentos_mensual'        => Strings::rdecimal($obj_documentos_mensual, 2),
                            'facturas_realizadas'           => $facturas_realizadas,
                            'notas_realizadas'              => $notas_realizadas,
                            'devoluciones_realizadas'       => $devoluciones_realizadas,
                            'montoendivisa_devoluciones'    => Strings::rdecimal($montoendivisa_devoluciones,2),
                            'efec_alcanzada_fecha'          => Strings::rdecimal($efec_alcanzada_fecha, 2),
                            'objetivo_bulto'                => Strings::rdecimal($objetivo_bulto, 2),
                            'logro_bulto'                   => Strings::rdecimal($logro_bulto, 2),
                            'porc_alcanzado_bulto'          => Strings::rdecimal($porc_alcanzado_bulto, 2),
                            'objetivo_kg'                   => Strings::rdecimal($objetivo_kg, 2),
                            'logro_kg'                      => Strings::rdecimal($logro_kg, 2),
                            'porc_alcanzado_kg'             => Strings::rdecimal($porc_alcanzado_kg, 2),
                            'drop_size_divisas'             => Strings::rdecimal($real_dz_dolares, 2),
                            'objetivo_ventas_divisas'       => Strings::rdecimal($objetivo_ventas_divisas, 2),
                            'logro_ventas_divisas'          => Strings::rdecimal($logro_ventas_divisas, 2),
                            'porc_alcanzado_ventas_divisas' => Strings::rdecimal($porc_ventas_divisas, 2),
                            'logro_ventas_divisas_pepsico'                 => Strings::rdecimal($logro_ventas_divisas_pepsico, 2),
                            'porc_alcanzado_ventas_divisas_pepsico'        => Strings::rdecimal($porcentaje_ventas_divisas_pepsico, 2),
                            'logro_ventas_divisas_complementaria'          => Strings::rdecimal($logro_ventas_divisas_complementaria, 2),
                            'porc_alcanzado_ventas_divisas_complementaria' => Strings::rdecimal($porcentaje_ventas_divisas_complementaria, 2),
                            'cobranzas_rebajadas'                          => Strings::rdecimal($cobranzasRebajadas, 2),
                        );

                        $sub_array['data'][] = $sub_array1;
                    }

                    #llenado del total general
                    $ttl_marcas->set_acumKpiMarcas($subttl_marcas->get_totalKpiMarcas());
                    $ttl_clientes                                 += $subttl_clientes;
                    $ttl_clientes_activos                         += $subttl_clientes_activos;
                    $ttl_activacionBultos                         = $ttl_marcas->get_totalKpiMarcas();
                    $ttl_clientes_noactivos                       += $subttl_clientes_noactivos;
                    $ttl_porc_activacion                          = ($ttl_clientes!=0) ? ($ttl_clientes_activos/$ttl_clientes) * 100 : 0;;
                    $ttl_obj_documentos_mensual                   += $subttl_obj_documentos_mensual;
                    $ttl_facturas_realizadas                      += $subttl_facturas_realizadas;
                    $ttl_notas_realizadas                         += $subttl_notas_realizadas;
                    $ttl_devoluciones_realizadas                  += $subttl_devoluciones_realizadas;
                    $ttl_montoendivisa_devoluciones               += $subttl_montoendivisa_devoluciones;
                    $ttl_efec_alcanzada_fecha                     = KpiHelpers::efectividadAlcanzadaAlaFecha($d_trans, $d_habiles, $ttl_obj_documentos_mensual, $ttl_facturas_realizadas, $ttl_notas_realizadas);
                    $ttl_objetivo_bulto                           += $subttl_objetivo_bulto;
                    $ttl_logro_bulto                              += $subttl_logro_bulto;
                    $ttl_porc_alcanzado_bulto                     = (($ttl_objetivo_bulto!=0) ? ($ttl_logro_bulto/$ttl_objetivo_bulto)*100 : 0) ?? 0;
                    $ttl_objetivo_kg                              += $subttl_objetivo_kg;
                    $ttl_logro_kg                                 += $subttl_logro_kg;
                    $ttl_porc_alcanzado_kg                        = (($ttl_objetivo_kg!=0) ? ($ttl_logro_kg/$ttl_objetivo_kg)*100 : 0) ?? 0;
                    $ttl_objetivo_ventas_divisas                  += $subttl_objetivo_ventas_divisas;
                    $ttl_logro_ventas_divisas                     += $subttl_logro_ventas_divisas;
                    $ttl_porc_ventas_divisas                      = (($ttl_objetivo_ventas_divisas!=0) ? ($ttl_logro_ventas_divisas/$ttl_objetivo_ventas_divisas)*100 : 0) ?? 0;
                    $ttl_real_dz_dolares                          += $subttl_real_dz_dolares;
                    $ttl_logro_ventas_divisas_pepsico             += $subttl_logro_ventas_divisas_pepsico;
                    $ttl_porcentaje_ventas_divisas_pepsico        += $subttl_porcentaje_ventas_divisas_pepsico/count($vendedores);
                    $ttl_logro_ventas_divisas_complementaria      += $subttl_logro_ventas_divisas_complementaria;
                    $ttl_porcentaje_ventas_divisas_complementaria += $subttl_porcentaje_ventas_divisas_complementaria/count($vendedores);
                    $ttl_cobranzasRebajadas                       += $subttl_cobranzasRebajadas;

                    $subtotal = array(
                        'ruta'            => "SUBTOTAL",
                        'maestro'         => $subttl_clientes,
                        'activos'         => $subttl_clientes_activos,
                        'marcas'          => $subttl_activacionBultos,
                        'porc_activacion' => Strings::rdecimal($subttl_porc_activacion),
                        'por_activar'     => $subttl_clientes_noactivos,
                        'visita'          => "",
                        'obj_documentos_mensual'        => Strings::rdecimal($subttl_obj_documentos_mensual, 2),
                        'facturas_realizadas'           => $subttl_facturas_realizadas,
                        'notas_realizadas'              => $subttl_notas_realizadas,
                        'devoluciones_realizadas'       => $subttl_devoluciones_realizadas,
                        'montoendivisa_devoluciones'    => Strings::rdecimal($subttl_montoendivisa_devoluciones,2),
                        'efec_alcanzada_fecha'          => Strings::rdecimal($subttl_efec_alcanzada_fecha, 2),
                        'objetivo_bulto'                => Strings::rdecimal($subttl_objetivo_bulto, 2),
                        'logro_bulto'                   => Strings::rdecimal($subttl_logro_bulto, 2),
                        'porc_alcanzado_bulto'          => Strings::rdecimal($subttl_porc_alcanzado_bulto, 2),
                        'objetivo_kg'                   => Strings::rdecimal($subttl_objetivo_kg, 2),
                        'logro_kg'                      => Strings::rdecimal($subttl_logro_kg, 2),
                        'porc_alcanzado_kg'             => Strings::rdecimal($subttl_porc_alcanzado_kg, 2),
                        'drop_size_divisas'             => Strings::rdecimal($subttl_real_dz_dolares, 2),
                        'objetivo_ventas_divisas'       => Strings::rdecimal($subttl_objetivo_ventas_divisas, 2),
                        'logro_ventas_divisas'          => Strings::rdecimal($subttl_logro_ventas_divisas, 2),
                        'porc_alcanzado_ventas_divisas' => Strings::rdecimal($subttl_porc_alcanzado_kg, 2),
                        'logro_ventas_divisas_pepsico'                 => Strings::rdecimal($subttl_logro_ventas_divisas_pepsico, 2),
                        'porc_alcanzado_ventas_divisas_pepsico'        => Strings::rdecimal($subttl_porcentaje_ventas_divisas_pepsico/count($vendedores), 2),
                        'logro_ventas_divisas_complementaria'          => Strings::rdecimal($subttl_logro_ventas_divisas_complementaria, 2),
                        'porc_alcanzado_ventas_divisas_complementaria' => Strings::rdecimal($subttl_porcentaje_ventas_divisas_complementaria/count($vendedores), 2),
                        'cobranzas_rebajadas'                          => Strings::rdecimal($subttl_cobranzasRebajadas, 2),
                    );

                    $sub_array['subtotal'] = $subtotal;
                }
                $data[] = $sub_array;
            }

            $total_general = array(
                'ruta'            => "TOTAL GENERAL",
                'maestro'         => $ttl_clientes,
                'activos'         => $ttl_clientes_activos,
                'marcas'          => $ttl_activacionBultos,
                'porc_activacion' => Strings::rdecimal($ttl_porc_activacion),
                'por_activar'     => $ttl_clientes_noactivos,
                'visita'          => "",
                'obj_documentos_mensual'        => Strings::rdecimal($ttl_obj_documentos_mensual, 2),
                'facturas_realizadas'           => $ttl_facturas_realizadas,
                'notas_realizadas'              => $ttl_notas_realizadas,
                'devoluciones_realizadas'       => $ttl_devoluciones_realizadas,
                'montoendivisa_devoluciones'    => Strings::rdecimal($ttl_montoendivisa_devoluciones,2),
                'efec_alcanzada_fecha'          => Strings::rdecimal($ttl_efec_alcanzada_fecha, 2),
                'objetivo_bulto'                => Strings::rdecimal($ttl_objetivo_bulto, 2),
                'logro_bulto'                   => Strings::rdecimal($ttl_logro_bulto, 2),
                'porc_alcanzado_bulto'          => Strings::rdecimal($ttl_porc_alcanzado_bulto, 2),
                'objetivo_kg'                   => Strings::rdecimal($ttl_objetivo_kg, 2),
                'logro_kg'                      => Strings::rdecimal($ttl_logro_kg, 2),
                'porc_alcanzado_kg'             => Strings::rdecimal($ttl_porc_alcanzado_kg, 2),
                'drop_size_divisas'             => Strings::rdecimal($ttl_real_dz_dolares, 2),
                'objetivo_ventas_divisas'       => Strings::rdecimal($ttl_objetivo_ventas_divisas, 2),
                'logro_ventas_divisas'          => Strings::rdecimal($ttl_logro_ventas_divisas, 2),
                'porc_alcanzado_ventas_divisas' => Strings::rdecimal($ttl_porc_alcanzado_kg, 2),
                'logro_ventas_divisas_pepsico'                 => Strings::rdecimal($ttl_logro_ventas_divisas_pepsico, 2),
                'porc_alcanzado_ventas_divisas_pepsico'        => Strings::rdecimal($ttl_porcentaje_ventas_divisas_pepsico/count($coordinadores), 2),
                'logro_ventas_divisas_complementaria'          => Strings::rdecimal($ttl_logro_ventas_divisas_complementaria, 2),
                'porc_alcanzado_ventas_divisas_complementaria' => Strings::rdecimal($ttl_porcentaje_ventas_divisas_complementaria/count($coordinadores), 2),
                'cobranzas_rebajadas'                          => Strings::rdecimal($ttl_cobranzasRebajadas, 2),
            );
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "tabla"         => $data,
            "total_general" => $total_general,
        );

        echo json_encode($output);
        break;

    case 'mostrar_detalle_edv':
        $edv = $_POST['edv'];
        $datos = $kpiManager->get_datos_edv($edv)[0];

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        if (is_array($datos) == true and count($datos) > 0) {
            $obj_kpi = $kpiManager->get_objetivos_kpi();

            $data[] = array("Nombre y Apellido", $datos["Descrip"]);
            $data[] = array("Cédula Identidad",  $datos["cedula"]);
            $data[] = array("Teléfono",          $datos["Telef"]);
            $data[] = array("Ubicación",         $datos["ubicacion"]);
            $data[] = array("Objetivo Kpi",      $obj_kpi[intval($datos["Requerido_Bult_Und"])]['descripcion']);
            $data[] = array("Clase",             $datos["clase"]);
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "edv" => $datos["CodVend"],
            "detalle_edv" => $data
        );

        echo json_encode($output);
        break;

    case 'listar_maestro_clientes':
        $edv = $_POST['edv'];
        $datos = $kpi->get_MaestroClientesPorRuta($edv);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        foreach ($datos as $row){

            $sub_array = array();

            $sub_array[] = $row["descrip"];
            $sub_array[] = $row["codclie"];
            $sub_array[] = $row["direc"];
            $sub_array[] = strtoupper($row["dia_visita"]);

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

    case 'listar_clientes_activados':
        $edv    = $_POST['edv'];
        $fechai = $_POST['fechai'];
        $fechaf = $_POST['fechaf'];

        $fechai2 = str_replace('/','-',$fechai); $fechai2 = date('Y-m-d', strtotime($fechai2));
        $fechaf2 = str_replace('/','-',$fechaf); $fechaf2 = date('Y-m-d', strtotime($fechaf2));

        $datos = $kpi->get_ClientesActivosPorRuta($edv, $fechai2, $fechaf2);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        foreach ($datos as $row){

            $sub_array = array();

            $sub_array[] = $row["descrip"];
            $sub_array[] = $row["codclie"];
            $sub_array[] = $row["direc"];
            $sub_array[] = strtoupper($row["dia_visita"]);

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

    case 'listar_clientes_pendientes':
        $edv    = $_POST['edv'];
        $fechai = $_POST['fechai'];
        $fechaf = $_POST['fechaf'];

        $fechai2 = str_replace('/','-',$fechai); $fechai2 = date('Y-m-d', strtotime($fechai2));
        $fechaf2 = str_replace('/','-',$fechaf); $fechaf2 = date('Y-m-d', strtotime($fechaf2));

        $datos = $kpi->get_ClientesNoActivosPorRuta($edv, $fechai2, $fechaf2);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        foreach ($datos as $row){

            $sub_array = array();

            $sub_array[] = $row["descrip"];
            $sub_array[] = $row["codclie"];
            $sub_array[] = $row["direc"];
            $sub_array[] = strtoupper($row["dia_visita"]);

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

    case 'listar_activacion_marcas':
        $edv    = $_POST['edv'];
        $marca  = $_POST['marca'];
        $fechai = $_POST['fechai'];
        $fechaf = $_POST['fechaf'];

        $fechai2 = str_replace('/','-',$fechai); $fechai2 = date('Y-m-d', strtotime($fechai2));
        $fechaf2 = str_replace('/','-',$fechaf); $fechaf2 = date('Y-m-d', strtotime($fechaf2));

        $datos = KpiMarcas::bultosActivadosPorMarca($edv, $marca, $fechai2, $fechaf2, true);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        foreach ($datos as $row){

            $sub_array = array();

            $tipoDocu  = ($row["tipofac"]=='A' ? 'Factura' : 'Nota de Entrega');
            $tipoBadge = ($row["tipofac"]=='A' ? 'badge-primary' : 'badge-secondary');

            $sub_array[] = $row["cliente"];
            $sub_array[] = $row["producto"];
            $sub_array[] = intval($row["bult"]);
            $sub_array[] = intval($row["paq"]);
            $sub_array[] = $row["numerod"] .'<br><span class="right badge '.$tipoBadge.'">'.$tipoDocu.'</span>';

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

    case 'listar_facturas_realizadas':
        $edv = $_POST['edv'];
        $fechai = $_POST['fechai'];
        $fechaf = $_POST['fechaf'];

        $fechai2 = str_replace('/','-',$fechai); $fechai2 = date('Y-m-d', strtotime($fechai2));
        $fechaf2 = str_replace('/','-',$fechaf); $fechaf2 = date('Y-m-d', strtotime($fechaf2));

        $datos = $kpi->get_ventasFactura($edv, $fechai2, $fechaf2);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        foreach ($datos as $row){

            $sub_array = array();

            $sub_array[] = $row["numerod"];
            $sub_array[] = $row["descrip"];
            $sub_array[] = date('d-m-Y', strtotime($row["fechae"]));
            $sub_array[] = Strings::rdecimal($row["montod"], 2);

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

    case 'listar_notas_realizadas':
        $edv = $_POST['edv'];
        $fechai = $_POST['fechai'];
        $fechaf = $_POST['fechaf'];

        $fechai2 = str_replace('/','-',$fechai); $fechai2 = date('Y-m-d', strtotime($fechai2));
        $fechaf2 = str_replace('/','-',$fechaf); $fechaf2 = date('Y-m-d', strtotime($fechaf2));

        $datos = $kpi->get_ventasNotas($edv, $fechai2, $fechaf2);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        foreach ($datos as $row){

            $sub_array = array();

            $sub_array[] = $row["numerod"];
            $sub_array[] = $row["descrip"];
            $sub_array[] = date('d-m-Y', strtotime($row["fechae"]));
            $sub_array[] = Strings::rdecimal($row["montod"], 2);

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

    case 'listar_devoluciones_realizadas':
        $edv = $_POST['edv'];
        $fechai = $_POST['fechai'];
        $fechaf = $_POST['fechaf'];

        $fechai2 = str_replace('/','-',$fechai); $fechai2 = date('Y-m-d', strtotime($fechai2));
        $fechaf2 = str_replace('/','-',$fechaf); $fechaf2 = date('Y-m-d', strtotime($fechaf2));

        $datos = array();

        $devolucionesFact = $kpi->get_devolucionesFactura($edv, $fechai2, $fechaf2);
        foreach ($devolucionesFact as $devol) array_push($datos, $devol);

        $devolucionesNota = $kpi->get_devolucionesNotas($edv, $fechai2, $fechaf2);
        foreach ($devolucionesNota as $devol) array_push($datos, $devol);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        foreach ($datos as $row){

            $sub_array = array();

            $tipoDocu  = ($row["tipofac"]=='B' ? 'Devolución Factura' : 'Devolución Nota de Entrega');
            $tipoBadge = ($row["tipofac"]=='B' ? 'badge-primary' : 'badge-secondary');

            $sub_array[] = $row["numerod"] .'<br><span class="right badge '.$tipoBadge.'">'.$tipoDocu.'</span>';
            $sub_array[] = $row["descrip"];
            $sub_array[] = date('d-m-Y', strtotime($row["fechae"]));
            $sub_array[] = Strings::rdecimal($row["montod"], 2);

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

    case 'listar_cobranzas_rebajadas':
        $edv = $_POST['edv'];
        $fechai = $_POST['fechai'];
        $fechaf = $_POST['fechaf'];

        $fechai2 = str_replace('/','-',$fechai); $fechai2 = date('Y-m-d', strtotime($fechai2));
        $fechaf2 = str_replace('/','-',$fechaf); $fechaf2 = date('Y-m-d', strtotime($fechaf2));

        $datos = Cobranzas::getCobranzasRebajadas($edv, $fechai2, $fechaf2);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        foreach ($datos as $row){

            $sub_array = array();

            $sub_array[] = $row["numerod"];
            $sub_array[] = $row["Descrip"];
            $sub_array[] = date('d-m-Y', strtotime($row["fechae"]));
            $sub_array[] = Strings::rdecimal($row["MONTO"], 2);

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
}