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

//LLAMAMOS AL MODELO
//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("librocompras_modelo.php");

//INSTANCIAMOS EL MODELO
$librocompra = new LibroCompra();

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

$fechai = Dates::normalize_date($_GET['fechai']).' 00:00:00';
$fechaf = Dates::normalize_date($_GET['fechaf']).' 23:59:59';

# creamos la cabecera de la tabla
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
foreach(range('B','R') as $columnID) {
    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
}

# Logo
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
$sheet->setCellValue('A2', 'Libro de Compras');
$spreadsheet->getActiveSheet()->mergeCells('A2:K2');

$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);



$sheet->setCellValue('C4', 'Desde:');
$sheet->setCellValue('D4', date(FORMAT_DATE, strtotime($fechai)));
$spreadsheet->getActiveSheet()->mergeCells('D4:E4');
$spreadsheet->getActiveSheet()->getStyle('C4')->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('D4:E4')->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$sheet->setCellValue('G4', 'Hasta:');
$sheet->setCellValue('H4', date(FORMAT_DATE, strtotime($fechaf)));
$spreadsheet->getActiveSheet()->mergeCells('H4:I4');
$spreadsheet->getActiveSheet()->getStyle('G4')->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('H4:I4')->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$row = 8;
$i = 0;
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('#'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('fecha_documento'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('rif'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('razon_social'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('tipo_documento'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('numero_comprobante_retencion'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('numerod'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('numero_control'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('tipo_transaccion'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('numerod_afectado'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('total_compras'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('compras_exentas'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('base_imponible'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('porcentaje_alicuota'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('monto_iva'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('monto_retenido'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('porcentaje_retenido'));
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('fecha_comprobante'));

//obtenemos el ultimo valor de la celda y la guardamos en una variable auxiliar
$aux = $i-1;
//se itera la cantidad de celdas almacenadas en la variable axiliar y se situan AutoSize
for($n=0; $n <= $aux; $n++)
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE),'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
$spreadsheet->getActiveSheet()->getStyle( 'A'.$row.':'.getExcelCol($aux, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'c8dcff'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),));


$datos = $librocompra->getLibroPorFecha($fechai, $fechaf);

//DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
$data = $totales = $resumen = Array();
$tcci = $mtoex = $totcom = $mtoiva = $retiva = 0;

if (is_array($datos)==true and count($datos)>0)
{
    foreach ($datos as $key => $x)
    {
        //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();

        $tcci += $x['totalcompraconiva'];
        $mtoex += $x['mtoexento'];
        $totcom += $x['totalcompra'];
        $mtoiva += $x['monto_iva'];
        $retiva += $x['retencioniva'];

        $sub_array['num']  = $key+1;
        $sub_array['fechacompra']  = date(FORMAT_DATE, strtotime($x["fechacompra"]));
        $sub_array['id3ex']        = $x["id3ex"];
        $sub_array['descripex']    = utf8_encode($x["descripex"]);
        $sub_array['tipodoc']      = $x["tipodoc"];
        $sub_array['nroretencion'] = Strings::avoidNull($x["nroretencion"]);
        $sub_array['numerodoc']    = $x["numerodoc"];
        $sub_array['nroctrol']     = Strings::avoidNull($x["nroctrol"]);
        $sub_array['tiporeg']      = $x["tiporeg"];
        $sub_array['docafectado']  = Strings::avoidNull($x["docafectado"]);
        $sub_array['totalcompraconiva'] = Strings::rdecimal($x["totalcompraconiva"], 2);
        $sub_array['mtoexento']    = Strings::rdecimal($x["mtoexento"], 2);
        $sub_array['totalcompra']  = Strings::rdecimal($x["totalcompra"], 2);
        $sub_array['alicuota_iva'] = Strings::rdecimal($x["alicuota_iva"], 0);
        $sub_array['monto_iva']    = Strings::rdecimal($x["monto_iva"], 2);
        $sub_array['retencioniva'] = Strings::rdecimal($x["retencioniva"], 2);
        $sub_array['porctreten']   = Strings::rdecimal($x["porctreten"], 0);
        $sub_array['fecharetencion'] = Strings::avoidNull($x["fecharetencion"]);

        $data[] = $sub_array;
    }
}

$totales = array(
    "tcci"   => Strings::rdecimal($tcci, 2),
    "mtoex"  => Strings::rdecimal($mtoex, 2),
    "totcom" => Strings::rdecimal($totcom, 2),
    "mtoiva" => Strings::rdecimal($mtoiva, 2),
    "retiva" => Strings::rdecimal($retiva, 2)
);

$resumen = array(
    array(
        "descripcion"    => 'Total Compras Exentas y/o sin derecho a crédito Fiscal',
        "base_imponible" => Strings::rdecimal($mtoex, 2),
        "credito_fiscal" => Strings::rdecimal(0, 2),
        "isBold" => false, "isColored" => false,
    ),
    array(
        "descripcion"    => 'Total Compras Importación Afectas solo Alícuota General',
        "base_imponible" => Strings::rdecimal(0, 2),
        "credito_fiscal" => Strings::rdecimal(0, 2),
        "isBold" => false, "isColored" => false,
    ),
    array(
        "descripcion"    => 'Total Compras Importación Afectas en Alícuota General + Adicional',
        "base_imponible" => Strings::rdecimal(0, 2),
        "credito_fiscal" => Strings::rdecimal(0, 2),
        "isBold" => false, "isColored" => false,
    ),
    array(
        "descripcion"    => 'Total Compras Importación Afectas en Alícuota Reducida',
        "base_imponible" => Strings::rdecimal(0, 2),
        "credito_fiscal" => Strings::rdecimal(0, 2),
        "isBold" => false, "isColored" => false,
    ),
    array(
        "descripcion"    => 'Total Compras Internas Afectas solo Alícuota General (16%): ',
        "base_imponible" => Strings::rdecimal($totcom, 2),
        "credito_fiscal" => Strings::rdecimal($mtoiva, 2),
        "isBold" => false, "isColored" => false,
    ),
    array(
        "descripcion"    => 'Total Compras Internas Afectas solo Alícuota General + Adicional',
        "base_imponible" => Strings::rdecimal(0, 2),
        "credito_fiscal" => Strings::rdecimal(0, 2),
        "isBold" => false, "isColored" => false,
    ),
    array(
        "descripcion"    => 'Total Compras Internas Afectas solo Alícuota Reducida',
        "base_imponible" => Strings::rdecimal(0, 2),
        "credito_fiscal" => Strings::rdecimal(0, 2),
        "isBold" => false, "isColored" => false,
    ),
    array(

        "descripcion"    => 'Total Compras y créditos fiscales del período',
        "base_imponible" => Strings::rdecimal(($totcom+$mtoex), 2),
        "credito_fiscal" => Strings::rdecimal($mtoiva, 2),
        "isBold" => true, "isColored" => true,
    ),
    array(
        "descripcion"    => 'Créditos Fiscales producto de la aplicación del porcentaje de la prorrata',
        "base_imponible" => Strings::rdecimal(0, 2),
        "credito_fiscal" => Strings::rdecimal(0, 2),
        "isBold" => false, "isColored" => false,
    ),
    array(
        "descripcion"    => 'Excedente de Crédito Fiscal del Periodo Anterior',
        "base_imponible" => Strings::rdecimal(0, 2),
        "credito_fiscal" => Strings::rdecimal(0, 2),
        "isBold" => false, "isColored" => false,
    ),
    array(
        "descripcion"    => 'Ajustes a los créditos fiscales de periodos anteriores',
        "base_imponible" => Strings::rdecimal(0, 2),
        "credito_fiscal" => Strings::rdecimal(0, 2),
        "isBold" => false, "isColored" => false,
    ),
    array(
        "descripcion"    => 'Compras no gravadas y/o sin derecho a credito fiscal',
        "base_imponible" => Strings::rdecimal(0, 2),
        "credito_fiscal" => Strings::rdecimal(0, 2),
        "isBold" => true, "isColored" => true,
    ),
);



$row = 9;
if (is_array($data)==true and count($data)>0) {
    foreach ($data as $x) {
        $i = 0;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue(getExcelCol($i) . $row, $x['num']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['fechacompra']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['id3ex']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['descripex']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['tipodoc']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['nroretencion']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['numerodoc']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['nroctrol']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['tiporeg']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['docafectado']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['totalcompraconiva']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['mtoexento']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['totalcompra']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['alicuota_iva'] . '%');
        $sheet->setCellValue(getExcelCol($i) . $row, $x['monto_iva']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['retencioniva']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['porctreten'] . '%');
        $sheet->setCellValue(getExcelCol($i) . $row, $x['fecharetencion']);

        $i = 0;
        /** centrarlas las celdas **/
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_JUSTIFY, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

        $row++;
    }
}

$i = 0;
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue(getExcelCol($i) . $row,'Totales');
$sheet->setCellValue(getExcelCol($i+=9) . $row, $totales['tcci']);
$sheet->setCellValue(getExcelCol($i) . $row, $totales['mtoex']);
$sheet->setCellValue(getExcelCol($i) . $row, $totales['totcom']);
$sheet->setCellValue(getExcelCol($i) . $row, '');
$sheet->setCellValue(getExcelCol($i) . $row, $totales['mtoiva']);
$sheet->setCellValue(getExcelCol($i) . $row, $totales['retiva']);
$spreadsheet->getActiveSheet()->mergeCells('A'.$row.':J'.$row);
$i = 0;
/** centrarlas las celdas **/
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i+=9) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$row += 5;
$i = 0;
$sheet->setCellValue(getExcelCol($i).$row, strtoupper( Strings::titleFromJson('resumen_credito_fiscal') ));
$sheet->setCellValue(getExcelCol($i+=3).$row, strtoupper( Strings::titleFromJson('base_imponible') ));
$sheet->setCellValue(getExcelCol($i).$row, strtoupper( Strings::titleFromJson('credito_fiscal') ));
$spreadsheet->getActiveSheet()->mergeCells('A'.$row.':D'.$row);

//obtenemos el ultimo valor de la celda y la guardamos en una variable auxiliar
$aux = $i-1;
//se itera la cantidad de celdas almacenadas en la variable axiliar y se situan AutoSize
for($n=0; $n <= $aux; $n++)
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE),'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
$spreadsheet->getActiveSheet()->getStyle( 'A'.$row.':'.getExcelCol($aux, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'c8dcff'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),));

$row += 1;
if (is_array($resumen)==true and count($resumen)>0) {
    foreach ($resumen as $x) {
        $i = 0;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue(getExcelCol($i) . $row, $x['descripcion']);
        $sheet->setCellValue(getExcelCol($i+=3) . $row, $x['base_imponible']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['credito_fiscal']);
        $spreadsheet->getActiveSheet()->mergeCells('A'.$row.':D'.$row);

        $i = 0;
        /** centrarlas las celdas **/
        if ($x['isBold'] and $x['isColored']) {
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('bold'  => true, 'color' => array('rgb' => '000000')),'fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_JUSTIFY, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i+=3) . $row)->applyFromArray(array('font' => array('bold'  => true, 'color' => array('rgb' => '000000')),'fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('bold'  => true, 'color' => array('rgb' => '000000')),'fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        } else {
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_JUSTIFY, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i+=3) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        }

        $row++;
    }
}



$spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(80);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="librocompras_de_'.date(FORMAT_DATE, $fechai).'_al_'.date(FORMAT_DATE, $fechaf).'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');