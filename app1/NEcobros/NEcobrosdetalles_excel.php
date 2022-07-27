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
require_once("NEcobros_modelo.php");

//INSTANCIAMOS EL MODELO
$facturas = new NExcobrar();

$vendedor=$_GET["vendedor"];
$dataop = $_GET['data'];

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


 $sheet->setCellValue('A1', 'Notas de Entregas Pendientes por Cobrar Detalles');


$sheet->setCellValue('A5', 'fecha del reporte:  '. date('d-m-Y'));

$spreadsheet->getActiveSheet()->mergeCells('A1:M1');

/** TITULO DE LA TABLA **/
$sheet->setCellValue('A7', utf8_decode(Strings::titleFromJson('tipo_transaccion')))
    ->setCellValue('B7', Strings::titleFromJson('numerod'))
    ->setCellValue('C7', Strings::titleFromJson('codclie'))
    ->setCellValue('D7', Strings::titleFromJson('cliente'))
    ->setCellValue('E7', Strings::titleFromJson('fecha_emision'))
    ->setCellValue('F7', Strings::titleFromJson('dias_transcurridos_hoy'));
    if ($dataop =='5') {
        $sheet->setCellValue('G7', Strings::titleFromJson('0_a_7'))
        ->setCellValue('H7', Strings::titleFromJson('8_a_15'))
        ->setCellValue('I7', Strings::titleFromJson('16_a_40'))
        ->setCellValue('J7', Strings::titleFromJson('mas_40'))
        ->setCellValue('K7', Strings::titleFromJson('saldo_pendiente'))
        ->setCellValue('L7', Strings::titleFromJson('ruta'))
        ->setCellValue('M7', Strings::titleFromJson('supervisor'));
        }
    $sheet->setCellValue('G7', Strings::titleFromJson('saldo_pendiente'))
        ->setCellValue('H7', Strings::titleFromJson('ruta'))
        ->setCellValue('I7', Strings::titleFromJson('supervisor'));  
        

$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);


//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A7:M7');

$query = $facturas->getdetallesNEporcobrar($vendedor, $dataop);

$row = 8;
foreach ($query as $i) {

     

        $total = number_format($i["SaldoPend"], 2, ',', '.');
        if ($dataop=='5') {
            $t07 = number_format($i["Total_0_a_7_Dias"], 2, ',', '.');
            $t815 = number_format($i["Total_8_a_15_Dias"], 2, ',', '.');
            $t1640 = number_format($i["Total_16_a_40_Dias"], 2, ',', '.');
            $tm40 = number_format($i["Total_Mayor_a_40_Dias"], 2, ',', '.');
        }
        

        $fecha_E = date('d/m/Y', strtotime($i["FechaEmi"]));

    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A' . $row, $i['TipoOpe']);
    $sheet->setCellValue('B' . $row, $i['NroDoc']);
    $sheet->setCellValue('C' . $row, $i['CodClie']);
    $sheet->setCellValue('D' . $row, $i['Cliente']);
    $sheet->setCellValue('E' . $row, $fecha_E);
    $sheet->setCellValue('F' . $row, $i['DiasTransHoy']);
    if ($dataop=='5') {
    $sheet->setCellValue('G' . $row, $t07);
    $sheet->setCellValue('H' . $row, $t815);
    $sheet->setCellValue('I' . $row, $t1640);
    $sheet->setCellValue('J' . $row, $tm40);
    $sheet->setCellValue('K' . $row, $total);
    $sheet->setCellValue('L' . $row, $i['Ruta']);
    $sheet->setCellValue('M' . $row, $i['Supervisor']);
    }else {
        $sheet->setCellValue('G' . $row, $total);
        $sheet->setCellValue('H' . $row, $i['Ruta']);
        $sheet->setCellValue('I' . $row, $i['Supervisor']);
        }

   

    /** centrar las celdas **/
    $spreadsheet->getActiveSheet()->getStyle('A'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('B'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('C'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('D'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('E'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('F'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('G'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('H'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('I'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
   
    $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

header('Content-Disposition: attachment;filename="Notas de Entrega Pendientes por Cobrar Detalles.xlsx"');

header('Cache-Control: max-age=0');


$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');
