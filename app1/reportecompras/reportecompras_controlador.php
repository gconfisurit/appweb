<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("reportecompras_modelo.php");

//INSTANCIAMOS EL MODELO
$reporte = new ReporteCompras();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar":
        $fechai = $_POST['fechai'];
        $fechaf = $_POST['fechaf'];
        $marca = $_POST['marca'];

        $separa = explode("-",$fechai);
        $dia = $separa[2];
        $mes = $separa[1];
        $anio = $separa[0];

        $fechaiA = date(FORMAT_DATE_TO_EVALUATE, mktime(0,0,0,($mes)-1,1, $anio));
        $fechafA = date(FORMAT_DATE_TO_EVALUATE, mktime(0,0,0,$mes,1, $anio)-1);

        $codidos_producto = $reporte->get_codprod_por_marca(ALMACEN_PRINCIPAL, $marca);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = array();
        foreach ($codidos_producto as $key => $coditem) {

            #Obtencion de datos
            $producto    = $reporte->get_datos_producto($coditem["codprod"]);
            $costos      = $reporte->get_costos($coditem["codprod"]);
            $ult_compras = $reporte->get_ultimas_compras($coditem["codprod"]);
            $ventas      = $reporte->get_ventas_mes_anterior($coditem["codprod"], $fechaiA, $fechafA);
            $bultosExis  = $reporte->get_bultos_existentes(ALMACEN_PRINCIPAL, $coditem["codprod"]);
            $no_vendidos = $reporte->get_productos_no_vendidos($coditem["codprod"], $fechai, $fechaf);

            #Calculos
            $rentabilidad = ReporteComprasHelpers::rentabilidad($producto[0]["precio1"], $producto[0]["costoactual"]);
            $fechapenultimacompra  = (count($ult_compras) > 1) ? date(FORMAT_DATE, strtotime($ult_compras[1]["fechae"])) : '-----';
            $bultospenultimacompra = (count($ult_compras) > 1) ? Strings::rdecimal($ult_compras[1]["cantBult"], 0) : 0;
            $fechaultimacompra   = (count($ult_compras) > 0) ? date(FORMAT_DATE,strtotime($ult_compras[0]["fechae"])) : '-----';
            $bultosultimacompra  = (count($ult_compras) > 0) ? Strings::rdecimal($ult_compras[0]["cantBult"], 0) : 0;
            $ventas_mes_anterior = ReporteComprasHelpers::ventasMesAnterior($ventas, $mes, $anio);
            $totalventasmesanterior = $ventas_mes_anterior["semana1"] + $ventas_mes_anterior["semana2"] + $ventas_mes_anterior["semana3"] + $ventas_mes_anterior["semana4"];
            $diasinventario = ($totalventasmesanterior > 0) ? ($bultosExis[0]["bultosexis"]/$totalventasmesanterior) : 0;
            $sugerido = ($totalventasmesanterior*1.2) - $bultosExis[0]["bultosexis"];
            $sugerido = ($sugerido > 0) ? $sugerido : 0;

            #Creamos un array para almacenar los datos procesados
            $sub_array = array();
            $sub_array['num'] = $key+1;
            $sub_array['codproducto'] = $producto[0]["codprod"];
            $sub_array['descrip']     = $producto[0]["descrip"];
            $sub_array['displaybultos'] = Strings::rdecimal($producto[0]["displaybultos"], 0);
            $sub_array['costodisplay']  = Strings::rdecimal((count($costos) > 0) ? (floatval($costos[0]["costodisplay"])) : 0, 2);
            $sub_array['costobultos']   = Strings::rdecimal((count($costos) > 0) ? (floatval($costos[0]["costobultos"])) : 0, 2);
            $sub_array['rentabilidad']  = Strings::rdecimal($rentabilidad, 2);
            $sub_array['fechapenultimacompra']  = $fechapenultimacompra;
            $sub_array['bultospenultimacompra'] = $bultospenultimacompra;
            $sub_array['fechaultimacompra']     = $fechaultimacompra;
            $sub_array['bultosultimacompra']    = $bultosultimacompra;
            $sub_array['semana1'] = Strings::rdecimal($ventas_mes_anterior["semana1"], 2);
            $sub_array['semana2'] = Strings::rdecimal($ventas_mes_anterior["semana2"], 2);
            $sub_array['semana3'] = Strings::rdecimal($ventas_mes_anterior["semana3"], 2);
            $sub_array['semana4'] = Strings::rdecimal($ventas_mes_anterior["semana4"], 2);
            $sub_array['totalventasmesanterior'] = Strings::rdecimal($totalventasmesanterior, 2);
            $sub_array['bultosexistentes']   = Strings::rdecimal(floatval($bultosExis[0]["bultosexis"]), 2);
            $sub_array['productonovendidos'] = Strings::rdecimal(floatval($no_vendidos[0]["cantidadBult"]), 2);
            $sub_array['diasdeinventario']   = Strings::rdecimal($diasinventario, 2);
            $sub_array['sugerido'] = Strings::rdecimal($sugerido, 2);
            $sub_array['pedido'] = '<input type="text" name="n[]" style="text-align: right; width: 90%;">
                                    <input type="hidden" name="v[]" value="'. $producto[0]["codprod"] .'">';

            //AGREGAMOS AL ARRAY DE CONTENIDO DE LA TABLA
            $data[] = $sub_array;
        }
        //al terminar, se almacena en una variable de salida el array.
        $output['contenido_tabla'] = $data;

        //de igual forma, se almacena en una variable de salida el total de registros.
        $output['cantidad_registros'] = count($codidos_producto);

        echo json_encode($output);
        break;

    case "listar_marcas":

        $output["lista_marcas"] = Marcas::todos();

        echo json_encode($output);
        break;

}