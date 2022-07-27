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
require_once("skunovendidos_modelo.php");

//INSTANCIAMOS EL MODELO
$sku = new Skunovendidos();

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

/** DATOS DEL REPORTE **/
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(25);
$sheet->setCellValue('A1', 'REPORTE PRODUCTOS NO VENDIDOS');
$sheet->setCellValue('A3', 'del: '. date(FORMAT_DATE, strtotime($fechai)));
$sheet->setCellValue('A5', 'al:  '. date(FORMAT_DATE, strtotime($fechaf)));

$spreadsheet->getActiveSheet()->mergeCells('A1:F1');

$row=7;
/** TITULO DE LA TABLA **/
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('numerod'))
    ->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('codvend'))
    ->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('descrip_vend'))
    ->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('codclie'))
    ->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('razon_social'))
    ->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('codigo_prod'))
    ->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('descrip_prod'))
    ->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('marca_prod'))
    ->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('tipo_empaque'))
    ->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('cantidad'))
    ->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('subtotal'))
    ->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('inv_bultos'))
    ->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('inv_paquetes'))
    ->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('fecha'));

$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);

//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A'.$row.':'.getExcelCol($i-=1).$row);

foreach(range('A',getExcelCol($i-=1)) as $columnID) {
    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
}


$data = array(
    'fechai' => $fechai,
    'fechaf' => $fechaf,
);

$query = $sku->getnovendidos($data);
$row = 8;
foreach ($query as $x) {

    $esunid = ($x["esunid"]=='1') ? 'PAQUETE' : 'BULTO';

    $i = 0;
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue(getExcelCol($i) . $row, $x['numerod']);
    $sheet->setCellValue(getExcelCol($i) . $row, $x['codvend']);
    $sheet->setCellValue(getExcelCol($i) . $row, $x['vendedor']);
    $sheet->setCellValue(getExcelCol($i) . $row, $x['codclie']);
    $sheet->setCellValue(getExcelCol($i) . $row, $x['cliente']);
    $sheet->setCellValue(getExcelCol($i) . $row, $x['coditem']);
    $sheet->setCellValue(getExcelCol($i) . $row, $x['descrip1']);
    $sheet->setCellValue(getExcelCol($i) . $row, $x['marca']);
    $sheet->setCellValue(getExcelCol($i) . $row, $esunid);
    $sheet->setCellValue(getExcelCol($i) . $row, $x['cantidad']);
    $sheet->setCellValue(getExcelCol($i) . $row, Strings::rdecimal($x['totalitem'], 2));
    $sheet->setCellValue(getExcelCol($i) . $row, Strings::rdecimal($x['bultos'], 2));
    $sheet->setCellValue(getExcelCol($i) . $row, Strings::rdecimal($x['paquetes'],2));
    $sheet->setCellValue(getExcelCol($i) . $row, date(FORMAT_DATE, strtotime($x['fechae'])));

    $i = 0;
    /** centrarlas las celdas **/
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $row++;
}

$spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(80);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="sku_no_vendidos_de_'.$fechai.'_al_'.$fechaf.'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');

