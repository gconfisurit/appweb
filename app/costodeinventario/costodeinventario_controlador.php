<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("costodeinventario_modelo.php");

//INSTANCIAMOS EL MODELO
$costo = new CostodeInventario();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_costoseinventario":

        //obtenemos la marca seleccionada enviada por post
        $marca = $_POST['marca'];

        //verificamos si existe al menos 1 deposito selecionado
        //y se crea el array.
        if(isset($_POST['depo'])){
            $numero = $_POST['depo'];
        } else {
            $numero = array();
        }

        //se contruye un string para listar los depositvos seleccionados
        //en caso que no haya ninguno, sera vacio
        $edv = "";
        if(count($numero)>0) {
            foreach ($numero AS $i) {
                $edv .= "'" . $i . "',";
            }
        }

        //realiza la consulta con marca y almacenes
        $datos = $costo->getCostosdEinventario($edv, $marca);

        //inicializamos los acumuladores
        $factor=0;
        $costosd = 0;
        $costos_pd = 0;
        $costos = 0;
        $costos_p = 0;
        $precios = 0;
        $bultos = 0;
        $paquetes = 0;
        $total_costo_bultos = 0;
        $total_costo_paquetes = 0;
        $total_costo_bultosd = 0;
        $costototalbs=$costototal=$total_costo_paquetesd = 0;
        $total_tara = 0;

        //DECLARAMOS ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        $totales = Array();

        foreach ($datos as $row) {

            if ($row['display'] == 0) {
                $cdisplay = 0;
            } else {
                $cdisplay = $row['costo'] / $row['display'];
            }

            $factor=$row['factor'];

            //creamos un array para almacenar los datos procesados
            $sub_array = array();
            $sub_array['codprod'] = $row["codprod"];
            $sub_array['descrip'] = $row["descrip"];
            $sub_array['marca'] = $row["marca"];
            $sub_array['costo'] = Strings::rdecimal($row['costo'],2);
            $sub_array['cdisplay'] = Strings::rdecimal($cdisplay,2);
            $sub_array['costod'] = Strings::rdecimal($row['costo']/$factor,2);
            $sub_array['cdisplayd'] = Strings::rdecimal($cdisplay/$factor,2);
            $sub_array['precio'] = Strings::rdecimal($row['precio'],2);
            $sub_array['bultos'] = Strings::rdecimal($row['bultos'],2);
            $sub_array['paquetes'] = Strings::rdecimal($row['paquetes'],2);
            $sub_array['costoxbulto'] = Strings::rdecimal($row['costo'] * $row['bultos'],2);
            $sub_array['cdisplayxpaquetes'] = Strings::rdecimal($cdisplay * $row['paquetes'],2);
            $sub_array['costoxbultod'] = Strings::rdecimal(($row['costo'] /$factor )* $row['bultos'],2);
            $sub_array['cdisplayxpaquetesd'] = Strings::rdecimal(($cdisplay /$factor)* $row['paquetes'],2);
            $sub_array['tara'] = Strings::rdecimal($row['tara'],2);
            $sub_array['costototalbs'] = Strings::rdecimal(  ($row['costo'] * $row['bultos']) + ($cdisplay * $row['paquetes'])  ,2);
            $sub_array['costototal'] = Strings::rdecimal(  (($row['costo'] /$factor )* $row['bultos']) + (($cdisplay /$factor)* $row['paquetes'])  ,2);

            //ACUMULAMOS LOS TOTALES
            $costos += $row['costo'];
            $costos_p += $cdisplay;
            $costosd += $row['costo']/$factor;
            $costos_pd += $cdisplay/$factor;
            $precios += $row['precio'];
            $bultos += $row['bultos'];
            $paquetes += $row['paquetes'];
            $total_costo_bultos += ($row['costo'] * $row['bultos']);
            $total_costo_paquetes += ($cdisplay * $row['paquetes']);
            $total_costo_bultosd += (($row['costo'] /$factor )* $row['bultos']);
            $total_costo_paquetesd += (($cdisplay /$factor)* $row['paquetes']);
            $total_tara += $row['tara'];
            $costototalbs += (($row['costo'] * $row['bultos']) + ($cdisplay * $row['paquetes']));
            $costototal += (($row['costo'] /$factor )* $row['bultos']) + (($cdisplay /$factor)* $row['paquetes']);

            //AGREGAMOS AL ARRAY DE CONTENIDO DE LA TABLA
            $data[] = $sub_array;
        }
        //creamos un array para almacenar los datos acumulados
        $sub_array1 = array();
        $sub_array1['costos'] = Strings::rdecimal($costos,2);
        $sub_array1['costos_p'] = Strings::rdecimal($costos_p,2);
        $sub_array1['costosd'] = Strings::rdecimal($costosd,2);
        $sub_array1['costos_pd'] = Strings::rdecimal($costos_pd,2);
        $sub_array1['precios'] = Strings::rdecimal($precios,2);
        $sub_array1['bultos'] = Strings::rdecimal($bultos,2);
        $sub_array1['paquetes'] = Strings::rdecimal($paquetes,2);
        $sub_array1['total_costo_bultos'] = Strings::rdecimal($total_costo_bultos,2);
        $sub_array1['total_costo_paquetes'] = Strings::rdecimal($total_costo_paquetes,2);
        $sub_array1['total_costo_bultosd'] = Strings::rdecimal($total_costo_bultosd,2);
        $sub_array1['total_costo_paquetesd'] = Strings::rdecimal($total_costo_paquetesd,2);
        $sub_array1['total_tara'] = Strings::rdecimal($total_tara,2);
         $sub_array1['total_costototalbs'] = Strings::rdecimal($costototalbs,2);
        $sub_array1['total_costototal'] = Strings::rdecimal($costototal,2);
        $sub_array1['cantidad_registros'] = count($datos);

        //al terminar, se almacena en una variable de salida el array.
        $output['contenido_tabla'] = $data;
        //de igual forma, se almacena en una variable de salida el array de totales.
        $output['totales_tabla'] = $sub_array1;

        echo json_encode($output);
        break;

    case "listar_marcas":

        $output["lista_marcas"] = Marcas::todos();

        echo json_encode($output);
        break;

    case "listar_depositos":

        $output['lista_depositos'] = Almacen::todos();

        echo json_encode($output);
        break;
}

?>
