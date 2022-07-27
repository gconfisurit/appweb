<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("listadeprecio_modelo.php");

//INSTANCIAMOS EL MODELO
$precios = new Listadeprecio();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    
        case "listar":
            
            $depos = $_POST['depo'];
            $marcas = $_POST['marca'];
            $orden = $_POST['orden'];
            $exis = $_POST['exis'];
            $iva = $_POST['iva'];
            $cubi = $_POST['cubi'];
            $paque = $_POST['paquete'];
            $blto = $_POST['bulto'];
            $p1 = str_replace("1","1",$_POST['p1']);
            $p2 = str_replace("1","2",$_POST['p2']);
            $p3 = str_replace("1","3",$_POST['p3']);
            $sumap = $_POST['p1'] + $_POST['p2'] + $_POST['p3'];
            $sumap2 = $p1 + $p2 + $p3;
    
            $datos = $precios->getListadeprecios($marcas, $depos, $exis, $orden);
            $num = count($datos);
    
            /** TITULO DE LAS COLUMNAS DE LA TABLA **/
            $thead = Array();
            $thead[] = Strings::titleFromJson('codigo_prod');
            $thead[] = Strings::titleFromJson('descrip_prod');
            $thead[] = Strings::titleFromJson('marca_prod');
            //<!--BULTOS-->
            if($blto == '1'){

                $thead[] = Strings::titleFromJson('bultos');
            }else{

            }

            switch ($sumap) {
                case 1:
                    $thead[] = "Precio $sumap2 Bulto";
                    break;
                case 2:
                    $aux1 = ($p1 == 1) ? $p1 : $p2;
                    $aux2 = ($p3 == 3) ? $p3 : $p2;
                    $thead[] = "Precio $aux1 Bulto";
                    $thead[] = "Precio $aux2 Bulto";
                    break;
                default: 
                    $thead[] = Strings::titleFromJson('precio1_bulto');
                    $thead[] = Strings::titleFromJson('precio2_bulto');
                    $thead[] = Strings::titleFromJson('precio3_bulto');
            }
            //<!--PAQUETES-->

            if($paque == '1'){

              $thead[] = Strings::titleFromJson('paquetes');

            }else{

            }
            switch ($sumap) {
                case 1:
                    $thead[] = "Precio $sumap2 Paquete";
                    break;
                case 2:
                    $aux1 = ($p1 == 1) ? $p1 : $p2;
                    $aux2 = ($p3 == 3) ? $p3 : $p2;
                    $thead[] = "Precio $aux1 Paquete";
                    $thead[] = "Precio $aux2 Paquete";
                    break;
                 default: 
                    $thead[] = Strings::titleFromJson('precio1_paquete');
                    $thead[] = Strings::titleFromJson('precio2_paquete');
                    $thead[] = Strings::titleFromJson('precio3_paquete');
            }
            if ($cubi == 1) {
                $thead[] = Strings::titleFromJson('cubicaje');
            }
    
            /** CONTENIDO DE LA TABLA **/
            //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
            $data = Array();
            foreach ($datos as $row) {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();
    
                if (!$row['esexento']) {
                    $precio1 = $row['precio1'] * $iva;
                    $precio2 = $row['precio2'] * $iva;
                    $precio3 = $row['precio3'] * $iva;
                    $preciou1 = $row['preciou1'] * $iva;
                    $preciou2 = $row['preciou2'] * $iva;
                    $preciou3 = $row['preciou3'] * $iva;
                } else {
                    $precio1 = $row['precio1'];
                    $precio2 = $row['precio2'];
                    $precio3 = $row['precio3'];
                    $preciou1 = $row['preciou1'];
                    $preciou2 = $row['preciou2'];
                    $preciou3 = $row['preciou3'];
                }
    
                $sub_array[] = $row["codprod"];
                $sub_array[] = $row["descrip"];
                $sub_array[] = $row["marca"];
                //<!--BULTOS-->

                if($blto == '1'){

                  $sub_array[] = round($row['existen']);
      
                  }else{
      
                  }
                switch ($sumap) {
                    case 1:
                        if ($row['esexento'] == 0)
                        {
                            $sub_array[] = Strings::rdecimal($row['precio'. $sumap2 ] * $iva, 2);
                        } else {
                            $sub_array[] = Strings::rdecimal($row['precio'. $sumap2 ], 2);
                        }
                        break;
                    case 2:
                        if ($p1 == 1)
                        {
                            $sub_array[] = Strings::rdecimal($precio1, 2);
                        } else {
                            $sub_array[] = Strings::rdecimal($precio2, 2);
                        }
                        if ($p3 == 3)
                        {
                            $sub_array[] = Strings::rdecimal($precio3, 2);
                        } else {
                            $sub_array[] = Strings::rdecimal($precio2, 2);
                        }
                        break;
                   default: 
                        $sub_array[] = Strings::rdecimal($precio1, 2);
                        $sub_array[] = Strings::rdecimal($precio2, 2);
                        $sub_array[] = Strings::rdecimal($precio3, 2);
                }
                // <!--PAQUETES-->


                if($paque == '1'){

                   $sub_array[] = round($row['exunidad']);
        
                    }else{
        
                    }
                switch ($sumap) {
                    case 1:
                        if ($row['esexento'] == 0)
                        {
                            $sub_array[] = Strings::rdecimal($row['preciou'. $sumap2 ]* $iva, 2);
                        } else {
                            $sub_array[] = Strings::rdecimal($row['preciou'. $sumap2 ], 2);
                        }
                        break;
                    case 2:
                        if ($p1 == 1)
                        {
                            $sub_array[] = Strings::rdecimal($preciou1, 2);
                        } else {
                            $sub_array[] = Strings::rdecimal($preciou2, 2);
                        }
                        if ($p3 == 3)
                        {
                            $sub_array[] = Strings::rdecimal($preciou3, 2);
                        } else {
                            $sub_array[] = Strings::rdecimal($preciou2, 2);
                        }
                        break;
                     default: 
                        $sub_array[] = Strings::rdecimal($preciou1, 2);
                        $sub_array[] = Strings::rdecimal($preciou2, 2);
                        $sub_array[] = Strings::rdecimal($preciou3, 2);
                    }
                if ($cubi == 1) {
                    $sub_array[] = $row['cubicaje'];
                }
    
                $data[] = $sub_array;
            }
    
            //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
            $output = array(
                "sEcho" => 1, //INFORMACION PARA EL DATATABLE
                "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
                "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
                "columns" => $thead,
                "aaData" => $data);
            echo json_encode($output);
    
            break;










        

    case "listar_depositos_marcas":
        //DEPOSITOS
        $output["lista_depositos"] = Almacen::todos();
        //MARCAS
        $output["lista_marcas"] = Marcas::todos();

        echo json_encode($output);
        break;

}
?>