<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require_once ( PATH_LIBRARY.'jpgraph4.3.4/src/jpgraph.php' );
require_once ( PATH_LIBRARY.'jpgraph4.3.4/src/jpgraph_bar.php' );
require_once ( PATH_LIBRARY.'jpgraph4.3.4/src/jpgraph_line.php' );

require (PATH_VENDOR.'autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Chart\Layout;

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("indicadoresdespacho_modelo.php");

//INSTANCIAMOS EL MODELO
$indicadores = new InidicadoresDespachos();

$tipoPeriodo = $_GET['tipoPeriodo'];
$periodo   = $_GET['periodo'];
$chofer_id = $_GET['chofer'];
$causa = $_GET['causa'];

switch($tipoPeriodo) {
    case "Anual":
        $fechai = $periodo."-01-01";
        $fechaf = $periodo."-12-31";
        break;
    case "Mensual":
        $fechai = $periodo."-01";
        $fechaf = date("Y-m-t", strtotime($periodo));
        break;
}

$i = 0;
//funcion recursiva creada para reporte Excel que evalua los numeros > 0
// y asigna la letra desde la A....hasta la Z y AA, AB, AC.....AZ
function getExcelCol($num, $letra_temp = false) {
    $numero = $num % 26;
    $letra = chr(65 + $numero);
    $num2 = intval($num / 26);
    if(!$letra_temp)
        $GLOBALS['i'] = $GLOBALS['i'] +1;

    if ($num2 > 0) {
        return getExcelCol($num2 - 1, true) . $letra;
    } else {
        return $letra;
    }
}

$cant_ordenes_despacho_max = 21;
$cant_fact_sinliquidar_max = 26;
$ancho_tabla_max = 19;
$row = 0;

/************************************* */
/** CONFIGURAMOS EL TIPO DE DOCUMENTO **/
/************************************* */
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(21);
foreach(range('B','U') as $columnID) {
    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setWidth(10);
}


/********************** */
/** SE INSERTA EL LOGO **/
/********************** */
$gdImage = imagecreatefrompng(PATH_LIBRARY.'/build/images/logo.png');
$objDrawing = new MemoryDrawing();
$objDrawing->setName('Sample image');
$objDrawing->setDescription('TEST');
$objDrawing->setImageResource($gdImage);
$objDrawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
$objDrawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
$objDrawing->setHeight(118);
$objDrawing->setWidth(118);
$objDrawing->setCoordinates('B3');
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());


