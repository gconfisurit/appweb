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
require_once("sellin_modelo.php");

//INSTANCIAMOS EL MODELO
$sellin = new sellin();

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$marca = $_GET['marca'];
$tipo = $_GET['tipo'];

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
$sheet->setCellValue('A1', 'REPORTE DE SELL IN COMPRAS');
$sheet->setCellValue('A3', 'del: '. date(FORMAT_DATE, strtotime($fechai)));
$sheet->setCellValue('A5', 'al:  '. date(FORMAT_DATE, strtotime($fechaf)));

$spreadsheet->getActiveSheet()->mergeCells('A1:C1');

/** TITULO DE LA TABLA **/
$sheet->setCellValue('A7', Strings::titleFromJson('codigo_prod'))
    ->setCellValue('B7', Strings::titleFromJson('descrip_prod'))
    ->setCellValue('C7', "Compra de Factura")
    ->setCellValue('D7', "Devolución de Factura")
    ->setCellValue('E7', "Compra de Notas de Entregas")
    ->setCellValue('F7', "Devolución de Notas de Entregas")
    ->setCellValue('G7', Strings::titleFromJson('total'))
    ->setCellValue('H7', Strings::titleFromJson('marca_prod'));

$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);

//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A7:H7');

$query =  $sellin->getsellin($fechai, $fechaf, $marca, $tipo);
$row = 8;
foreach ($query as $i) {
    $sheet = $spreadsheet->getActiveSheet();

 if($tipo=='f'){

    $sheet->setCellValue('A' . $row, $i['coditem']);
    $sheet->setCellValue('B' . $row, utf8_decode($i['producto']));
    $sheet->setCellValue('C' . $row, number_format(0, 2));
    $sheet->setCellValue('D' . $row, number_format(0, 2));
    $sheet->setCellValue('E' . $row, number_format($i['compras'], 2));
    $sheet->setCellValue('F' . $row, number_format($i['devol'], 2));
    $sheet->setCellValue('G' . $row, number_format($i['total'],2));
    $sheet->setCellValue('H' . $row, $i['marca']);


    }else{

             if($tipo=='n'){

                $sheet->setCellValue('A' . $row, $i['coditem']);
                $sheet->setCellValue('B' . $row, utf8_decode($i['producto']));
                $sheet->setCellValue('C' . $row, number_format($i['compras'], 2));
                $sheet->setCellValue('D' . $row, number_format($i['devol'], 2));
                $sheet->setCellValue('E' . $row, number_format(0, 2));
                $sheet->setCellValue('F' . $row, number_format(0, 2));
                $sheet->setCellValue('G' . $row, number_format($i['total'],2));
                $sheet->setCellValue('H' . $row, $i['marca']);


            }else{

                 if($tipo=='Todos'){

                    $sheet->setCellValue('A' . $row, $i['coditem']);
                    $sheet->setCellValue('B' . $row, utf8_decode($i['producto']));
                    $sheet->setCellValue('C' . $row, number_format($i['compras'], 2));
                    $sheet->setCellValue('D' . $row, number_format($i['devol'], 2));
                    $sheet->setCellValue('E' . $row, number_format($i['compras_notas'], 2));
                    $sheet->setCellValue('F' . $row, number_format($i['devol_notas'], 2));
                    $sheet->setCellValue('G' . $row, number_format($i['total'],2));
                    $sheet->setCellValue('H' . $row, $i['marca']);


                    
                    }

                }


            }


    
    /** centrarlas las celdas **/
    $spreadsheet->getActiveSheet()->getStyle('A'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('B'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('C'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('D'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('E'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('F'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('G'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('H'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="sell_in_compras_de_'.$fechai.'_al_'.$fechaf.'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');

