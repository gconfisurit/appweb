<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("notadeentrega_modelo.php");

//INSTANCIAMOS EL MODELO
$nota = new NotaDeEntrega();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_notadeentrega":
        $output = array();
        $numerod = $_POST["nrodocumento"];

        $empresa = Empresa::getInfo();
        $cabecera = NotasDeEntrega::getHeaderById($numerod);
        $detalle = NotasDeEntrega::getDetailById($numerod);

        # darle formatos y validar datos de la cabecera
        if (ArraysHelpers::validate($cabecera)) {
            $codclie = $cabecera['codclie'];
            $cabecera['representante']  = $nota->get_datos_cliente($codclie)['represent'];
            $cabecera['descuentoitem']  = Numbers::avoidNull( $nota->get_descuento($numerod, 'C')['descuento'] );
            $cabecera['descuentototal'] = Numbers::avoidNull($cabecera['descuento']);
            $cabecera['fechae']    = Date(FORMAT_DATE, strtotime($cabecera['fechae']));
            $cabecera['telefono']  = Strings::avoidNull($cabecera['telefono']);
            $cabecera['notas1']    = Strings::avoidNull($cabecera['notas1']);
            $cabecera['subtotal']  = Strings::rdecimal($cabecera['subtotal']);
            $cabecera['descuento'] = Strings::rdecimal($cabecera['descuento']);
            $cabecera['total']     = Strings::rdecimal($cabecera['total'],2);
        }

        # darle formatos y validar datos del detalle
        if (ArraysHelpers::validate($detalle)) {
            $data_detalle = array();
            foreach ($detalle as $key => $row) {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $sub_array['coditem']     = $row["coditem"];
                $sub_array['descripcion'] = utf8_encode($row["descripcion"]);
                $sub_array['cantidad']    = Strings::rdecimal($row["cantidad"], 0);
                $sub_array['unidad']      = ($row['esunidad'] == '1') ? 'PAQ' : 'BUL';
                $sub_array['precio']      = Strings::rdecimal($row['precio']);
                $sub_array['totalitem']   = Strings::rdecimal($row["totalitem"], 2);
                $sub_array['descuento']   = Strings::rdecimal($row["descuento"], 2);
                $sub_array['total']       = Strings::rdecimal($row["total"], 2);
                

                $data_detalle[] = $sub_array;
            }
            $detalle = $data_detalle;
        }

        $output = array(
            'empresa' => $empresa,
            'cabecera' => $cabecera,
            'detalle' => $data_detalle,
        );

        echo json_encode($output);
        break;
}