/********************** */
/** TITULO DEL REPORTE **/
/********************** */
$row = 2;
$spreadsheet->getActiveSheet()->getStyle('J3:L3')->getFont()->setSize(25);
$sheet->setCellValue('K3', 'RECHAZO DE LOS CLIENTES');
$sheet->setCellValue('R2', 'Codigo: FOR-TRA-09-R0');
$sheet->setCellValue('R4', 'Fecha: 25/08/14');
$spreadsheet->getActiveSheet()->getStyle('A'.($row+=0).':T'.($row))->applyFromArray(array('borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('A'.($row+=1).':T'.($row))->applyFromArray(array('borders' => array('left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('A'.($row+=1).':T'.($row))->applyFromArray(array('borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));



/************************************************************************************************ */
/** INICIO DE LOS PROCESOS DE OBTENCION DE DATOS Y PROCESAMIENTO PARA UTILIZARLOS POSTERIORMENTE **/
/************************************************************************************************ */

$query = $indicadores->get_causasrechazo_por_chofer($fechai, $fechaf, $chofer_id, $causa);

if($chofer_id!="-") {
    $chofer = Choferes::getByDni($chofer_id);
    $chofer = (count($chofer) > 0) ? $chofer[0]['cedula'].' - '.$chofer[0]['descripcion'] : "";
} else {
    $chofer = "Todos los Choferes";
}
$formato_fecha = $tipoPeriodo=="Anual" ? 'm-Y' : 'd-m-Y';
$ordenes_despacho_string = "";
$totaldespacho = Array();
$totalendespacho = 0;
$total_ped_devueltos = 0;
$fecha_entrega = Array();
$nombre_mes = Array();
$cant_documentos = Array();
$porc = Array();
$ordenes_despacho = Array();
$correlativo = Array();
$observacion = Array();


//almacenamos el total de despachos agrupado
foreach ($query as $item){
    if($item['fecha_entre']==null or Dates::check_in_range($fechai, $fechaf, $item['fecha_entre'])) {
        $fecha_entreg = $item['fecha_entre'] != null ? date_format(date_create($item['fecha_entre']), $formato_fecha) : "sin fecha de entrega";

        //consultamos si la fecha a consultar existe en el array totaldespacho
        if(count($totaldespacho)>0 and in_array($fecha_entreg, array_keys($totaldespacho)))
            $totaldespacho[$fecha_entreg] += intval($item['cant_documentos']);
        //si no existe, solo inserta un nuevo registro al array
        else $totaldespacho[$fecha_entreg] = intval($item['cant_documentos']);
    }
}

foreach ($query as $key => $item)
{
    /** obtener los despachos realizados para imprimirlos en un string ordenado al final **/
    if(count($ordenes_despacho)>0 and $item['correlativo'] == $ordenes_despacho[count($ordenes_despacho)-1]["correlativo"]) {
        $ordenes_despacho[count($ordenes_despacho)-1]['cant_documentos'] += intval($item['cant_documentos']);
    } else {
        $ordenes_despacho[] = array(
            "correlativo"     => $item['correlativo'],
            "cant_documentos" => $item['cant_documentos']
        );
    }

    /** causas de rechazo **/
    if(($item['tipo_pago'] =='N/C' or $item['tipo_pago'] =='N/C/P') and $key>=0 )
    {
        //consultamos si la de la iteracion actual tiene fecha igual a la insertada en la interacion anterior
        if(count($fecha_entrega)>0 and  ($item['fecha_entre'] != null
                and date_format(date_create($item['fecha_entre']), $formato_fecha) == $fecha_entrega[count($fecha_entrega)-1]) ||
            ($item['fecha_entre']==null and "sin fecha de entrega"==$fecha_entrega[count($fecha_entrega)-1])
        ) {
            $porcentaje = ($item['cant_documentos'] / $totaldespacho[$fecha_entrega[count($fecha_entrega)-1]]) * 100;
            $cant_documentos[count($cant_documentos)-1] += intval($item['cant_documentos']);
            $porc[count($porc)-1] += $porcentaje;
            $correlativo[count($correlativo)-1] .= (", " . $item['correlativo']);

            $arr = array_map(function ($val) { return $val['tipo']; }, $observacion[$fecha_entrega[count($fecha_entrega)-1]]);
            //verifica si existe la observacion
            if (!in_array(strtoupper($item['observacion']), $arr)) {
                # no existe, le agrega en una nueva posicion
                $observacion[$fecha_entrega[count($fecha_entrega)-1]][] = Array(
                    "tipo" => strtoupper($item['observacion']),
                    "cant" => intval($item['cant_documentos']),
                    "color" => Array("id" => $item['color_id'], "hex" => $item['color'])
                );
            } else {
                # si existe, le suma la cantidad de documentos
                $pos = array_search($item['observacion'], $arr);
                $observacion[$fecha_entrega[count($fecha_entrega)-1]][$pos]['cant'] += intval($item['cant_documentos']);
            }

        }
        //si no es igual, solo inserta un nuevo registro al array
        elseif($item['fecha_entre']==null or Dates::check_in_range($fechai, $fechaf, $item['fecha_entre'])){
            $fecha_ent = ($item['fecha_entre'] != null and strlen($item['fecha_entre'])>0)
                ? date_format(date_create($item['fecha_entre']), $formato_fecha) : "sin fecha de entrega";
            $nombreMes = ($item['fecha_entre'] != null and strlen($item['fecha_entre'])>0)
                ? Dates::month_name(date_format(date_create($item['fecha_entre']), 'm'), true) : "sin f. entreg.";
            $porcentaje = ($item['cant_documentos'] / $totaldespacho[$fecha_ent]) * 100;

            $fecha_entrega[] = $fecha_ent;
            $nombre_mes[] = $nombreMes;
            $cant_documentos[] = intval($item['cant_documentos']);
            $porc[] = $porcentaje;
            $correlativo[] = $item['correlativo'];
            $observacion[$fecha_ent][] = Array(
                "tipo"  => strtoupper($item['observacion']),
                "cant"  => intval($item['cant_documentos']),
                "color" => Array("id" => $item['color_id'], "hex" => $item['color'])
            );
        }
    }
}

/** los despachos realizados obtenidos se agregan a un string **/
foreach ($ordenes_despacho as $arr){
    $ordenes_despacho_string .= ($arr['correlativo'] . "(" . Strings::addCero($arr['cant_documentos']) . "), ");
}

/** calcular los pedidos devueltos **/
foreach ($cant_documentos as $arr){
    $total_ped_devueltos += intval($arr);
}

/** calcular el total despachos **/
foreach ($totaldespacho as $key => $arr){
    $totalendespacho += intval($arr);
}


/****************************************************************************** */
/** EVALUAMOS SI LA DATA PROCESADA ES INFERIOR A 42 PARA EVITAR DESBORDAMIENTO **/
/****************************************************************************** */
if(count($cant_documentos)>42) {
    echo "<script>
                alert('Desbordamiento de informacion. Disminuya el rango de fecha para mejor visualizacion');
                window.close();
          </script>";
}


/****************************** */
/**      DATOS DEL REPORTE     **/
/****************************** */
$row+=2;
$sheet->setCellValue('A'.($row), 'CHOFER:'); $sheet->setCellValue('B'.($row), $chofer);
$sheet->setCellValue('N'.($row), 'DESDE: ' . date_format(date_create($fechai), "d-m-Y"));
$sheet->setCellValue('R'.($row), 'HASTA: ' . date_format(date_create($fechaf), "d-m-Y"));
$spreadsheet->getActiveSheet()->mergeCells('N'.($row).':O'.($row));
$spreadsheet->getActiveSheet()->mergeCells('R'.($row).':S'.($row));
$spreadsheet->getActiveSheet()->getStyle('A'.($row).':T'.($row))->applyFromArray(array('borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('A'.($row+=1).':T'.($row))->applyFromArray(array('borders' => array('left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));

$sheet->setCellValue('A'.($row+=1), 'ORDENES DE DESPACHO');
$ordenes_despacho_arr = explode(",", $ordenes_despacho_string);
//como la longitud en horizontal puede sobrepasar el cuadro
//evalua la cantidad de ordenes para su procesamiento
if(count($ordenes_despacho_arr) > $cant_ordenes_despacho_max)
{
    //dividimos las ordenes de despacho, en string de (cantidad maxima de ordenes) despachos
    // y los anexa a un array para posterior utlizacion
    $temp_string = "";
    foreach($ordenes_despacho_arr as $index => $arr)
    {
        //axena cada (cantidad maxima de ordenes) o si llego al final del arr
        if( ($index>0 && ($index % $cant_ordenes_despacho_max)==0) || ($index>0 && $index==count($ordenes_despacho_arr)) ) {
            $sheet->setCellValue('B'.($row), $temp_string);
            $spreadsheet->getActiveSheet()->mergeCells('B'.($row).':T'.($row));
            $spreadsheet->getActiveSheet()->getStyle('A'.($row).':T'.($row))->applyFromArray(array('borders' => array('left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));
            $temp_string = ($arr . ", ");
            $row+=1;
        } else {
            //concadena cada despacho
            $temp_string .= ($arr . ", ");
        }
    }
}
else {
    //sino no es necesario procesar la ordenes de despachos sino imprimirlas
    $sheet->setCellValue('B'.($row), $ordenes_despacho_string);
    $spreadsheet->getActiveSheet()->mergeCells('B'.($row).':T'.($row));
    $spreadsheet->getActiveSheet()->getStyle('A'.($row).':T'.($row))->applyFromArray(array('borders' => array('left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));
    $row+=1;
}
$spreadsheet->getActiveSheet()->getStyle('A'.($row).':T'.($row))->applyFromArray(array('borders' => array('left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));

$sheet->setCellValue('A'.($row+=1), 'CAUSA DEL RECHAZO');
$sheet->setCellValue('B'.($row), $causa);
$spreadsheet->getActiveSheet()->mergeCells('B'.($row).':T'.($row));
$spreadsheet->getActiveSheet()->getStyle('A'.($row).':T'.($row))->applyFromArray(array('borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));



/************************************* */
/** CONTENIDO DE LA TABLA DE LA TABLA **/
/************************************* */

$titulo_tabla = [
    'fecha_devolucion' => 'F. Devolución',
    'ped_devueltos'    => 'Devoluciones',
    'rechazo'          => '% Rechazos',
    'orden_despacho'   => 'Orden(es) Despacho',
];

//estilo de la cabecera de la tabla
$style_title = new Style();
$style_title->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D5D5F6'],), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$style_subtitle = new Style();
$style_subtitle->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'B6B6F7'],), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$row-=3;
$temp_letra = $ult_letra = "";
for ($j=0; $j<count($cant_documentos); $j++) {
    $sheet = $spreadsheet->getActiveSheet();
    //evalua con la intencion de saber si se va a hacer un salto de la
    //tabla para agregarle estilo a la cabecera de la tabla
    if (($j % $ancho_tabla_max) == 0 || $j + 1 == count($cant_documentos)) {

        if ($temp_letra != "") {
            $ult_letra = !($j + 1 == count($cant_documentos)) ? $temp_letra : getExcelCol($i, true);
            $spreadsheet->getActiveSheet()->mergeCells('A' . ($row) . ':' . $ult_letra . ($row));
            $spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A' . ($row) . ':' . $ult_letra . ($row));
        }
    }

    //esta evalua si la iteracion va a ser superior al ancho maximo para generar
    //el salgo de la tabla y agregarle los titulos
    if (($j % $ancho_tabla_max) == 0) {
        $i = 0;
        $row += 6;
        $temp_letra = getExcelCol($i);
        $sheet->setCellValue($temp_letra . ($row + 0), 'Rechazos de los Clientes');
        $sheet->setCellValue($temp_letra . ($row + 1), $titulo_tabla['fecha_devolucion']);
        $sheet->setCellValue($temp_letra . ($row + 2), $titulo_tabla['ped_devueltos']);
        $sheet->setCellValue($temp_letra . ($row + 3), $titulo_tabla['rechazo']);
        if ($tipoPeriodo != "Anual") {
            $sheet->setCellValue($temp_letra . ($row + 4), $titulo_tabla['orden_despacho']);
        }
        $spreadsheet->getActiveSheet()->duplicateStyle($style_subtitle, $temp_letra . ($row + 1));
        $spreadsheet->getActiveSheet()->duplicateStyle($style_title, $temp_letra . ($row + 2));
        $spreadsheet->getActiveSheet()->duplicateStyle($style_title, $temp_letra . ($row + 3));
        if ($tipoPeriodo != "Anual") {
            $spreadsheet->getActiveSheet()->duplicateStyle($style_title, $temp_letra . ($row + 4));
        }
    }

    $temp_letra = getExcelCol($i);
    $sheet->setCellValue($temp_letra . ($row + 1), $tipoPeriodo!="Anual" ? $fecha_entrega[$j] : $nombre_mes[$j]);
    $sheet->setCellValue($temp_letra . ($row + 2), $cant_documentos[$j]);
    $sheet->setCellValue($temp_letra . ($row + 3), Strings::rdecimal($porc[$j], 2) . ' %');
    if ($tipoPeriodo != "Anual") {
        $sheet->setCellValue($temp_letra . ($row + 4), $correlativo[$j]);
    }

    /** centrarlas las celdas **/
    $spreadsheet->getActiveSheet()->duplicateStyle($style_subtitle, $temp_letra . ($row + 1));
    $spreadsheet->getActiveSheet()->getStyle($temp_letra . ($row + 2))->applyFromArray(array('borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle($temp_letra . ($row + 3))->applyFromArray(array('borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    if ($tipoPeriodo != "Anual") {
        $spreadsheet->getActiveSheet()->getStyle($temp_letra . ($row + 4))->applyFromArray(array('borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    }
}

/************************************* */
/**      TOTALES BAJO LA TABLA        **/
/************************************* */
$row+=6;
$sheet->setCellValue('B' . ($row+0), 'Total de Pedidos en el camión:');
$sheet->setCellValue('B' . ($row+1), 'Total de Pedidos devueltos:');
$sheet->setCellValue('E' . ($row+0), $totalendespacho);
$sheet->setCellValue('E' . ($row+1), $total_ped_devueltos);
$spreadsheet->getActiveSheet()->mergeCells('B'.($row+0).':D'.($row+0));
$spreadsheet->getActiveSheet()->mergeCells('B'.($row+1).':D'.($row+1));
$spreadsheet->getActiveSheet()->getStyle('B'.($row+0).':D'.($row+0))->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('B'.($row+1).':D'.($row+1))->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('E'.($row+0))->applyFromArray(array('font' => array('bold'  => true), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_LEFT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('E'.($row+1))->applyFromArray(array('font' => array('bold'  => true), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_LEFT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));




/************************************* */
/**             GRAFICO               **/
/************************************* */
# ancho y alto de la imagen grafico
$aWidth = 1080; $aHeight = 450;
$labels = $tipoPeriodo!="Anual" ? $fecha_entrega : $nombre_mes;
$causas = CausasRechazos::todos();

# array inicializado con los valores en cero basado en la cantidad de registros de $labels
$arr_temp = array_map(function() { return 0; }, $labels);

# creamos un array con los id de colores por causa de rechazo disponibles en el rango de fecha
$index_color = [];
foreach ($observacion as $obsr) { foreach ($obsr as $value) { array_push($index_color, $value['color']['id']); }}
$index_color = array_unique($index_color); sort($index_color);

# creamos un array inicializado basandose en la dimension de index_color.
$values = array();
foreach ($index_color as $idx) {
    array_push($values, Array(
        'id'     => $idx,
        'tipo'   => $causas[$idx-1]['descripcion'],
        'values' => $arr_temp,
        'color'  => $causas[$idx-1]['color']
    ));
}

# llenamos el array con la data necesaria para ser procesada en el grafico
foreach ($values as $index => $val) {
    foreach ($observacion as $grupo_asocionacion => $obsr) {
        $tipos_observacion = array_map(function($tipo_obs) { return $tipo_obs['tipo']; }, $obsr);
        if (in_array($val['tipo'], $tipos_observacion)) {
            $idx = array_search($grupo_asocionacion, array_keys($observacion));
            $values[$index]['values'][$idx] = $obsr[array_search($val['tipo'], $tipos_observacion)]['cant'];
        }
    }
}


# obtencion del valor mas alto en base a $cant_documentos
$valorMasAlto = 0;
foreach($cant_documentos as $item)
    if ($item > $valorMasAlto) {$valorMasAlto = $item;}

# llenado de unos array con las escalas para el grafico en base al valor mas alto
$salto = $valorMasAlto < 10 ? 1 : 5;
$valuesPar = $valuesImpar = Array();
for($m=0; $m <= $valorMasAlto+$salto; $m+=$salto)
    if ($m%2==0){$valuesPar[] = $m;}
    else{$valuesImpar[] = $m;}

# agregamos los titulos de la leyenda ocultos atras del grafico
$num_temp = 0;
foreach ($values as $key => $value) {
    $num_temp = $pos = ($row + $key + 4);
    $sheet->setCellValue('C'.$pos, $value['tipo']);

    //agregamos a su vez los valores por serie
    $i = 4;
    foreach ($value['values'] as $val){
        $sheet->setCellValue(getExcelCol($i) . $pos, $val);
    }
}



// Create the graph. These two calls are always required
$graph = new Graph ( $aWidth , $aHeight , 'auto' );
$graph->SetScale("textlin");

$graph->SetMargin(50 , 30 , 40 , 100);

$graph->yaxis->SetTickPositions($valuesPar, $valuesImpar);
$graph->SetBox(false);
$graph->yaxis->title->SetFont(FF_VERDANA, FS_NORMAL);
$graph->xaxis->title->SetFont(FF_VERDANA, FS_NORMAL);
$graph->xaxis->title->Set("Fecha","left");
$graph->yaxis->title->Set("Cantidad Devoluciones","middle");
$graph->xaxis->SetTitleMargin(25);
$graph->yaxis->SetTitleMargin(35);

$graph->ygrid->SetFill(false);
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false , false);
// Setup month as labels on the X-axis
$graph->xaxis->SetTickLabels($tipoPeriodo!="Anual" ? $fecha_entrega : $nombre_mes);

// Create the bar plots desgloce de devoluciones
$bplot = array();
foreach ($values as $key => $value) {
    //creamos un nuevo Barplot
    $bplotTemp = new BarPlot($value['values']);
    $bplotTemp->value->show();
    $bplotTemp->value->SetColor("black","darkred");
    $bplotTemp->value->SetFormat('%1d');

    //buscamos y signamos el color
    $bplotTemp->SetColor($value['color']);
    $bplotTemp->SetFillColor($value['color']);

    //asignamos el nombre de la leyenda
    $bplotTemp->SetWidth(25);
    $bplotTemp->SetLegend($value['tipo']);

    //por ultimo lo agregamos a un array para su posterior plot
    $bplot[] = $bplotTemp;
}
// Create the grouped bar plot
$gbbplot = new AccBarPlot($bplot);
// ...and add it to the graPH
$graph->Add( $gbbplot );

$graph->legend->SetFrameWeight(1);
$graph->legend->SetColumns(5);
$graph->legend->Pos(0.2, 0.03);
$graph->legend->SetPos(0.5,0.99,'center','bottom');
$graph->legend->SetFont(FF_VERDANA, FS_NORMAL,9);
$graph->legend->SetColor('#4E4E4E' , '#00A78A');

$graph->title->Set('');

// Display the graph
$graph->Stroke("rechazo_clientes.png");
$gdImage = imagecreatefrompng('rechazo_clientes.png');
$objDrawing = new MemoryDrawing();
$objDrawing->setName('Sample image');
$objDrawing->setDescription('TEST');
$objDrawing->setImageResource($gdImage);
$objDrawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
$objDrawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
$objDrawing->setHeight($aHeight);
$objDrawing->setWidth($aWidth);
$objDrawing->setCoordinates('C' . ($row+=4));
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());
unlink("rechazo_clientes.png");


/************************************* */
/**     CUADRO DE AUTORIZADO POR      **/
/************************************* */
$row+=25;
$sheet->setCellValue('B'.$row, 'Aprobado por: ');
$sheet->setCellValue('K'.$row, 'C.I:');
$sheet->setCellValue('O'.$row, 'Firma: ');
$spreadsheet->getActiveSheet()->mergeCells('B'.($row).':C'.($row));
$spreadsheet->getActiveSheet()->getStyle('B'.($row).':C'.($row))->applyFromArray(array('font' => array('bold'  => true), 'borders' => array('top' => ['borderStyle' => Border::BORDER_MEDIUM], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], 'bottom' => ['borderStyle' => Border::BORDER_MEDIUM],)));
$spreadsheet->getActiveSheet()->getStyle('D'.($row).':J'.($row))->applyFromArray(array('font' => array('bold'  => true), 'borders' => array('top' => ['borderStyle' => Border::BORDER_MEDIUM], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], 'bottom' => ['borderStyle' => Border::BORDER_MEDIUM],)));
$spreadsheet->getActiveSheet()->getStyle('K'.($row).':L'.($row))->applyFromArray(array('font' => array('bold'  => true), 'borders' => array('top' => ['borderStyle' => Border::BORDER_MEDIUM], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], 'bottom' => ['borderStyle' => Border::BORDER_MEDIUM],)));
$spreadsheet->getActiveSheet()->getStyle('M'.($row).':N'.($row))->applyFromArray(array('font' => array('bold'  => true), 'borders' => array('top' => ['borderStyle' => Border::BORDER_MEDIUM], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], 'bottom' => ['borderStyle' => Border::BORDER_MEDIUM],)));
$spreadsheet->getActiveSheet()->getStyle('O'.($row).':P'.($row))->applyFromArray(array('font' => array('bold'  => true), 'borders' => array('top' => ['borderStyle' => Border::BORDER_MEDIUM], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], 'bottom' => ['borderStyle' => Border::BORDER_MEDIUM],)));
$spreadsheet->getActiveSheet()->getStyle('Q'.($row).':R'.($row))->applyFromArray(array('font' => array('bold'  => true), 'borders' => array('top' => ['borderStyle' => Border::BORDER_MEDIUM], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], 'bottom' => ['borderStyle' => Border::BORDER_MEDIUM],)));



header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="indicadores_causas_rechazo_del_'.$fechai.'_al_'.$fechaf.'.xlsx"');
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->setIncludeCharts(true);
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');