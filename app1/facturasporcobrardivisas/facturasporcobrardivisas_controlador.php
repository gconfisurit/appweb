<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("facturasporcobrardivisas_modelo.php");

//INSTANCIAMOS EL MODELO
$facturas = new facturasporcobrardivisas();
//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_facturasporcobrar":

    $datos = $facturas->getfacturasporcobrardivisas( $_POST["fechai"], $_POST["fechaf"]);

    //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
    $data = Array();
    $fechai=$_POST["fechai"];
    $fechaf=$_POST["fechaf"];

    foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();

       /* $fecha_E = date('d/m/Y', strtotime($row["FechaEmi"]));
        $fecha_D = date('d/m/Y', strtotime($row["FechaDesp"]));

        $tasa=$row["tasa"];

        $De_0_a_7_Dias = $row["De_0_a_7_Dias"];
        $De_8_a_14_Dias = $row["De_8_a_14_Dias"];
        $De_15_a_21_Dias = $row["De_15_a_21_Dias"];
        $De_22_a_31_Dias = $row["De_22_a_31_Dias"];
        $Mas_31_Dias = $row["Mas_31_Dias"];
        
        

        $sub_array[] = $row["TipoOpe"];
        $sub_array[] = $row["NroDoc"];
        $sub_array[] = $row["CodClie"];
        $sub_array[] = $row["Cliente"];
        $sub_array[] = $fecha_E;
        $sub_array[] = $row["FechaDesp"];
        $sub_array[] = $row["DiasTrans"];
        $sub_array[] = $row["DiasTransHoy"];
        if($tasa>=1){
            $sub_array[] = number_format(($De_0_a_7_Dias / $tasa), 2, ',', '.');
            $sub_array[] = number_format(($De_8_a_14_Dias / $tasa), 2, ',', '.');
            $sub_array[] = number_format(($De_15_a_21_Dias / $tasa), 2, ',', '.');
            $sub_array[] = number_format(($De_22_a_31_Dias / $tasa), 2, ',', '.');
            $sub_array[] = number_format(($Mas_31_Dias / $tasa), 2, ',', '.');
            $Montonew = ($De_0_a_7_Dias / $tasa) + ($De_8_a_14_Dias / $tasa) + ($De_15_a_21_Dias / $tasa) + ($De_22_a_31_Dias / $tasa) + ($Mas_31_Dias / $tasa);
            $sub_array[] = number_format( $Montonew, 2, ',', '.') ;
        }else{
             $sub_array[] = 0;
             $sub_array[] = 0;
             $sub_array[] = 0;
             $sub_array[] = 0;
             $sub_array[] = 0;
             $sub_array[] = 0;
        }
        
        $sub_array[] = $row["Ruta"];
        $sub_array[] = $row["Supervisor"];*/

        $Total_0_a_7_Dias = number_format($row["Total_0_a_7_Dias"], 2, ',', '.');
        $Total_8_a_15_Dias = number_format($row["Total_8_a_15_Dias"], 2, ',', '.');
        $Total_16_a_40_Dias = number_format($row["Total_16_a_40_Dias"], 2, ',', '.');
        $Total_Mayor_a_40_Dias = number_format($row["Total_Mayor_a_40_Dias"], 2, ',', '.');
        $SubTotal = number_format($row["SubTotal"], 2, ',', '.');

        $data1='1';
        $data2='2';
        $data3='3';
        $data4='4';
        $data5='5';

        $sub_array[] = $row["Empresa"];
        $sub_array[] = '<div class="col text-center"><a href="../facturasporcobrardivisas/detallesdivisas.php?fechai='.$fechai.'&fechaf='.$fechai.'&data='.$data1.'""> '.$Total_0_a_7_Dias.'</div>';
        $sub_array[] = '<div class="col text-center"><a href="../facturasporcobrardivisas/detallesdivisas.php?fechai='.$fechai.'&fechaf='.$fechai.'&data='.$data2.'""> '.$Total_8_a_15_Dias.'</div>';
        $sub_array[] = '<div class="col text-center"><a href="../facturasporcobrardivisas/detallesdivisas.php?fechai='.$fechai.'&fechaf='.$fechai.'&data='.$data3.'""> '.$Total_16_a_40_Dias.'</div>';
        $sub_array[] = '<div class="col text-center"><a href="../facturasporcobrardivisas/detallesdivisas.php?fechai='.$fechai.'&fechaf='.$fechai.'&data='.$data4.'""> '.$Total_Mayor_a_40_Dias.'</div>';
        $sub_array[] = '<div class="col text-center"><a href="../facturasporcobrardivisas/detallesdivisas.php?fechai='.$fechai.'&fechaf='.$fechai.'&data='.$data5.'""> '.$SubTotal.'</div>';


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


    case "detalle_de_facturas":


        $fechai=$_POST["fechai"];
        $fechaf=$_POST["fechaf"];
        $dataop= $_POST["dataop"];
        

       // var_dump($dataop);

        $datos = $facturas->getdetallesfacturasporcobrardivisas( $fechai, $fechaf, $dataop);
$suma_monto=0;
        foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();

        $SaldoPend = number_format($row["SaldoPend"], 2, ',', '.');
        
        $fecha_E = date('d/m/Y', strtotime($row["FechaEmi"]));
        $fecha_Desp = date('d/m/Y', strtotime($row["FechaDesp"]));

        $sub_array[] = $row["TipoOpe"];
        $sub_array[] = $row["NroDoc"];
        $sub_array[] = $row["CodClie"];
        $sub_array[] = $row["Cliente"];
        $sub_array[] = $fecha_E;
        $sub_array[] = $fecha_Desp;
        $sub_array[] = $row["DiasTrans"];
        $sub_array[] = $row["DiasTransHoy"];
        $sub_array[] = $SaldoPend;
        $sub_array[] = $row["ruta"];
        $sub_array[] = $row["Supervisor"];
        $suma_monto += $row["SaldoPend"];


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
}

?>
