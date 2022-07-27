<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("devoluciones_modelo.php");

//INSTANCIAMOS EL MODELO
$devoluciones = new devolucionesdata();
//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_devoluciones":

    $datos = $devoluciones->getdevoluciones( $_POST["fechai"], $_POST["fechaf"],$_POST["ruta"],$_POST["tipo"]);

    //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
    $data = Array();
    $suma_monto=0;
    foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();

        if($row["tipofac"]=='B'){
            $tipo='DEVOLUCION FACT';
        }else{
            $tipo='DEVOLUCION N/E';
        }

        $fecha_E = date('d/m/Y', strtotime($row["fecha_fact"]));
        $total = number_format($row["Monto"], 2, ',', '.');

        $sub_array[] = $tipo;
        $sub_array[] = $row["code_vendedor"];
        $sub_array[] = $row["numerod"];
        $sub_array[] = $fecha_E;
        $sub_array[] = $row["cod_clie"];
        $sub_array[] = $row["cliente"];
        $sub_array[] = $row["chofer"];
        $sub_array[] = $total;
        $sub_array[] = $row["motivo"];
 $sub_array[] ='<div class="col text-center">
                                <button type="button" data-toggle="modal" onclick="obtener_datos(\'' . $row["numerod"] . '\',\'' . $row["tipofac"] . '\');" data-target="#editarMotivo" class="btn btn-info btn-sm update">Editar Motivo</button>
                            </div>';
      
       
        
        $suma_monto += $row["Monto"];

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



    case "editarMotivo":

       /* echo "<script>console.log('motivo: " . $_POST["motivo"] . "' );</script>";
        echo "<script>console.log('ndevolucion: " . $_POST["ndevolucion"] . "' );</script>";*/
        $datos = false;
        $motivo= '';
        $ndevolucion='';
        $motivo= $_POST["motivo"];
        $ndevolucion=$_POST["ndevolucion"];
        $tipo=$_POST["tipo"];


        $datos = $devoluciones->editarMotivo(  $motivo,$ndevolucion,$tipo);

        if($datos){
            $output = [
                "mensaje" => "Editado con Exito!",
                "icono"   => "success"
            ];
        } else {
            $output = [
                "mensaje" => "Ocurrió un error al Editar!",
                "icono"   => "error"
            ];
        }

        echo json_encode($output);
        break;


    
}