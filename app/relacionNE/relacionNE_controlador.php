<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("relacionNE_modelo.php");

//INSTANCIAMOS EL MODELO
$notaentrega = new relacionNE();
$clientes = new relacionNE();
//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_notaentrega":

        $fechai= $_POST["fechai"];
          $fechaf= floatval($_POST["fechaf"])+1;
         $ruta=  $_POST["ruta"];

    $datos = $notaentrega->getdevolucionnotaentrega( $fechai, $fechaf,$ruta);
    $suma_submonto=0;
    $suma_monto=0;
    //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
    $data = Array();

    foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();
        if($row["tipofac"]=='C'){
            $tipo='N/E';
        }
       $estado='';
       $saldo = 0;
       $montototal =0;
        $montototaldv = $abono =0; 
        $montoDEV =0;

        $auxDEV =  $notaentrega->getmontoDEV( $row["numerodv"]);
        foreach ($auxDEV as $row1) 
            {
                $montoDEV = $row1["total"];
            }


        $descuentosanota = $notaentrega->get_descuentosanota( $row["numerod"]);
        $descuentosaitemnota = $notaentrega->get_descuentosaitemnota( $row["numerod"]);

         if ($descuentosaitemnota[0]["descuento"] > 0 &&  $descuentosanota[0]["descuento"] > 0) {
        $tdescuento = $descuentosaitemnota[0]["descuento"] +  $descuentosanota[0]["descuento"];
      } elseif ($descuentosaitemnota[0]["descuento"] > 0) {
        $tdescuento = $descuentosaitemnota[0]["descuento"];
      } elseif ($descuentosanota[0]["descuento"] > 0) {
        $tdescuento =  $descuentosanota[0]["descuento"];
      } else {
        $tdescuento = 0;
      }

      if ($row["estatus"] == 0) {
          $estado= "Pendiente";
        } elseif ($row["estatus"] == 1) {
           $estado= "Abono";
        } elseif ($row["estatus"] == 2) {
           $estado= "Facturada";
        } elseif ($row["estatus"] == 4) {
           $estado= "Devolucion";
        } elseif ($row["estatus"] == 3) {
           $estado= "Pagada";
        }elseif ($row["estatus"] == 5) {
            $estado= "PROCESADA";
        }


        
       $montototal = $row["total"];
        $abono =$row["abono"];
        $montototaldv = $montoDEV;


           if (count($auxDEV)!= 0 & $row["estatus"] == 0) {
                
                $saldo=($montototal - $abono) - $montototaldv ;

            } else {

                if (count($auxDEV)!= 0 & $row["estatus"] == 1) {
                
                $saldo=($montototal - $abono) - $montototaldv ;

                } else{
                   
                    if ($row["estatus"] == 0) {
                
                       $saldo=($montototal - $abono);

                    } else{
                        if ($row["estatus"] == 1) {
                
                           $saldo=($montototal - $abono);

                         } else{
                             if ($row["estatus"] ==3) {
                
                             $saldo=0;

                            }else{
                                $saldo=0;
                            }
                         }
                    }
                }
                
                
            }

        if ($row["numerodv"] == 0 or $row["numerodv"] =='' or $row["numerodv"] =='NULL') {
           $devolucion= "0";
        }else{
            $devolucion= $row["numerodv"];
        }


        $fecha_E = date('d/m/Y', strtotime($row["fechae"]));
        $total = number_format($row["total"], 2, ',', '.');
        $sub_array[] = $tipo;
        $sub_array[] = $row["numerod"];
        $sub_array[] = $row["numerof"];
        $sub_array[] = $devolucion;
        $sub_array[] = $row["rif"];
        $sub_array[] = $row["rsocial"];
        $sub_array[] = $fecha_E;
        $sub_array[] = $row["codvend"];
        $sub_array[] = $total;
        $sub_array[] =  $montoDEV;
        $sub_array[] =  number_format($row["abono"], 2, ',', '.');
        $sub_array[] =  $saldo;
        $sub_array[] =  $tdescuento;
        $sub_array[] =   $estado;
        
        $suma_monto += $row["total"];

        $data[] = $sub_array;

    }

    //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
    $results = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            'Mtototal' => Strings::rdecimal($suma_monto, 2),
            "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data);

    echo json_encode($results);
    break;

    case "listar_vendedores":

        $output['lista_vendedores'] = vendedores::todos();

        echo json_encode($output);
        break;

    
}