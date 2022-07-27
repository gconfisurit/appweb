<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("estadocuenta_modelo.php");

//INSTANCIAMOS EL MODELO
$cuenta = new estadocuenta();
//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_estadocuenta":


        //echo "<script>console.log('Console:  PRUEBA' );</script>";

    $datos = $cuenta->getestadocuenta( $_POST["fechai"], $_POST["fechaf"],$_POST["cliente"],$_POST["tipo"]);

    //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
    $data = Array();
    $fechai=$_POST["fechai"];
    $fechaf=$_POST["fechaf"];
    $tipodoc = $_POST["tipo"];
    $tipo='';
    $estado='';
     $saldoact = 0;
    foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();

			$fechaE = date('d/m/Y', strtotime($row["FechaE"]));
			$fechaV = date('d/m/Y', strtotime($row["FechaV"]));

            if($tipodoc == 'B'){

                    switch ($row["TipoCxc"]) {
                    case "10":
                
                    $tipo= "Factura";
                
                    break;
                    case "31":
                
                    $tipo= "Nota de Crédito";
                
                    break;
                    case "41":
            
                    $tipo= "PAG"; 
                    break;
                    case "81":
            
                    $tipo= "RET"; 
                    break;
                    default:
                
                    $tipo= "No tiene tipo Fact";
            
                    }



            } else{

                if($tipodoc == 'D'){

                     switch ($row["TipoCxc"]) {
                            case "10":
                        
                            $tipo= "Nota de Entrega";
                        
                            break;
                            case "31":
                        
                            $tipo= "Nota de Crédito";
                        
                            break;
                            case "41":
                    
                            $tipo= "PAG"; 
                            break;
                            case "81":
                    
                            $tipo= "RET"; 
                            break;
                            default:
                        
                            $tipo= "No tiene tipo Fact";
                    
                            }

                }

            }

           

        $mtotald = number_format($row["Monto"], 2, ',', '.');
         $saldoact = number_format($row["SaldoAct"], 2, ',', '.');

        $sub_array[] = $tipo;
        $sub_array[] = $row["CodClie"];
        $sub_array[] = $row["Descrip"];
        $sub_array[] = $fechaE;
        $sub_array[] = $fechaV;
        $sub_array[] = $row["NumeroD"];

        if($tipo=="PAG" or $tipo=="Nota de Crédito" or $tipo=="RET"){
            $sub_array[] = $row["NumeroN"];
        }else{
            $sub_array[] = "";
        }

         $sub_array[] = $row["Document"];

         if($tipo=="Factura" or $tipo=="Nota de Entrega"){
            
            $sub_array[] =  $mtotald;
            $sub_array[] = "";
            
        }else{

            
            $sub_array[] = "";
            $sub_array[] =  $mtotald;

            
        }

        $sub_array[] =  $saldoact;      
        
        $data[] = $sub_array;

    }

  

    //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
    $results = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data),
            'saldo' => ($saldoact), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data);

    echo json_encode($results);
    break;

}