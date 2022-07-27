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
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("ventaskgedvporcategoria_modelo.php");

//INSTANCIAMOS EL MODELO
$ventaskg = new VentasKgEdvPorCategoria();

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$vendedor = $_GET['vendedor'];
$inst = $_GET['instancia'];

$vende = ($vendedor=='-') ? 'TODOS' : $vendedor;
$instan = ($inst=='-') ? 'TODAS' : $inst;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
foreach(range('A','D') as $columnID) {
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
$objDrawing->setCoordinates('F1');
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());

/** DATOS DEL REPORTE **/
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(25);
$spreadsheet->getActiveSheet()->getStyle('A3:F3')->getFont()->setSize(18);
$spreadsheet->getActiveSheet()->getStyle('A5:F5')->getFont()->setSize(18);
$sheet->setCellValue('A1', 'VENTAS EN KG DE EJECUTIVO EN VENTAS (X CATEGORIA)');
$sheet->setCellValue('A3', 'del: '. date(FORMAT_DATE, strtotime($fechai)));
$sheet->setCellValue('A5', 'al:  '. date(FORMAT_DATE, strtotime($fechaf)));
$sheet->setCellValue('B3', 'Instancia:  '. $instan);
$sheet->setCellValue('B5', 'Edv:  '. $vende);

$spreadsheet->getActiveSheet()->mergeCells('A1:E1');

/** TITULO DE LA TABLA **/
$sheet->setCellValue('A7', Strings::titleFromJson('categoria'))
    ->setCellValue('B7', Strings::titleFromJson('und_bultos'))
    ->setCellValue('C7', Strings::titleFromJson('und_kg'))
    ->setCellValue('D7', Strings::titleFromJson('monto_bs'));

$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);

//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A7:D7');

$datos = array(
    'fechai'    => $fechai,
    'fechaf'    => $fechaf,
    'vendedor'  => $vendedor,
    'instancia' => $inst,
);

$total_monto = $total_peso = $total_cant = 0;
$instancias_data = $ventaskg->getinstancias($datos);
$row = 8;
foreach ($instancias_data as $key => $instancia) {

    $peso = $cant = $monto = 0;
    $notas_debitos = $ventaskg->getNotaDebitos($datos, $instancia["codinst"]);
    if (ArraysHelpers::validate($notas_debitos)) {
        foreach ($notas_debitos as $i) {
            if ($i['unidad'] == 0) {
                if ($i['tipo'] == 'A') {
                    $monto += $i["monto"];
                    $peso  += $i["peso"];
                    $cant  += $i["cantidad"];
                } else {
                    $monto -= $i["monto"];
                    $peso  -= $i["peso"];
                    $cant  -= $i["cantidad"];
                }
            } else {
                if ($i['tipo'] == 'A') {
                    $monto += $i["monto"];
                    $peso  += (($i["peso"]/$i["paquetes"]) * $i["cantidad"]);
                    $cant  += ($i["cantidad"] / $i["paquetes"]);
                } else {
                    $monto -= $i["monto"];
                    $peso  -= (($i["peso"]/$i["paquetes"]) * $i["cantidad"]);
                    $cant  -= ($i["cantidad"] / $i["paquetes"]);
                }
            }
        }
    }

    $descuento = Functions::find_discount($datos['fechai'], $datos['fechaf'], $instancia["codinst"]);
    $monto -= $descuento;
    $total_cant  += $cant;
    $total_peso  += $peso;
    $total_monto += $monto;

    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A' . $row, strtoupper($instancia["descrip"]));
    $sheet->setCellValue('B' . $row, number_format($cant, 2, ",", "."));
    $sheet->setCellValue('C' . $row, number_format($peso, 2, ",", "."));
    $sheet->setCellValue('D' . $row, number_format($monto, 2, ",", "."));

    /** centrarlas las celdas **/
    $spreadsheet->getActiveSheet()->getStyle('A'.$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_JUSTIFY, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('B'.$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('C'.$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('D'.$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    # Formatos numero para excel
    $spreadsheet->getActiveSheet()->getStyle('B'.$row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
    $spreadsheet->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
    $spreadsheet->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

    $row++;
}
$spreadsheet->getActiveSheet()->getStyle('A'.$row.':D'.$row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('17a2b8');
$sheet->setCellValue('A' . $row, 'Total');
$sheet->setCellValue('B' . $row, number_format($total_cant, 2, ",", ".").' Und');
$sheet->setCellValue('C' . $row, number_format($total_peso, 2, ",", ".").' Kg');
$sheet->setCellValue('D' . $row, number_format($total_monto, 2, ",", ".").' (Bs, SIN/IVA)');

/** centrarlas las celdas **/
$spreadsheet->getActiveSheet()->getStyle('A'.$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('B'.$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('C'.$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('D'.$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="ventas_kg_edv_por_categoria_del_'.$fechai.'_al_'.$fechaf.'_intancia_'.$instan.'_vendedor_'.$vende.'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');

