<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("librocompras_modelo.php");

//INSTANCIAMOS EL MODELO
$librocompra = new LibroCompra();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_librocompras":

        $fechai = Dates::normalize_date($_POST['fechai']).' 00:00:00';
        $fechaf = Dates::normalize_date($_POST['fechaf']).' 23:59:59';

        $datos = $librocompra->getLibroPorFecha($fechai, $fechaf);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = $totales = $resumen = Array();

        $tcci = $mtoex = $totcom = $mtoiva = $retiva = 0;

        if (is_array($datos)==true and count($datos)>0)
        {
            foreach ($datos as $key => $row)
            {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $tcci += $row['totalcompraconiva'];
                $mtoex += $row['mtoexento'];
                $totcom += $row['totalcompra'];
                $mtoiva += $row['monto_iva'];
                $retiva += $row['retencioniva'];

                $sub_array['num']  = $key+1;
                $sub_array['fechacompra']  = date(FORMAT_DATE, strtotime($row["fechacompra"]));
                $sub_array['id3ex']        = $row["id3ex"];
                $sub_array['descripex']    = utf8_encode($row["descripex"]);
                $sub_array['tipodoc']      = $row["tipodoc"];
                $sub_array['nroretencion'] = Strings::avoidNull($row["nroretencion"]);
                $sub_array['numerodoc']    = $row["numerodoc"];
                $sub_array['nroctrol']     = Strings::avoidNull($row["nroctrol"]);
                $sub_array['tiporeg']      = $row["tiporeg"];
                $sub_array['docafectado']  = Strings::avoidNull($row["docafectado"]);
                $sub_array['totalcompraconiva'] = Strings::rdecimal($row["totalcompraconiva"], 2);
                $sub_array['mtoexento']    = Strings::rdecimal($row["mtoexento"], 2);
                $sub_array['totalcompra']  = Strings::rdecimal($row["totalcompra"], 2);
                $sub_array['alicuota_iva'] = Strings::rdecimal($row["alicuota_iva"], 0);
                $sub_array['monto_iva']    = Strings::rdecimal($row["monto_iva"], 2);
                $sub_array['retencioniva'] = Strings::rdecimal($row["retencioniva"], 2);
                $sub_array['porctreten']   = Strings::rdecimal($row["porctreten"], 0);
                $sub_array['fecharetencion'] = Strings::avoidNull($row["fecharetencion"]);

                $data[] = $sub_array;
            }
        }

        $totales = array(
            "tcci"   => Strings::rdecimal($tcci, 2),
            "mtoex"  => Strings::rdecimal($mtoex, 2),
            "totcom" => Strings::rdecimal($totcom, 2),
            "mtoiva" => Strings::rdecimal($mtoiva, 2),
            "retiva" => Strings::rdecimal($retiva, 2)
        );

        $resumen = array(
            array(
                "descripcion"    => 'Total Compras Exentas y/o sin derecho a crédito Fiscal',
                "base_imponible" => Strings::rdecimal($mtoex, 2),
                "credito_fiscal" => Strings::rdecimal(0, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(
                "descripcion"    => 'Total Compras Importación Afectas solo Alícuota General',
                "base_imponible" => Strings::rdecimal(0, 2),
                "credito_fiscal" => Strings::rdecimal(0, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(
                "descripcion"    => 'Total Compras Importación Afectas en Alícuota General + Adicional',
                "base_imponible" => Strings::rdecimal(0, 2),
                "credito_fiscal" => Strings::rdecimal(0, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(
                "descripcion"    => 'Total Compras Importación Afectas en Alícuota Reducida',
                "base_imponible" => Strings::rdecimal(0, 2),
                "credito_fiscal" => Strings::rdecimal(0, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(
                "descripcion"    => 'Total Compras Internas Afectas solo Alícuota General (16%): ',
                "base_imponible" => Strings::rdecimal($totcom, 2),
                "credito_fiscal" => Strings::rdecimal($mtoiva, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(
                "descripcion"    => 'Total Compras Internas Afectas solo Alícuota General + Adicional',
                "base_imponible" => Strings::rdecimal(0, 2),
                "credito_fiscal" => Strings::rdecimal(0, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(
                "descripcion"    => 'Total Compras Internas Afectas solo Alícuota Reducida',
                "base_imponible" => Strings::rdecimal(0, 2),
                "credito_fiscal" => Strings::rdecimal(0, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(

                "descripcion"    => 'Total Compras y créditos fiscales del período',
                "base_imponible" => Strings::rdecimal(($totcom+$mtoex), 2),
                "credito_fiscal" => Strings::rdecimal($mtoiva, 2),
                "isBold" => true, "isColored" => true,
            ),
            array(
                "descripcion"    => 'Créditos Fiscales producto de la aplicación del porcentaje de la prorrata',
                "base_imponible" => Strings::rdecimal(0, 2),
                "credito_fiscal" => Strings::rdecimal(0, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(
                "descripcion"    => 'Excedente de Crédito Fiscal del Periodo Anterior',
                "base_imponible" => Strings::rdecimal(0, 2),
                "credito_fiscal" => Strings::rdecimal(0, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(
                "descripcion"    => 'Ajustes a los créditos fiscales de periodos anteriores',
                "base_imponible" => Strings::rdecimal(0, 2),
                "credito_fiscal" => Strings::rdecimal(0, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(
                "descripcion"    => 'Compras no gravadas y/o sin derecho a credito fiscal',
                "base_imponible" => Strings::rdecimal(0, 2),
                "credito_fiscal" => Strings::rdecimal(0, 2),
                "isBold" => true, "isColored" => true,
            ),
        );

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $results = array(
            "tabla"   => $data,
            "totales" => $totales,
            "resumen" => $resumen
        );

        echo json_encode($results);
        break;
}
