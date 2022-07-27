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

$formato_fecha = $tipoPeriodo=="Anual" ? 'm-Y' : 'd-m-Y';
$cant_ordenes_despacho_max = 22;
$cant_fact_sinliquidar_max = 24;
$ancho_tabla_max = 19;
$row = 0;

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

/************************************* */
/** CONFIGURAMOS EL TIPO DE DOCUMENTO **/
/************************************* */
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
foreach(range('B','U') as $columnID) {
    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setWidth(10);
}


/********************** */
/** SE INSERTA EL LOGO **/
/********************** */
$gdImage = imagecreatefrompng(PATH_LIBRARY.'build/images/logo.png');
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
$sheet->setCellValue('K3', 'OPORTUNIDAD DE DESPACHO');
$sheet->setCellValue('R2', 'Codigo: FOR-TRA-09-R0');
$sheet->setCellValue('R4', 'Fecha: 25/08/14');
$spreadsheet->getActiveSheet()->getStyle('A'.($row+=0).':T'.($row))->applyFromArray(array('borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('A'.($row+=1).':T'.($row))->applyFromArray(array('borders' => array('left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('A'.($row+=1).':T'.($row))->applyFromArray(array('borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));



/************************************************************************************************ */
/** INICIO DE LOS PROCESOS DE OBTENCION DE DATOS Y PROCESAMIENTO PARA UTILIZARLOS POSTERIORMENTE **/
/************************************************************************************************ */
$query = $indicadores->get_oportunidaddespacho_por_chofer($fechai, $fechaf, $chofer_id);
$chofer = Choferes::getByDni($chofer_id);

//inicializamos la variables
$chofer = (count($chofer) > 0) ? $chofer[0]['cedula'].' - '.$chofer[0]['descripcion'] : "";
$ordenes_despacho_string = '';
$oportunidad_promedio = 0;
$total_ped = 0;
$objetivo = 80;
$totaldoc = 0;
$fechaaevaluar = "00/00/0000";
$fecha_desp = Array();
$cant_documentos = Array();
$oportunidad = Array();
$documentos = Array();
$nombre_mes = Array();


foreach ($query as $key => $item)
{
    $fecha_entrega = $item["fecha_entre"] ?? date('Y-m-d');
    $tiempo_entrega_estimado = intval($item['tiempo_estimado']);

    $tiempo = strtotime($fecha_entrega)-strtotime($item["fecha_desp"]);
    $tiempo_entrega = intval($tiempo/60/60/24);

    $cal_oportunidad = ($tiempo_entrega <= $tiempo_entrega_estimado) ? 100 : ($tiempo_entrega_estimado/$tiempo_entrega)*100;
    $oportunidad_promedio += $cal_oportunidad;

    /** oportunidad despachos **/
    if($item['fecha_desp'] != null) {
        //almacenamos el total de documentos para calcular la oportunidad posteriormente
        if (!Dates::check_in_range($fechaaevaluar, $fechaaevaluar, $item['fecha_desp'])) {
            $fechaaevaluar = $item['fecha_desp'];
            $totaldoc = Functions::searchQuantityDocumentsByDates($query, "fecha_desp", $fechaaevaluar, $formato_fecha);
        }

        //consultamos si la de la iteracion actual tiene fecha igual a la insertada en la interacion anterior
        if(count($fecha_desp)>0 and date_format(date_create($item['fecha_desp']), $formato_fecha) == $fecha_desp[count($fecha_desp)-1])
        {
            $cant_documentos[count($cant_documentos)-1] += 1;
            $oportunidad[count($oportunidad)-1] += floatval($cal_oportunidad/$totaldoc);
            $documentos[count($documentos)-1] .= (", " . $item['numerod']);
        }
        //si no es igual, solo inserta un nuevo registro al array
        else {
            $fecha_desp[] = date_format(date_create($item['fecha_desp']), $formato_fecha);
            $cant_documentos[] = 1;
            $oportunidad[] = floatval($totaldoc>1 ? $cal_oportunidad/$totaldoc : $oportunidad);
            $documentos[] = $item['numerod'];
            $nombre_mes[] = Dates::month_name(date_format(date_create($item['fecha_desp']), 'm'), true);
        }
    }
}

/** calculamos el porcentaje de oportunidad de despacho promedio **/
$oportunidad_promedio = count($oportunidad)>0 ? ($oportunidad_promedio/count($oportunidad)) : $oportunidad_promedio=0;


/** calcular la cantidad de documentos **/
foreach ($cant_documentos as $arr)
    $total_ped += intval($arr);

/** concatenar la cantidad de documentos **/
foreach ($documentos as $arr)
    $ordenes_despacho_string .= (", " . $arr);





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

$sheet->setCellValue('A'.($row+=1), 'DOCUMENTOS');
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
            $sheet->setCellValue('C'.($row), $temp_string);
            $spreadsheet->getActiveSheet()->mergeCells('C'.($row).':T'.($row));
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
    $sheet->setCellValue('C'.($row), $ordenes_despacho_string);
    $spreadsheet->getActiveSheet()->mergeCells('C'.($row).':T'.($row));
    $spreadsheet->getActiveSheet()->getStyle('A'.($row).':T'.($row))->applyFromArray(array('borders' => array('left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));
    $row+=1;
}
$spreadsheet->getActiveSheet()->getStyle('A'.($row).':T'.($row))->applyFromArray(array('borders' => array('left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));

$spreadsheet->getActiveSheet()->getStyle('A'.($row).':T'.($row))->applyFromArray(array('borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));



/************************************* */
/** CONTENIDO DE LA TABLA DE LA TABLA **/
/************************************* */

$titulo_tabla = [
    'fecha_despacho'  => 'Fecha Despacho',
    'cant_documentos' => 'Cantidad Documentos',
    'oportunidad'     => '% Oportunidad'
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
    if( ( $j % $ancho_tabla_max )==0 || $j+1==count($cant_documentos) ) {

        if($temp_letra!="") {
            $ult_letra = !($j+1==count($cant_documentos)) ? $temp_letra : getExcelCol($i, true);
            $spreadsheet->getActiveSheet()->mergeCells('A'.($row).':'. $ult_letra . ($row));
            $spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A'.($row).':'. $ult_letra . ($row));
        }
    }

    //esta evalua si la iteracion va a ser superior al ancho maximo para generar
    //el salgo de la tabla y agregarle los titulos
    if( ( $j % $ancho_tabla_max )==0 ) {
        $i = 0;
        $row += 6;
        $temp_letra = getExcelCol($i);
        $sheet->setCellValue($temp_letra . ($row+0), 'Oportunidad de despachos');
        $sheet->setCellValue($temp_letra . ($row+1), $titulo_tabla['fecha_despacho']);
        $sheet->setCellValue($temp_letra . ($row+2), $titulo_tabla['cant_documentos']);
        $sheet->setCellValue($temp_letra . ($row+3), $titulo_tabla['oportunidad']);
        $spreadsheet->getActiveSheet()->duplicateStyle($style_subtitle, $temp_letra . ($row+1));
        $spreadsheet->getActiveSheet()->duplicateStyle($style_title, $temp_letra . ($row+2));
        $spreadsheet->getActiveSheet()->duplicateStyle($style_title, $temp_letra . ($row+3));
    }

    $temp_letra = getExcelCol($i);
    $sheet->setCellValue($temp_letra . ($row+1), $tipoPeriodo!="Anual" ? $fecha_entrega[$j] : $nombre_mes[$j]);
    $sheet->setCellValue($temp_letra . ($row+2), $cant_documentos[$j]);
    $sheet->setCellValue($temp_letra . ($row+3), Strings::rdecimal($oportunidad[$j], 2) . ' %');

    /** centrarlas las celdas **/
    $spreadsheet->getActiveSheet()->duplicateStyle($style_subtitle, $temp_letra . ($row+1));
    $spreadsheet->getActiveSheet()->getStyle($temp_letra . ($row+2))->applyFromArray(array('borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle($temp_letra . ($row+3))->applyFromArray(array('borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
}



/************************************* */
/**      TOTALES BAJO LA TABLA        **/
/************************************* */
$row+=6;
$sheet->setCellValue('B' . ($row+0), 'Total de Pedidos:');
$sheet->setCellValue('E' . ($row+0), $total_ped);
$spreadsheet->getActiveSheet()->mergeCells('B'.($row+0).':D'.($row+0));
$spreadsheet->getActiveSheet()->getStyle('B'.($row+0).':D'.($row+0))->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('E'.($row+0))->applyFromArray(array('font' => array('bold'  => true), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_LEFT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));



/************************************* */
/**             GRAFICO               **/
/************************************* */
$aWidth = 1080; $aHeight = 450;

//el objetivo es una linea de objetivo de oportunidad
$objetivo = array_map(function() { return 80; }, $oportunidad);

//para este reporte el valor mas alto es 100% correspondiente a maximo porcentaje de oportunidad
$valorMasAlto = 100;

$valuesPar = $valuesImpar = Array();
for($m=0; $m <= $valorMasAlto; $m+=5)
    if ($m%2==0){$valuesPar[] = $m;}
    else{$valuesImpar[] = $m;}

// Create the graph. These two calls are always required
$graph = new Graph ( $aWidth , $aHeight , 'auto' );
$graph->SetScale("textlin");

$graph->SetMargin(50 , 30 , 40 , 100);

$graph->yaxis->SetTickPositions($valuesPar, $valuesImpar);
$graph->yaxis->scale->SetGrace(100-$objetivo[0]);
$graph->SetBox(false);
$graph->yaxis->title->SetFont(FF_VERDANA, FS_NORMAL);
$graph->xaxis->title->SetFont(FF_VERDANA, FS_NORMAL);
$graph->xaxis->title->Set("Fecha","left");
$graph->yaxis->title->Set("Porcentaje(%) de Oportunidad de Despachos","middle");
$graph->xaxis->SetTitleMargin(25);
$graph->yaxis->SetTitleMargin(35);

$graph->ygrid->SetFill(false);
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false , false);
// Setup month as labels on the X-axis
$graph->xaxis->SetTickLabels($tipoPeriodo!="Anual" ? $fecha_entrega : $nombre_mes);

// Create the bar plots
$b1plot = new BarPlot($oportunidad);
$b1plot->value->show();
$lplot = new LinePlot($objetivo);

// ...and add it to the graPH
$graph->Add($b1plot);
$graph->Add($lplot);

$b1plot->value->Show();
$b1plot->value->SetColor("black","darkred");
$b1plot->value->SetFormat('%1d%%');
$b1plot->SetColor("white");
$b1plot->SetFillGradient("#000066" , "white" , GRAD_LEFT_REFLECTION);
$b1plot->SetWidth(25);
$b1plot->SetLegend("% de Oportunidad");

$lplot->SetBarCenter();
$lplot->SetColor("red");
$lplot->SetLegend("Objetivo (".$objetivo[0]."%)");
$lplot->mark->SetWidth(15);
$lplot->mark->setColor("red");
$lplot->mark->setFillColor("red");

$graph->legend->SetFrameWeight(1);
$graph->legend->SetColumns(6);
$graph->legend->Pos(0.2, 0.03);
$graph->legend->SetPos(0.5,0.95,'center','bottom');
$graph->legend->SetColor('#4E4E4E' , '#00A78A');

$graph->title->Set('');

// Display the graph
$graph->Stroke("oportunidad_despacho.png");
$gdImage = imagecreatefrompng('oportunidad_despacho.png');
$objDrawing = new MemoryDrawing();
$objDrawing->setName('Sample image');
$objDrawing->setDescription('TEST');
$objDrawing->setImageResource($gdImage);
$objDrawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
$objDrawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
$objDrawing->setHeight($aHeight);
$objDrawing->setWidth($aWidth);
$objDrawing->setCoordinates('C' . ($row+=5));
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());
unlink("oportunidad_despacho.png");


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
header('Content-Disposition: attachment;filename="indicadores_entregas_efectivas_del_'.$fechai.'_al_'.$fechaf.'.xlsx"');
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');

?>