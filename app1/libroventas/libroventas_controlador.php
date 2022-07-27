<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("libroventas_modelo.php");

//INSTANCIAMOS EL MODELO
$libroventa = new LibroVenta();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_libroventas":

        $fechai = Dates::normalize_date($_POST['fechai']).' 00:00:00';
        $fechaf = Dates::normalize_date($_POST['fechaf']).' 23:59:59';

        $datos = $libroventa->getLibroPorFecha($fechai, $fechaf);
        $retenciones_otros_periodos = $libroventa->getRetencionesOtrosPeriodos($fechai, $fechaf);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = $totales_libro = $resumen = $otros_periodos = $totales_otros_periodos = Array();

        $tvii = $ve = $magbi16c = $mag16c = $ivare = $ivape = 0;
        $ivare2 = $ivape2 = 0;

        if (is_array($datos)==true and count($datos)>0)
        {
            foreach ($datos as $key => $row)
            {
                $retencion_dato = $libroventa->getRetencionItem($fechai, $fechaf, $row['numerodoc']);

                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $base_imponible = $row["totalventas"] - $row['mtoexento'];
                $totalventasconiva = $base_imponible + $row['mtoexento'] + $row['montoiva_contribuyeiva'];

                $tvii += $totalventasconiva;
                $ve += $row['mtoexento'];
                $magbi16c += $base_imponible;
                $mag16c += $row['montoiva_contribuyeiva'];
                $ivare += count($retencion_dato)>0 ? Numbers::avoidNull($retencion_dato[0]['retencioniva']) : 0;

                $sub_array['num']  = $key+1;
                $sub_array['fechaemision']  = date(FORMAT_DATE, strtotime($row["fechaemision"]));
                $sub_array['rifcliente']    = $row["rifcliente"];
                $sub_array['nombre']        = utf8_encode($row["nombre"]);
                $sub_array['tipodoc']       = $row["tipodoc"];
                $sub_array['numerodoc']     = $row["numerodoc"];
                $sub_array['nroctrol']      = Strings::avoidNull($row["nroctrol"]);
                $sub_array['tiporeg']       = $row["tiporeg"];
                $sub_array['factafectada']  = Strings::avoidNull($row["factafectada"]);
                $sub_array['nroretencion']  = count($retencion_dato)>0 ? Strings::avoidNull($retencion_dato[0]["nroretencion"]) : '';
                $sub_array['totalventasconiva'] = Strings::rdecimal($totalventasconiva, 2);
                $sub_array['mtoexento']      = Strings::rdecimal($row["mtoexento"], 2);
                $sub_array['base_imponible'] = Strings::rdecimal($base_imponible, 2);
                $sub_array['alicuota_contribuyeiva'] = Strings::rdecimal($row["alicuota_contribuyeiva"], 0);
                $sub_array['montoiva_contribuyeiva'] = Strings::rdecimal($row["montoiva_contribuyeiva"], 2);
                $sub_array['retencioniva']  = count($retencion_dato)>0 ? Strings::avoidNull($retencion_dato[0]["retencioniva"]) : '';

                $data[] = $sub_array;
            }
        }

        $totales_libro = array(
            "tvii"     => Strings::rdecimal($tvii, 2),
            "ve"       => Strings::rdecimal($ve, 2),
            "magbi16c" => Strings::rdecimal($magbi16c, 2),
            "mag16c"   => Strings::rdecimal($mag16c, 2),
            "ivare"    => Strings::rdecimal($ivare, 2)
        );

        if (is_array($retenciones_otros_periodos)==true and count($retenciones_otros_periodos)>0)
        {
            foreach ($retenciones_otros_periodos as $key => $row)
            {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $ivare2 += Numbers::avoidNull($row['retencioniva']);

                $sub_array['num']  = $key+1;
                $sub_array['fechaemision']  = date(FORMAT_DATE, strtotime($row["fechaemision"]));
                $sub_array['rifcliente']    = $row["rifcliente"];
                $sub_array['nombre']        = utf8_encode($row["nombre"]);
                $sub_array['tipodoc']       = $row["tipodoc"];
                $sub_array['numerodoc']     = $row["numerodoc"];
                $sub_array['tiporeg']       = $row["tiporeg"];
                $sub_array['factafectada']  = Strings::avoidNull($row["factafectada"]);
                $sub_array['fecharetencion'] = !is_null($row["fecharetencion"]) ? date(FORMAT_DATE, strtotime($row["fecharetencion"])) : '';
                $sub_array['totalgravable_contribuye'] = Strings::rdecimal($row["totalgravable_contribuye"], 2);
                $sub_array['totalivacontribuye'] = Strings::rdecimal($row["totalivacontribuye"], 2);
                $sub_array['retencioniva']  = Strings::rdecimal($row["retencioniva"], 2);

                $otros_periodos[] = $sub_array;
            }
        }

        $totales_otros_periodos = array(
            "ivare" => Strings::rdecimal($ivare2, 2)
        );

        $resumen = array(
            array(
                "descripcion"    => 'Total ventas internas no gravadas',
                "base_imponible" => Strings::rdecimal($ve, 2),
                "credito_fiscal" => Strings::rdecimal(0, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(
                "descripcion"    => 'Total ventas de Exportación',
                "base_imponible" => Strings::rdecimal(0, 2),
                "credito_fiscal" => Strings::rdecimal(0, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(
                "descripcion"    => 'Total ventas internas Gravadas por Alicuota General (16%)',
                "base_imponible" => Strings::rdecimal($magbi16c, 2),
                "credito_fiscal" => Strings::rdecimal($mag16c, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(
                "descripcion"    => 'Total ventas internas Gravadas por Alicuota General',
                "base_imponible" => Strings::rdecimal(0, 2),
                "credito_fiscal" => Strings::rdecimal(0, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(
                "descripcion"    => 'Total ventas Gravadas por Alicuota reducida',
                "base_imponible" => Strings::rdecimal(0, 2),
                "credito_fiscal" => Strings::rdecimal(0, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(

                "descripcion"    => 'Total Ventas y Débitos Fiscales para efectos de determinación',
                "base_imponible" => Strings::rdecimal(($ve+$magbi16c), 2),
                "credito_fiscal" => Strings::rdecimal($mag16c, 2),
                "isBold" => true, "isColored" => true,
            ),
            array(
                "descripcion"    => 'Iva Retenidos periodos anteriores',
                "base_imponible" => Strings::rdecimal(0, 2),
                "credito_fiscal" => Strings::rdecimal($ivare2, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(
                "descripcion"    => 'Iva Retenidos en este periodo',
                "base_imponible" => Strings::rdecimal(0, 2),
                "credito_fiscal" => Strings::rdecimal($ivare, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(
                "descripcion"    => 'Total IVA Retenido',
                "base_imponible" => Strings::rdecimal(0, 2),
                "credito_fiscal" => Strings::rdecimal(($ivare2+$ivare), 2),
                "isBold" => true, "isColored" => true,
            ),
        );

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $results = array(
            "tabla"   => $data,
            "totales_libro" => $totales_libro,
            "otros_periodos" => $otros_periodos,
            "totales_otros_periodos" => $totales_otros_periodos,
            "resumen" => $resumen
        );

        echo json_encode($results);
        break;
}
