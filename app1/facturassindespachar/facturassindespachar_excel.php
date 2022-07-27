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
require_once("facturassindespachar_modelo.php");

//INSTANCIAMOS EL MODELO
$factsindes = new FacturaSinDes();

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
$convend = $_GET['vendedores'];
$tipo = $_GET['tipo'];
$check = hash_equals("true", $_GET['check']);
$hoy = date(FORMAT_DATE);


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
$objDrawing->setCoordinates('I1');
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());

/** DATOS DEL REPORTE **/
switch ($GLOBALS['check']) {
    case true:
        $titulo = 'REPORTE DE FACTURAS DESPACHADAS DEL ' . $fechai . ' AL ' . $fechaf;
        break;
    case false:
        $titulo = 'REPORTE DE FACTURAS SIN DESPACHAR DEL ' . $fechai . ' AL ' . $fechaf;
        break;
}
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(25);
$sheet->setCellValue('A1', $titulo);
//$sheet->setCellValue('A3', 'del: '. date(FORMAT_DATE, strtotime($fechai)));
//$sheet->setCellValue('A5', 'al:  '. date(FORMAT_DATE, strtotime($fechaf)));


$spreadsheet->getActiveSheet()->mergeCells('A1:H1');

/** TITULO DE LA TABLA **/
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('numerod'));
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('fecha_emision'));
if($check) {
    $sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('fecha_despacho'));
    $sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('dias_transcurridos'));
}
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('codigo'));
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('cliente'));
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('dias_transcurridos_hoy'));
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('cantidad_bultos'));
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('cantidad_paquetes'));
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('monto'));
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('descrip_vend'));
if($check) {
    $sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('tiempo_prom_estimado'));
    $sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('porcentaje_oportunidad'));
}

//obtenemos el ultimo valor de la celda y la guardamos en una variable auxiliar
$aux = $i-1;
//se itera la cantidad de celdas almacenadas en la variable axiliar y se situan AutoSize
for($n=0; $n <= $aux; $n++){ $spreadsheet->getActiveSheet()->getColumnDimension(getExcelCol($n))->setAutoSize(true); }
//pinta la cabecera de amarillo
$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);

//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A7:'.getExcelCol($aux).'7');


//a partir de aqui comienza a llenarse la tabla
$query = $factsindes->getFacturas($tipo, $fechai, $fechaf, $convend, $check);
$num = count($query);
$suma_bulto = 0;
$suma_paq = 0;
$suma_monto = 0;
$porcent = 0;
$row = 8;
foreach ($query as $x) {
    $i = 0;

    $dias = $factsindes->dias_transcurridos( $x["FechaE"], $fechaf);

    $dias=$dias+1;

          /*  if($dias != 0){
                $dias=$dias+1;
            }else{
                
            }*/

    if($check) {
        $calcula = 0;
        if (round(Dates::daysEnterDates(date(FORMAT_DATE, strtotime($x["FechaE"])),date(FORMAT_DATE, strtotime($x["fechad"])))) != 0)
            $calcula = (2 / round(Dates::daysEnterDates(date(FORMAT_DATE, strtotime($x["FechaE"])),date(FORMAT_DATE, strtotime($x["fechad"])))))*100;

        if ($calcula > 100)
            $calcula = 100;

        $porcent += $calcula;
    }

    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue(getExcelCol($i, true) . $row, $x['NumeroD']);
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $sheet->setCellValue(getExcelCol($i, true) . $row, date(FORMAT_DATE, strtotime($x["FechaE"])));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    if ($check) {
        $sheet->setCellValue(getExcelCol($i, true) . $row, date(FORMAT_DATE, strtotime($x["fechad"])));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

        $sheet->setCellValue(getExcelCol($i, true) . $row, round(Dates::daysEnterDates(date(FORMAT_DATE, strtotime($x["FechaE"])),date(FORMAT_DATE, strtotime($x["fechad"])))));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    }
    $sheet->setCellValue(getExcelCol($i, true) . $row, $x['CodClie']);
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $sheet->setCellValue(getExcelCol($i, true) . $row, utf8_decode($x['Descrip']));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

   /* $sheet->setCellValue(getExcelCol($i, true) . $row, round(Dates::daysEnterDates(date(FORMAT_DATE, strtotime($x["FechaE"])), $hoy)));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
*/

    $sheet->setCellValue(getExcelCol($i, true) . $row, $dias);
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));


    $sheet->setCellValue(getExcelCol($i, true) . $row, Strings::rdecimal($x["Bult"], 0));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $sheet->setCellValue(getExcelCol($i, true) . $row, Strings::rdecimal($x["Paq"], 0));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $sheet->setCellValue(getExcelCol($i, true) . $row, Strings::rdecimal($x["Monto"], 1)); $suma_monto += $x["Monto"];
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $sheet->setCellValue(getExcelCol($i, true) . $row, $x['CodVend']);
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    if ($check) {
        $sheet->setCellValue(getExcelCol($i, true) . $row, 2);
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

        $sheet->setCellValue(getExcelCol($i, true) . $row, Strings::rdecimal($calcula, 1) . "%");
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    }

    $row++;
}
$i = 0;
// por ultimo se escribe los totales
$sheet->setCellValue(getExcelCol($i) . ($row + 3), '');
$sheet->setCellValue(getExcelCol($i) . ($row + 3), '');
if ($check) {
    $sheet->setCellValue(getExcelCol($i) . ($row + 3), '');
    $sheet->setCellValue(getExcelCol($i) . ($row + 3), '');
}
$sheet->setCellValue(getExcelCol($i) . ($row + 3), '');
$sheet->setCellValue(getExcelCol($i) . ($row + 3), 'Total de Documentos:  ' . $num);
$sheet->setCellValue(getExcelCol($i) . ($row + 3), '');
$sheet->setCellValue(getExcelCol($i) . ($row + 3), '');
$sheet->setCellValue(getExcelCol($i) . ($row + 3), '');
$sheet->setCellValue(getExcelCol($i) . ($row + 3), 'Monto Total: ' . Strings::rdecimal($suma_monto, 2));
$sheet->setCellValue(getExcelCol($i) . ($row + 3), '');
if ($check) {
    $sheet->setCellValue(getExcelCol($i) . ($row + 3), '');
    $sheet->setCellValue(getExcelCol($i) . ($row + 3), '% Oportunidad Total: ' . Strings::rdecimal(($porcent / count($query)), 2) . ' %');
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Listado_de_precios_e_inventario_' . date('d/m/Y') . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');
