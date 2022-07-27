<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("inventarioglobal_modelo.php");

//INSTANCIAMOS EL MODELO
$invglobal = new InventarioGlobal();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_inventarioglobal":

        //verificamos si existe al menos 1 deposito selecionado
        //y se crea el array.
        $depos = $_POST['depo'] ?? array();

        $fechaf = date('Y-m-d');
        $dato = explode("-", $fechaf); //Hasta
        $aniod = $dato[0]; //año
        $mesd = $dato[1]; //mes
        $diad = "01"; //dia
        $fechai = $aniod . "-01-01";

        $coditem = $cantidad = $tipo = array();
        $t = 0;

        $devolucionesDeFactura = $invglobal->facturasindespachar($fechai, $fechaf, $depos);
        if(count($devolucionesDeFactura) > 0) {
            foreach ($devolucionesDeFactura as $devol) {
                $coditem[] = $devol['descrip'];
                $cantidad[] = $devol['numerod'];
                $tipo[] = $devol['NumeroR'];
                $t += 1;
            }
        }

        $relacion_inventarioglobal = $invglobal->getInventarioGlobal($fechai, $fechaf, $depos);
        $tbulto = $tpaq = $tbultoinv = $tpaqinv = $tbultsaint = $tpaqsaint = 0;
        $cant_paq = 0;
        $cant_bul = 0;
        $i=0;
        //DECLARAMOS ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        $totales = Array();

        foreach ($relacion_inventarioglobal as $key=>$row) {

            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            if($t > 0) {
                for($e = 0; $e < $t; $e++)
                {
                    if($coditem[$e] == $row['CodProd']) {
                        switch ($tipo[$e]) {
                            case '0':
                                $cant_bul = $row['bultosxdesp'] - $cantidad[$e];
                                break;
                            case '1':
                                $cant_paq = $row['paqxdesp'] - $cantidad[$e];
                                break;
                        }
//                        $e = $t + 2;
                        break;
                    }else{
                        $cant_bul = $row['bultosxdesp'];
                        $cant_paq = $row['paqxdesp'];
                    }
                }
            } else {
                $cant_bul = $row['bultosxdesp'];
                $cant_paq = $row['paqxdesp'];
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
            $sub_array['codprod']  = $row["CodProd"];
            $sub_array['descrip']  = $row["Descrip"];
            $sub_array['cant_bul'] = Strings::rdecimal($cant_bul,0);
            $sub_array['cant_paq'] = Strings::rdecimal($cant_paq,0);
            $sub_array['invbut']   = Strings::rdecimal($invbut,0);
            $sub_array['invpaq']   = Strings::rdecimal($invpaq,0);
            $sub_array['tinvbult'] = Strings::rdecimal($tinvbult,0);
            $sub_array['tinvpaq']  = Strings::rdecimal($tinvpaq, 0);

            //ACUMULAMOS LOS TOTALES
            $tbulto     += $cant_bul;
            $tpaq       += $cant_paq;
            $tbultoinv  += $tinvbult;
            $tpaqinv    += $tinvpaq;
            $tbultsaint += $invbut;
            $tpaqsaint  += $invpaq;

            //AGREGAMOS AL ARRAY DE CONTENIDO DE LA TABLA
            $data[] = $sub_array;
        }

        //CREAMOS UN SUB_ARRAY PARA ALMACENAR LOS DATOS ACUMULADOS
        $totales = array();
        $totales['tbulto']     = Strings::rdecimal($tbulto,0);
        $totales['tpaq']       = Strings::rdecimal($tpaq,0);
        $totales['tbultsaint'] = Strings::rdecimal($tbultsaint,0);
        $totales['tpaqsaint']  = Strings::rdecimal($tpaqsaint,0);
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
}