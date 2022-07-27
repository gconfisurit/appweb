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
require_once("auditoriacomisioneskpi_modelo.php");

//INSTANCIAMOS EL MODELO
$auditoriacomisioneskpi = new Auditoriacomisioneskpi();

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$vendedor = $_GET['vendedor'];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
foreach(range('A','F') as $columnID) {
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
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(25);
$sheet->setCellValue('A1', 'Auditoría de Cambios en KPI Managers SAINT');
$sheet->setCellValue('A5', 'fecha tope:  '. date('d-m-Y'));

$spreadsheet->getActiveSheet()->mergeCells('A1:C1');

/** TITULO DE LA TABLA **/
$sheet->setCellValue('A7', Strings::titleFromJson('campo_mod'))
    ->setCellValue('B7', Strings::titleFromJson('antes'))
    ->setCellValue('C7', Strings::titleFromJson('despues'))
    ->setCellValue('D7', Strings::titleFromJson('diferencia'))
    ->setCellValue('E7', Strings::titleFromJson('usuario'))
    ->setCellValue('F7', Strings::titleFromJson('fecha_hora'));

$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);

//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A7:F7');


$query = $auditoriacomisioneskpi->getauditoriacomisioneskpi($fechai, $fechaf, $vendedor);
$row = 8;
foreach ($query as $i) {
    

    $campo = "";
    switch ($i["campo"]) {
        case 1:
          $campo = "Cobranza 0 a 7 días";
          break;
        case 2:
          $campo = "Comisión 0 a 7 días";
          break;
        case 3:
          $campo = "Cobranza 8 a 14 días";
          break;
        case 4:
          $campo = "Comisión 8 a 14 días";
          break;
        case 5:
          $campo = "Cobranza 15 a 21 días";
          break;
        case 6:
          $campo = "Comisión 15 a 21 días";
          break;
        case 7:
          $campo = "Cobranza mayor a 21 días";
          break;
        case 8:
          $campo = "Activación de Clintes";
          break;
        case 9:
          $campo = "Efectividad de Facturación (EVA)";
          break;
      }

    $fecha_h = date('d/m/Y', strtotime($i["fechah"]));
    
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A' . $row, $campo);
    $sheet->setCellValue('B' . $row, Strings::rdecimal($i["antes"]));
    $sheet->setCellValue('C' . $row, Strings::rdecimal($i["despu"]));
    $sheet->setCellValue('D' . $row, Strings::rdecimal($i["despu"]-$i["antes"], 2));
    $sheet->setCellValue('E' . $row, utf8_encode($i["descrip"]));
    $sheet->setCellValue('F' . $row, $fecha_h);

    /** centrar las celdas **/
    $spreadsheet->getActiveSheet()->getStyle('A'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('B'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('C'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('D'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('E'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('F'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    

    $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Auditoría de Cambios en KPI Managers SAINT.xlsx"');
header('Cache-Control: max-age=0');


$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');

