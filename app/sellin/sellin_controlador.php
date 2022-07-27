<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("sellin_modelo.php");

//INSTANCIAMOS EL MODELO
$sellin = new sellin();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_sellin":

    if($_POST["tipo"]=='f'){

        $datos = $sellin->getsellin($_POST["fechai"], $_POST["fechaf"], $_POST["marca"],$_POST["tipo"]);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        $suma_montod= $suma_monto=0;
        foreach ($datos as $row) {
        //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();
      // $fecha_E = '';
        $datos2 = $sellin->getfechasellin($_POST["fechai"], $_POST["fechaf"], $_POST["marca"],$row["coditem"],$_POST["tipo"]);
        foreach ($datos2 as $row2) {
            $fecha_E = date('d/m/Y', strtotime($row2["FechaE"]));
        }

        $sub_array[] = $row["coditem"];
        $sub_array[] = $row["producto"];
        $sub_array[] = $row["marca"];
        $sub_array[] = $fecha_E;
        $sub_array[] = Strings::rdecimal($row["compras"],2);
        $sub_array[] = Strings::rdecimal($row["devol"],2);
        $sub_array[] = 0;
        $sub_array[] = 0;
        $sub_array[] = Strings::rdecimal($row["total"],2);
        $suma_monto += $row["total"];
        $data[] = $sub_array;
        }

    }else{

             if($_POST["tipo"]=='n'){

                $datos = $sellin->getsellin($_POST["fechai"], $_POST["fechaf"], $_POST["marca"],$_POST["tipo"]);

                //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
                $data = Array();
                $suma_montod= $suma_monto=0;
                foreach ($datos as $row) {
                    //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                    $sub_array = array();
                   // $fecha_E = '';
                    $datos2 = $sellin->getfechasellin($_POST["fechai"], $_POST["fechaf"], $_POST["marca"],$row["coditem"],$_POST["tipo"]);
                    foreach ($datos2 as $row2) {
                        $fecha_E = date('d/m/Y', strtotime($row2["FechaE"]));
                    }

                    $sub_array[] = $row["coditem"];
                    $sub_array[] = $row["producto"];
                    $sub_array[] = $row["marca"];
                    $sub_array[] = $fecha_E;
                    $sub_array[] = 0;
                    $sub_array[] = 0;
                    $sub_array[] = Strings::rdecimal($row["compras"],2);
                    $sub_array[] = Strings::rdecimal($row["devol"],2);
                    $sub_array[] = Strings::rdecimal($row["total"],2);
                    $suma_monto += $row["total"];
                    $data[] = $sub_array;
                }

            }else{

                 if($_POST["tipo"]=='Todos'){

                    $datos = $sellin->getsellin($_POST["fechai"], $_POST["fechaf"], $_POST["marca"],$_POST["tipo"]);

                    //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
                    $data = Array();
                    $suma_montod= $suma_monto=0;
                    foreach ($datos as $row) {
                        //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                        $sub_array = array();
                       // $fecha_E = '';
                        $datos2 = $sellin->getfechasellin($_POST["fechai"], $_POST["fechaf"], $_POST["marca"],$row["coditem"],$_POST["tipo"]);
                        foreach ($datos2 as $row2) {
                            $fecha_E = date('d/m/Y', strtotime($row2["FechaE"]));
                        }

                        $sub_array[] = $row["coditem"];
                        $sub_array[] = $row["producto"];
                        $sub_array[] = $row["marca"];
                        $sub_array[] = $fecha_E;
                        $sub_array[] = Strings::rdecimal($row["compras"],2);
                        $sub_array[] = Strings::rdecimal($row["devol"],2);
                        $sub_array[] = Strings::rdecimal($row["compras_notas"],2);
                        $sub_array[] = Strings::rdecimal($row["devol_notas"],2);
                        $sub_array[] = Strings::rdecimal($row["total"],2);
                        
                        if($row["tipodoc"]=='H'){
                             $suma_monto += $row["total"];
                        }else{
                                if($row["tipodoc"]=='J'){
                                $suma_montod += $row["total"];
                               }
                        }
                        $data[] = $sub_array;
                    }

                }


            }


    }

    

    //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
    $results = array(
        "sEcho" => 1, //INFORMACION PARA EL DATATABLE
        "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
        'Mtototal' => Strings::rdecimal($suma_monto, 2),
        'Mtototald' => Strings::rdecimal($suma_montod, 2),
        "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
        "aaData" => $data
    );

    echo json_encode($results);
    break;

    case "listar_marcas":

        $output["lista_marcas"] = Marcas::todos();

        echo json_encode($output);
        break;

}
