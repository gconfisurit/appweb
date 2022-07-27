<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("NEcobros_modelo.php");

//INSTANCIAMOS EL MODELO
$nexC = new NExcobrar();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_cliente":
        $datos = $nexC->getNEporcobrar( $_POST["vendedor"]);

    //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
    //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
    $data = Array();
    $vendedor=$_POST["vendedor"];
    $data1='1';
    $data2='2';
    $data3='3';
    $data4='4';
    $data5='5';

    foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();
        
        $Total_0_a_7_Dias = number_format($row["Total_0_a_7_Dias"], 2, ',', '.');
        $Total_8_a_15_Dias = number_format($row["Total_8_a_15_Dias"], 2, ',', '.');
        $Total_16_a_40_Dias = number_format($row["Total_16_a_40_Dias"], 2, ',', '.');
        $Total_Mayor_a_40_Dias = number_format($row["Total_Mayor_a_40_Dias"], 2, ',', '.');
        $SubTotal = number_format($row["SubTotal"], 2, ',', '.');

        $sub_array[] = $row["Empresa"];
        $sub_array[] = '<div class="col text-center"><a href="../NEcobros/detalles.php?vendedor='.$vendedor.'&data='.$data1.'""> '.$Total_0_a_7_Dias.'</div>';
        $sub_array[] = '<div class="col text-center"><a href="../NEcobros/detalles.php?vendedor='.$vendedor.'&data='.$data2.'""> '.$Total_8_a_15_Dias.'</div>';
        $sub_array[] = '<div class="col text-center"><a href="../NEcobros/detalles.php?vendedor='.$vendedor.'&data='.$data3.'""> '.$Total_16_a_40_Dias.'</div>';
        $sub_array[] = '<div class="col text-center"><a href="../NEcobros/detalles.php?vendedor='.$vendedor.'&data='.$data4.'""> '.$Total_Mayor_a_40_Dias.'</div>';
        $sub_array[] = '<div class="col text-center"><a href="../NEcobros/detalles.php?vendedor='.$vendedor.'&data='.$data5.'""> '.$SubTotal.'</div>';

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

        $vendedor=$_POST["vendedor"];
        $dataop= $_POST["dataop"];
        

       // var_dump($dataop);

       $datos = $nexC->getdetallesNEporcobrar($vendedor, $dataop);
       $suma_monto=0;
        foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();

        $SaldoPend = number_format($row["SaldoPend"], 2, ',', '.');
        $fecha_E = date('d/m/Y', strtotime($row["FechaEmi"]));

        if ($dataop=='5') {
            $t07 = number_format($row["Total_0_a_7_Dias"], 2, ',', '.');
            $t815 = number_format($row["Total_8_a_15_Dias"], 2, ',', '.');
            $t1640 = number_format($row["Total_16_a_40_Dias"], 2, ',', '.');
            $tm40 = number_format($row["Total_Mayor_a_40_Dias"], 2, ',', '.');
        }        

        $sub_array[] = $row["TipoOpe"];
        $sub_array[] = $row["NroDoc"];
        $sub_array[] = $row["CodClie"];
        $sub_array[] = $row["Cliente"];
        $sub_array[] = $fecha_E;
        $sub_array[] = $row["DiasTransHoy"];
        if ($dataop=='5') {
            $sub_array[] = $t07;
            $sub_array[] = $t815;
            $sub_array[] = $t1640;
            $sub_array[] = $tm40;
        }
        $sub_array[] = $SaldoPend;
        $sub_array[] = $row["Ruta"];
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

        
    case "listar_vendedores":

        $output['lista_vendedores'] = Vendedores::todos();

        echo json_encode($output);
        break;
}
