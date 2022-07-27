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
require_once("proveedor_modelo.php");

//INSTANCIAMOS EL MODELO
$proveedores = new listarProveedores();

$orden = $_GET['orden'];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
foreach(range('A','J') as $columnID) {
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
if($orden=="Todos"){
    $sheet->setCellValue('A1', 'REPORTE DE TODOS LOS PROVEEDORES');
}elseif($orden=="Activos"){
    $sheet->setCellValue('A1', 'REPORTE DE LOS PROVEEDORES ACTIVOS');
}elseif($orden=="Inactivos"){
    $sheet->setCellValue('A1', 'REPORTE DE LOS PROVEEDORES INACTIVOS');
}
$sheet->setCellValue('A5', 'fecha tope:  '. date('d-m-Y'));

$spreadsheet->getActiveSheet()->mergeCells('A1:C1');

/** TITULO DE LA TABLA **/
$sheet->setCellValue('A7', utf8_decode(Strings::titleFromJson('codprov')))
    ->setCellValue('B7', Strings::titleFromJson('razon_social'))
    ->setCellValue('C7', Strings::titleFromJson('rif'))
    ->setCellValue('D7', Strings::titleFromJson('activo'))
    ->setCellValue('E7', Strings::titleFromJson('direc1'))
    ->setCellValue('F7', Strings::titleFromJson('direc2'))
    ->setCellValue('G7', Strings::titleFromJson('estado'))
    ->setCellValue('H7', Strings::titleFromJson('tlf'))
    ->setCellValue('I7', Strings::titleFromJson('tlf_movil'))
    ->setCellValue('J7', Strings::titleFromJson('correo_electronico'));

$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);


//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A7:J7');

$query = $proveedores->getlistaproveedores($orden);
$row = 8;
foreach ($query as $i) {
    
    if ($i['Activo'] == 1) {
        $estado = "ACTIVO";
    } else {
        if ($i['Activo'] == 0) {
            $estado = "INACTIVO";
        }
    }

    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A' . $row, $i['CodProv']);
    $sheet->setCellValue('B' . $row, utf8_encode($i['Descrip']));
    $sheet->setCellValue('C' . $row, $i['ID3']);
    $sheet->setCellValue('D' . $row, $estado);
    $sheet->setCellValue('E' . $row, $i['Direc1']);
    $sheet->setCellValue('F' . $row, $i['Direc2']);
    $sheet->setCellValue('G' . $row, utf8_encode($i['Descrip']));
    $sheet->setCellValue('H' . $row, $i['Telef']);
    $sheet->setCellValue('I' . $row, $i['Movil']);
    $sheet->setCellValue('J' . $row, utf8_encode($i['Email']));

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
    $spreadsheet->getActiveSheet()->getStyle('J'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Reporte de proveedoreS : '.$orden.'.xlsx"');
header('Cache-Control: max-age=0');


$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');