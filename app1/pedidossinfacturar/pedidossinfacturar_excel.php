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
require_once("pedidossinfacturar_modelo.php");

//INSTANCIAMOS EL MODELO
$pedsinfacturar = new Pedidossinfacturar();

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
$marca = $_GET['marca'];

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
$objDrawing->setCoordinates('H1');
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());

/** DATOS DEL REPORTE **/
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(25);
$sheet->setCellValue('A1', Empresa::getName());
$spreadsheet->getActiveSheet()->mergeCells('A1:G1');
$spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray(array('font' => array('bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_JUSTIFY, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$spreadsheet->getActiveSheet()->getStyle('A2:F2')->getFont()->setSize(18);
$sheet->setCellValue('A2', 'Relacion de pedidos sin Facturar');
$spreadsheet->getActiveSheet()->mergeCells('A2:K2');

$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);



$sheet->setCellValue('C4', 'Desde:');
$sheet->setCellValue('D4', date(FORMAT_DATE, strtotime($_GET['fechai'])));
$spreadsheet->getActiveSheet()->mergeCells('D4:E4');
$spreadsheet->getActiveSheet()->getStyle('C4')->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('D4:E4')->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$sheet->setCellValue('G4', 'Hasta:');
$sheet->setCellValue('H4', date(FORMAT_DATE, strtotime($_GET['fechaf'])));
$spreadsheet->getActiveSheet()->mergeCells('H4:I4');
$spreadsheet->getActiveSheet()->getStyle('G4')->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('H4:I4')->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

/** TITULO DE LA TABLA **/
$row = 8;
$i = 0;
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('#'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('fecha'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('marca_prod'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('codigo_prod'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('descrip_prod'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('cliente'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('unidad'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('cantidad'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('total'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('ruta'));


//obtenemos el ultimo valor de la celda y la guardamos en una variable auxiliar
$aux = $i-1;
//se itera la cantidad de celdas almacenadas en la variable axiliar y se situan AutoSize
for($n=0; $n <= $aux; $n++)
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE),'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
$spreadsheet->getActiveSheet()->getStyle( 'A'.$row.':'.getExcelCol($aux, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'c8dcff'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),));

foreach(range('A',getExcelCol($aux, true)) as $columnID) {
    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
}


$data = array(
    'fechai' => $fechai,
    'fechaf' => $fechaf,
    'marca'  => $marca
);

$query = $pedsinfacturar->getPedidos($data);
$row = 9;
foreach ($query as $key => $x) {
    $i = 0;
    $sheet = $spreadsheet->getActiveSheet();

    $unidad = ($x['unidad'] == 1)
        ? Strings::titleFromJson('paquete')
        : Strings::titleFromJson('bulto');

    $sheet->setCellValue(getExcelCol($i) . $row, $key+1);
    $sheet->setCellValue(getExcelCol($i) . $row, date(FORMAT_DATE, strtotime($x["fechae"])));
    $sheet->setCellValue(getExcelCol($i) . $row, $x["marca"]);
    $sheet->setCellValue(getExcelCol($i) . $row, $x["coditem"]);
    $sheet->setCellValue(getExcelCol($i) . $row, $x["producto"]);
    $sheet->setCellValue(getExcelCol($i) . $row, $x["cliente"]);
    $sheet->setCellValue(getExcelCol($i) . $row, $unidad);
    $sheet->setCellValue(getExcelCol($i) . $row, Strings::rdecimal($x["cantidad"],0));
    $sheet->setCellValue(getExcelCol($i) . $row, Strings::rdecimal($x["total"],2));
    $sheet->setCellValue(getExcelCol($i) . $row, $x["ruta"]);

    /** centrarlas las celdas **/
    $i = 0;
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="pedidos_sin_facturar_desde_'.$fechai.'_al_'.$fechaf.'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');

