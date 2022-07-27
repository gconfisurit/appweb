<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("disponiblealmacen_modelo.php");

//INSTANCIAMOS EL MODELO
$invglobal = new disponiblealmacen();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_marcas":

        $output['lista_marcas'] = Marcas::todos();

        echo json_encode($output);
        break;

        case "buscar_inventario":
            $datos = $invglobal->getdisponiblealmacen( $_POST["marcas"]);

            //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
            $data = Array();
        
            foreach ($datos as $row) {
                    //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();
       
         if( $row["CodUbic"]==='01'){
            $sub_array[] = $row["codprod"];
            $sub_array[] = $row["Descrip"];
            $sub_array[] = $row["marca"];
            $sub_array[] = number_format($row["Bultos"], 2, ',', '.');
            $sub_array[] = number_format($row["Paquetes"], 2, ',', '.');
            $sub_array[] = number_format(0, 2, ',', '.');
            $sub_array[] = number_format(0, 2, ',', '.');
            $sub_array[] = number_format(0, 2, ',', '.');
            $sub_array[] = number_format(0, 2, ',', '.');

        }else{
            if($row["CodUbic"]==='03'){
                $sub_array[] = $row["codprod"];
                $sub_array[] = $row["Descrip"];
                $sub_array[] = $row["marca"];
                $sub_array[] = number_format(0, 2, ',', '.');
                $sub_array[] = number_format(0, 2, ',', '.');
                $sub_array[] = number_format($row["Bultos"], 2, ',', '.');
                $sub_array[] = number_format($row["Paquetes"], 2, ',', '.');
                $sub_array[] = number_format(0, 2, ',', '.');
                $sub_array[] = number_format(0, 2, ',', '.');
            }else{
                $sub_array[] = $row["codprod"];
                $sub_array[] = $row["Descrip"];
                $sub_array[] = $row["marca"];
                $sub_array[] = number_format(0, 2, ',', '.');
                $sub_array[] = number_format(0, 2, ',', '.');
                $sub_array[] = number_format(0, 2, ',', '.');
                $sub_array[] = number_format(0, 2, ',', '.');
                $sub_array[] = number_format($row["Bultos"], 2, ',', '.');
                $sub_array[] = number_format($row["Paquetes"], 2, ',', '.');
            }
        }
                
        
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
}