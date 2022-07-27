<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("tabladinamica_modelo.php");

//INSTANCIAMOS EL MODELO
$tabladinamica = new Tabladinamica();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_tabladinamica":

        $data = array(
            'fechai' => $_POST['fechai'],
            'fechaf' => $_POST['fechaf'],
            'marca'  => $_POST['marca'],
            'edv'    => $_POST['edv'],
        );

        $datos = array();
        switch ($_POST['tipo']) {
            case 'f': $datos = $tabladinamica->getTabladinamicaFactura($data); break;
            case 'n': $datos = $tabladinamica->getTabladinamicaNotaDeEntrega($data); break;
        }

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $arr_data = Array();

        $paqt = $bult = $kilo = $total = 0;

        if (is_array($datos)==true and count($datos)>0)
        {
            foreach ($datos as $key => $row)
            {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $montod = $montobs = $descuento = 0;

              /*  $multiplicador = in_array($row['tipo'], array('A','C'))
                    ? 1
                    : -1;*/

                    if($row['tipo']=='B' or $row['tipo']=='D'){
                            $multiplicador = -1;
                        }else{
                            $multiplicador = 1;
                        }

                switch ($_POST['tipo']) {
                    case 'f':
                        $montod = (is_numeric($row["factor"]) and $row["factor"]>0)
                            ? Numbers::avoidNull($row["montod"]) / $row["factor"]
                            : 0;

                        $montobs = $row['montod'];

                        $descuento = (is_numeric($row["factor"]) and $row["factor"]>0)
                            ? Numbers::avoidNull($row["descuento"]) / $row["factor"]
                            : 0;
                        break;
                    case 'n':
                        $montod = Numbers::avoidNull($row["montod"]);

                        $montobs = (is_numeric($row["factor"]) and $row["factor"]>0)
                            ? Numbers::avoidNull($row["montod"]) * $row["factor"]
                            : 0;

                        $descuento = Numbers::avoidNull($row["descuento"]);
                        break;
                }

                $sub_array['num']  = $key+1;
                $sub_array['codvend']       = $row["codvend"];
                $sub_array['vendedor']      = $row["vendedor"];
                $sub_array['clasevend']     = $row["clasevend"];
                $sub_array['tipo']          = $row["tipo"];
                $sub_array['numerod']       = $row["numerod"];
                $sub_array['codclie']       = $row["codclie"];
                $sub_array['cliente']       = utf8_encode($row["cliente"]);
                $sub_array['codnestle']     = Strings::avoidNull($row["codnestle"]);
                $sub_array['clasificacion'] = Strings::avoidNull($row["clasificacion"]);
                $sub_array['coditem']       = $row["coditem"];
                $sub_array['descripcion']   = utf8_encode($row["descripcion"]);
                $sub_array['marca']         = $row["marca"];
                $sub_array['cantidad']      = $row["cantidad"] * $multiplicador;
                $sub_array['unid']          = $row["unid"];
                $sub_array['paq']           = Strings::rdecimal($row["paq"] * $multiplicador, 1);
                $sub_array['bul']           = number_format($row["bul"] * $multiplicador, 2);
                $sub_array['kg']            = Strings::rdecimal($row["kg"] * $multiplicador, 1);
                $sub_array['instancia']     = $row["instancia"];
                $sub_array['montod']        =  Strings::rdecimal($montod  * $multiplicador, 2);
                $sub_array['descuento']     =  Strings::rdecimal($descuento  * $multiplicador, 2);
                $sub_array['factor']        =  Strings::rdecimal($row['factor'], 2);
                $sub_array['montobs']       =  Strings::rdecimal($montobs * $multiplicador, 2);
                $sub_array['fechae']        = date(FORMAT_DATE, strtotime($row["fechae"]));
                $sub_array['mes']           =  utf8_encode($row['MES']);

                $paqt  += $row["paq"] * $multiplicador;
                $bult  += $row["bul"] * $multiplicador;
                $kilo  += $row["kg"]  * $multiplicador;
                $total += $montod * $multiplicador;

                $arr_data[] = $sub_array;
            }
        }

        $total = (hash_equals('n', $_POST['tipo']))
            ? Numbers::avoidNull($tabladinamica->getTotalNotaDeEntrega($data,'C')[0]['montod']) - Numbers::avoidNull($tabladinamica->getTotalNotaDeEntrega($data, 'D')[0]['montod'])
            : $total;

        $totales_tabladinamica = array(
            "paqt"  => Strings::rdecimal($paqt, 2),
            "bult"  => Strings::rdecimal($bult, 2),
            "kilo"  => Strings::rdecimal($kilo, 2),
            "total" => Strings::rdecimal($total, 2),
        );



        switch ($_POST['tipo']) {
            case 'f': $resumen = $tabladinamica->getResumenFactura($data); break;
            case 'n': $resumen = $tabladinamica->getResumenNotaDeEntrega($data); break;
        }

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $arr_data1 = Array();

        if (is_array($resumen)==true and count($resumen)>0)
        {
            foreach ($resumen as $key => $row)
            {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $descuentototal = $descuentototalbs = 0;

                switch ($_POST['tipo']) {
                    case 'f':
                        $descuentototal =  ($row["descto1"] > 0 & $row["descto2"] > 0)
                            ? ($row["descto1"] + $row["descto2"]) / $row["tasa"]
                            :  $row["descto1"] / $row["tasa"];

                        $descuentototalbs =  ($row["descto1"] > 0 & $row["descto2"] > 0)
                            ? $row["descto1"] + $row["descto2"]
                            : $row["descto1"];
                        break;
                    case 'n':
                        $descuentototal   = $row["descuento"];

                        $descuentototalbs = $row["descuento"] * $row["tasa"];
                        break;
                }

                $sub_array['num']              = $key+1;
                $sub_array['codvend']          = $row["codvend"];
                $sub_array['codclie']          = $row["codclie"];
                $sub_array['descrip']          = $row["descrip"];
                $sub_array['descuentototal']   = Strings::rdecimal($descuentototal, 2);
                $sub_array['tasa']             = Strings::rdecimal($row["tasa"], 2);
                $sub_array['descuentototalbs'] = Strings::rdecimal($descuentototalbs, 2);
                $sub_array['numerod']          = utf8_decode($row["numerod"]);
                $sub_array['tipofac']          = $row["tipofac"];
                $sub_array['fechae']           = date(FORMAT_DATE, strtotime($row["fechae"]));

                $arr_data1[] = $sub_array;
            }
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $results = array(
            "tabla"   => $arr_data,
            "totales" => $totales_tabladinamica,
            "resumen" => $arr_data1
        );

        echo json_encode($results);
        break;

    case "listar_marcas":

        $output["lista_marcas"] = Marcas::todos();

        echo json_encode($output);
        break;

    case "listar_vendedores":

        $output['lista_vendedores'] = Vendedores::todos();

        echo json_encode($output);
        break;
}
