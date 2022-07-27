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
require_once("motivonoventa_modelo.php");

//INSTANCIAMOS EL MODELO
$motivonoventa = new MotivoNoVenta();

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$codvend = $_GET['vendedor'];

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
$spreadsheet->getActiveSheet()->getStyle('A1:D1')->getFont()->setSize(25);
$sheet->setCellValue('A1', 'REPORTE DE MOTIVO DE NO VENTA');
$sheet->setCellValue('A3', 'del: '. date(FORMAT_DATE, strtotime($fechai)));
$sheet->setCellValue('A5', 'al:  '. date(FORMAT_DATE, strtotime($fechaf)));
$sheet->setCellValue('D3', 'EDV: '. (!hash_equals('-', $codvend) ? $codvend : 'Todos'));


$spreadsheet->getActiveSheet()->mergeCells('A1:E1');

/** TITULO DE LA TABLA **/
$sheet->setCellValue('A7', Strings::titleFromJson('fecha'))
    ->setCellValue('B7', Strings::titleFromJson('ruta'))
    ->setCellValue('C7', Strings::titleFromJson('codclie'))
    ->setCellValue('D7', Strings::titleFromJson('razon_social'))
    ->setCellValue('E7', Strings::titleFromJson('causa'));

$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);

//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A7:E7');

$data = array(
    'edv'    => $codvend,
    'fechai' => $fechai,
    'fechaf' => $fechaf
);

$query = $motivonoventa->getMotivoNoVenta($data);
$row = 8;
foreach ($query as $i) {

    $motivo = '';
    switch (intval($i["motivo"])) {
        case 1: $motivo = "Cliente Cerrado"; break;
        case 2: $motivo = "Cliente con Inventario"; break;
        case 3: $motivo = "Cliente a la espera de pedido anterior"; break;
        case 4: $motivo = "Cliente no visitado"; break;
        case 5: $motivo = "Cliente fuera de ruta"; break;
        case 6: $motivo = "Cliente con deuda y sin pago"; break;
        case 7: $motivo = "Cliente compra a la competencia"; break;
        case 8: $motivo = "Cliente considera altos los precios"; break;
    }

    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A' . $row, date(FORMAT_DATE, strtotime($i['fecha'])));
    $sheet->setCellValue('B' . $row, $i['edv']);
    $sheet->setCellValue('C' . $row, $i['codclie']);
    $sheet->setCellValue('D' . $row, $i['descrip']);
    $sheet->setCellValue('E' . $row, $motivo);

    /** centrarlas las celdas **/
    $spreadsheet->getActiveSheet()->getStyle('A'.$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('B'.$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('C'.$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('D'.$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_JUSTIFY, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('E'.$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $row++;
}
$sheet->setCellValue('B' . ($row+3), 'Total de clientes: ' . count($query));

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="motivo_no_venta_de_'.$fechai.'_al_'.$fechaf.'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');

