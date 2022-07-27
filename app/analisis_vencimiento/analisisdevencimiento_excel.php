<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
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
require_once("analisisdevencimiento_modelo.php");

//INSTANCIAMOS EL MODELO
$analisis = new analisisdevencimiento();

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$proveedor = $_GET['proveedor'];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
foreach(range('A','G') as $columnID) {
    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
}

// Logo
$gdImage = imagecreatefrompng(PATH_LIBRARY.'build/images/logo.png');
$objDrawing = new MemoryDrawing();
$objDrawing->setName('Sample image');
$objDrawing->setDescription('TEST');
$objDrawing->setImageResource($gdImage);
$objDrawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
$objDrawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
$objDrawing->setHeight(108);
$objDrawing->setWidth(128);
$objDrawing->setCoordinates('E1');
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());

/** DATOS DEL REPORTE **/
$spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFont()->setSize(25);
$sheet->setCellValue('A1', 'REPORTE DE ANALISIS DE VENCIMIENTO PROVEEDORES');
$sheet->setCellValue('A5', 'fecha tope:  '. date(FORMAT_DATE, strtotime($fechaf)));

$spreadsheet->getActiveSheet()->mergeCells('A1:C1');

/** TITULO DE LA TABLA **/
$sheet->setCellValue('A7', utf8_decode(Strings::titleFromJson('codprov')))
    ->setCellValue('B7', Strings::titleFromJson('razon_social'))
    ->setCellValue('C7', Strings::titleFromJson('numerod'))
    ->setCellValue('D7', Strings::titleFromJson('fecha_documento'))
    ->setCellValue('E7', Strings::titleFromJson('fecha_vencimiento'))
    ->setCellValue('F7', Strings::titleFromJson('dias_transcurridos'))
    ->setCellValue('G7', Strings::titleFromJson('monto'));

$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);


//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A7:G7');

$query = $analisis->getanalisisdevencimiento( $fechai, $fechaf, $proveedor);

$dias = $analisis->dias_transcurridos( $fechai, $fechaf);
$row = 8;
foreach ($query as $i) {
    $Montonew = number_format($i["Monto"], 2, ',', '.');
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A' . $row, $i['CodProv']);
    $sheet->setCellValue('B' . $row, utf8_encode($i['Descrip']));
    $sheet->setCellValue('C' . $row, $i['NumeroD']);
    $sheet->setCellValue('D' . $row, date(FORMAT_DATE, strtotime($i['FechaE'])));
    $sheet->setCellValue('E' . $row, date(FORMAT_DATE, strtotime($i['FechaEV'])));
    $sheet->setCellValue('F' . $row, utf8_encode($dias));
    $sheet->setCellValue('G' . $row, $Montonew);

    /** centrar las celdas **/
    $spreadsheet->getActiveSheet()->getStyle('A'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('B'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('C'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('D'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('E'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('F'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('G'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Analisis_de_Vencimiento_del'.$fechai.' hasta '.$fechaf.'.xlsx"');
header('Cache-Control: max-age=0');


$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');

/*
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

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$proveedor = $_GET['proveedor'];

$pAux = '';


//creamos la cabecera de la tabla
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);

// Logo
$gdImage = imagecreatefrompng(PATH_LIBRARY.'build/images/logo.png');
$objDrawing = new MemoryDrawing();
$objDrawing->setName('Sample image');
$objDrawing->setDescription('TEST');
$objDrawing->setImageResource($gdImage);
$objDrawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
$objDrawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
$objDrawing->setHeight(108);
$objDrawing->setWidth(128);
$objDrawing->setCoordinates('G1');
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());

/** DATOS DEL REPORTE 
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(25);
$sheet->setCellValue('A1', 'REPORTE DE ANALISIS DE VENCIMIENTO PROVEEDORES');
//$sheet->setCellValue('A3', 'del: '. date(FORMAT_DATE, strtotime($fechai)));
//$sheet->setCellValue('A5', 'al:  '. date(FORMAT_DATE, strtotime($fechaf)));


$spreadsheet->getActiveSheet()->mergeCells('A1:E1');

/** TITULO DE LA TABLA 
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('codprov'));
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('razon_social'));
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('numerod'));
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('fecha_documento'));
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('fecha_vencimiento'));
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('dias_transcurridos'));
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('monto'));

//obtenemos el ultimo valor de la celda y la guardamos en una variable auxiliar
$aux = $i-1;
//se itera la cantidad de celdas almacenadas en la variable axiliar y se situan AutoSize
for($n=0; $n <= $aux; $n++){ $spreadsheet->getActiveSheet()->getColumnDimension(getExcelCol($n))->setAutoSize(true); }


$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);

//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A7:'.getExcelCol($aux).'7');


//a partir de aqui comienza a llenarse la tabla

$datos = $analisis->getanalisisdevencimiento( $_POST["fechai"], $_POST["fechaf"], $_POST["proveedor"]);

$dias = $analisis->dias_transcurridos( $_POST["fechai"], $_POST["fechaf"]);

//DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
$data = Array();
$num = count($datos);
$X = 8;
foreach ($datos as $row) {
    $i = 0;
        //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
    $sub_array = array();

    $fecha_E = date('d/m/Y', strtotime($row["FechaE"]));
    $fecha_V = date('d/m/Y', strtotime($row["FechaV"]));
    $Montonew = number_format($row["Monto"], 2, ',', '.');
    
    $sub_array[] = $row["CodProv"];
    $sub_array[] = $row["Descrip"];
    $sub_array[] = $row["NumeroD"];
    $sub_array[] = $fecha_E;
    $sub_array[] = $fecha_V;
    $sub_array[] = $dias;
    $sub_array[] = $Montonew;

    $data[] = $sub_array;

    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue(getExcelCol($i, true) . $X, $sub_array['CodProv']);
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$X)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $sheet->setCellValue(getExcelCol($i, true) . $X, $sub_array['Descrip']);
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$X)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $sheet->setCellValue(getExcelCol($i, true) . $X, $sub_array['NumeroD']);
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$X)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $sheet->setCellValue(getExcelCol($i, true) . $X, $sub_array[$fecha_E]);
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$X)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $sheet->setCellValue(getExcelCol($i, true) . $X, $sub_array[$fecha_v]);
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$X)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $sheet->setCellValue(getExcelCol($i, true) . $X, $sub_array[$dias]);
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$roXw)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $sheet->setCellValue(getExcelCol($i, true) . $X, $sub_array[$Montonew]);
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$X)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $X++;
    
}


/* por ultimo se escribe la cantidad de productos
$sheet->setCellValue('B' . ($row + 3), 'Total de Productos:  ' . $num);*/

/*
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Analisis_de_Vencimiento' . date('d/m/Y') . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');*/
